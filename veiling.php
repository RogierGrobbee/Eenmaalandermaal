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

include_once('partial files\databaseconnection.php');

$voorwerp = getVoorwerp($voorwerpnummer);

/*$query = $db->query("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer=$voorwerpnummer");
$voorwerpinrubriek = $query->fetch(PDO::FETCH_OBJ);
$inputRubriekId = $voorwerpinrubriek->rubriekoplaagsteniveau;*/

$inputRubriekId = getVoorwerpRubriek($voorwerpnummer);

include_once('partial files\rubrieken.php');
include_once('partial files\header.php');
include_once('partial files\navigatie.php');

echo "<h1>$voorwerp->titel</h1>";

include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, $navigatieArray[count($navigatieArray) - 1]);

$list = loadBestanden($voorwerp->voorwerpnummer);
$image = $list[0];
?>
    <div class="row">
        <?php echo '<img class="bigpicture" src="./bestanden/'.$image.'" alt="geveilde voorwerp">' ?>
        <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7 detail">
            <div class="prijstijd">
                <div class="veilingprijs">
                    <?php echo "€$voorwerp->startprijs" ?>
                </div>
                <div class="veilingtijd">
                    <span data-tijd="<?php echo $voorwerp->looptijdeindeveiling ?>" class="tijd"></span>
                </div>
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
<?php include_once('partial files\footer.php')?>