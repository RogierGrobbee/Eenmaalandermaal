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
        <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7 detail">
            <div class="veilingtijd">
                <span data-tijd="<?php echo $voorwerp->looptijdeindeveiling ?>" class="tijd"></span>
            </div>

            <form action='' method='GET'>
                <div class="search">
                    <input type="text" class="search-bar" name="search" value="<?php
                    echo $biedingen[0]->bodbedrag + calculateIncrease($biedingen->bodbedrag);
                    ?>" required>
                    <button type="submit" class="btn-bied">Bied</button>
                </div>
            </form>


            <div class="veilingprijs">
                <?php echo "€". $biedingen[0]->bodbedrag ?>
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