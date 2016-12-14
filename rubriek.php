<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

//input rubriekId wordt opgehaald
$inputRubriekId = null;
if (isset($_GET['rubriek'])) {
    if (is_numeric($_GET['rubriek'])) {
        $inputRubriekId = $_GET['rubriek'];
    }
}
$filter = "looptijdeindeveilingASC";
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}
$page = 1;
if (isset($_GET['page'])) {
    if (is_numeric($_GET['page'])) {
        $page = $_GET['page'];
    }
}

require('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
$huidigeRubriek = null;

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}

require('partial files\header.php');
require('partial files\navigatie.php');

//De koptekst wordt gezet, als er geen rubriek is geselecteerd id het Welkom
if ($huidigeRubriek != null) {
    echo '<h1>' . $huidigeRubriek->rubrieknaam . '</h1>';
} else {
    echo '<h1>Nieuwste veilingen</h1>';
}

//sidebar maken op basis van rubrieken
require('partial files\sidebar.php');
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
    loadVeilingItems($inputRubriekId, $page, $filter);
    ?>

<?php require('partial files\footer.php') ?>