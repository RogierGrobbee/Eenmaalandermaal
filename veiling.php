<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}
require('partial files\header.php');

if (isset($_GET['voorwerpnummer'])) {
    if (is_numeric($_GET['voorwerpnummer'])) {
        $voorwerpnummer = $_GET['voorwerpnummer'];
    } else {
        $voorwerpnummer = 0;
    }
} else {
    exit;
}

require('partial files\databaseconnection.php');

$voorwerp = getVoorwerp($voorwerpnummer);

if(isset($_POST['bod'])){
    if(is_numeric($_POST['bod'])){
        if(!insertNewBod($voorwerp, $_POST['bod'], 'Leon')){
            $error = "<div class='alert alert-danger'>
                        <strong>Dit bod is niet geldig!</strong>
                      </div>";
        }
    }
}

$biedingen = getVoorwerpBiedingen($voorwerpnummer);

$inputRubriekId = getVoorwerpRubriek($voorwerpnummer);

require('partial files\rubrieken.php');
require('partial files\navigatie.php');

echo "<h1>$voorwerp->titel</h1>";

require('partial files\sidebar.php');
loadSidebar($rubriekArray, $navigatieArray[count($navigatieArray) - 1]);

$list = loadBestanden($voorwerp->voorwerpnummer);
$image = $list[0];

if($biedingen == null){
    $minimalePrijs = $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs);
    $minimalePrijs = number_format((float)$minimalePrijs, 2, '.', ',');
}
else {
    $minimalePrijs = $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag);
    $minimalePrijs = number_format((float)$minimalePrijs, 2, '.', ',');
}
?>
    <div class="row">
        <?php
            if(isset($error)){
                echo $error;
            }
        ?>
        <?php echo '<img class="bigpicture" src="pics/'.$image.'" alt="geveilde voorwerp">' ?>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7">
            <div class="boddetail">
                <div class="veilingtijd">
                    <span data-tijd="<?php echo $voorwerp->looptijdeindeveiling ?>" class="tijd"></span>
                </div>

                <div class="bieden">
                    <form action="veiling.php?voorwerpnummer=<?php echo $voorwerp->voorwerpnummer ?>" method="POST">
                        <label for="bied-bar">€</label>
                        <input type="number" class="bied-bar" name="bod" id="bied-bar" min=<?php
                        echo '"'.$minimalePrijs . '" max="10000000" value="' . $minimalePrijs . '" step="0.01" required>
                        <input type="hidden" name="voorwerpnummer" value="'.$voorwerp->voorwerpnummer.'">';
                        ?>
                        <button type="submit" class="btn-bied">Bied</button>
                    </form>
                </div>

                <?php
                if($biedingen == null){
                    echo "<div class='highest-bod'>Er zijn nog geen biedingen! Bied snel!</div>";
                }
                else {
                    for($i=0; $i<count($biedingen); $i++){
                        if($i == 0){
                            echo "<div class='highest-bod'>";
                        }
                        else {
                            echo "<div class='bod'>";
                        }

                        if($biedingen[$i]->bodbedrag < 1){
                            $bod = "0" . $biedingen[$i]->bodbedrag;
                        }
                        else{
                            $bod = $biedingen[$i]->bodbedrag;
                        }

                        echo "<div class='gebruikersnaam'>".$biedingen[$i]->gebruikersnaam."</div>
                        <div class='bodprijs'>€".$bod."</div><br></div>";
                    }
                }

                if($voorwerp->startprijs < 1){
                    $prijs = "0" . $voorwerp->startprijs;
                }
                else{
                    $prijs = $voorwerp->startprijs;
                }

                echo "<div class='bod'><div class='gebruikersnaam'>Startprijs</div>
                        <div class='bodprijs'>€".$prijs."</div><br></div>";?>
            </div>

            <p><?php echo "Deze voorwerp is aangeboden door $voorwerp->verkoper ($voorwerp->plaatsnaam, $voorwerp->land)"?></p>

            <h4>Beschrijving</h4>
            <p><?php echo strip_html_tags($voorwerp->beschrijving) ?></p>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5 extrainfo">
            <h4>Betalingswijze- en instructie</h4>
            <p>
                <?php echo "$voorwerp->betalingswijze, $voorwerp->betalingsinstructie" ?>
            </p>

            <h4>Verzendkosten- en instructie</h4>
            <p>
                <?php
                    if($voorwerp->verzendkosten == 0) {
                        echo "Geen verzendkosten, $voorwerp->verzendinstructies";
                    }
                    else{
                        echo "€$voorwerp->verzendkosten, $voorwerp->verzendinstructies";
                    }
                ?>
        </div>
    </div>
<div class="row">
<?php
for($i = 1; $i < 4; $i++) {
    if(!empty($list[$i])){
        echo '<div class="sm-3">
            <img class="smallpicture" src="./pics/' . $list[$i] . '" alt="plaatje voorwerp">
        </div>';
    }
}
?>
</div>
<?php require('partial files\footer.php')?>