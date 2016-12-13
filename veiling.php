<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

if (!empty($_GET['voorwerpnummer'])) {
    if (is_numeric($_GET['voorwerpnummer'])) {
        $voorwerpnummer = $_GET['voorwerpnummer'];
    } else {
        $voorwerpnummer = 0;
    }
} else {
    $voorwerpnummer = 1;
}

require('partial files\databaseconnection.php');

$voorwerp = getVoorwerp($voorwerpnummer);
$biedingen = getVoorwerpBiedingen($voorwerpnummer);

$inputRubriekId = getVoorwerpRubriek($voorwerpnummer);

require('partial files\rubrieken.php');
require('partial files\header.php');
require('partial files\navigatie.php');

echo "<h1>$voorwerp->titel</h1>";

require('partial files\sidebar.php');
loadSidebar($rubriekArray, $navigatieArray[count($navigatieArray) - 1]);

$list = loadBestanden($voorwerp->voorwerpnummer);
$image = $list[0];

function calculateIncrease($prijs){
   switch(true){
       case $prijs >= 5000:
           return 50;
           break;
       case $prijs >= 1000:
           return 10;
           break;
       case $prijs >= 500:
           return 5;
           break;
       case $prijs >= 50:
           return 1;
           break;
       default:
           return 0.50;
       break;
   }
}
?>
    <div class="row">
        <?php echo '<img class="bigpicture" src="pics/'.$image.'" alt="geveilde voorwerp">' ?>
        <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7">
            <div class="boddetail">
                <div class="veilingtijd">
                    <span data-tijd="<?php echo $voorwerp->looptijdeindeveiling ?>" class="tijd"></span>
                </div>

                <form action='' method='POST'>
                    <div class="bieden">
                        <label for="bied-bar">€</label>
                        <input type="text" class="bied-bar" name="bod" id="bied-bar" value="<?php
                        if($biedingen == null){
                            $minimalePrijs = $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs);
                            echo $minimalePrijs;
                        }
                        else {
                            $minimalePrijs = $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag);
                            echo number_format((float)$minimalePrijs, 2, '.', '');
                        }
                        ?>" required><button type="submit" class="btn-bied">Bied</button>
                    </div>
                </form>

                <?php
                if($biedingen == null){
                    echo "<div class='bod'>Er zijn nog geen biedingen! Bied snel!</div>";
                }
                else {
                    for($i=0; $i<count($biedingen); $i++){
                        if($i == 0){
                            echo "<div class='highest-bod'>";
                        }
                        else {
                            echo "<div class='bod'>";
                        }

                        echo "<div class='left'>".$biedingen[$i]->gebruikersnaam."</div>
                        <div class='right'>".$biedingen[$i]->bodbedrag."</div><br></div>";
                    }
                }?>
            </div>

            <p><?php echo "$voorwerp->verkoper ($voorwerp->plaatsnaam, $voorwerp->land)"?></p>

            <h4>Beschrijving</h4>
            <p><?php echo $voorwerp->beschrijving ?></p>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5 extrainfo">
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
<?php
echo '<div class="row">';
foreach ($list as $k => $smallImage) {
    if ($smallImage != $image) {
        echo '<div class="sm-3">
                <img class="smallpicture" src="./bestanden/'.$smallImage.'" alt="geveilde voorwerp1">
            </div>';
    }
}
echo '</div>';
?>
<?php require('partial files\footer.php')?>