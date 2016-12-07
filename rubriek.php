<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}
?>
<?php
//input rubriekId wordt opgehaald
$inputRubriekId = null;
if (isset($_GET['rubriek'])) {
    if (is_numeric($_GET['rubriek'])) {
        $inputRubriekId = $_GET['rubriek'];
    }
}

include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
$huidigeRubriek = null;

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}

include_once('partial files\header.php');
include_once('partial files\navigatie.php');

//De koptekst wordt gezet, als er geen rubriek is geselecteerd id het Welkom
if ($huidigeRubriek != null) {
    echo '<h1>' . $huidigeRubriek->rubrieknaam . '</h1>';
} else {
    echo '<h1>Nieuwste veilingen</h1>';
}

//sidebar maken op basis van rubrieken
include_once('partial files\sidebar.php');
if(isset($navigatieArray)) {
    loadSidebar($rubriekArray, $navigatieArray[count($navigatieArray) - 1]);
}
else{
    loadSidebar($rubriekArray, $huidigeRubriek);
}
?>

    <?php
    if (!is_null($huidigeRubriek)) {
        include 'partial files\subrubrieken.php';
        loadSubrubrieken($rubriekArray, $huidigeRubriek);
    }
    ?>
    <?php
    loadVeilingItems($inputRubriekId);
    ?>

<?php include_once('partial files\footer.php') ?>