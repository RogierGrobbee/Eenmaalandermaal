<script type="text/javascript" src="js/countdown.js"></script>
<?php
//input rubriekId wordt opgehaald
if ($_GET != null) {
    if (is_numeric($_GET['rubriek'])) {
        $inputRubriekId = $_GET['rubriek'];
    } else {
        $inputRubriekId = 0;
    }
} else {
    $inputRubriekId = 0;
}

include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
$huidigeRubriek = null;

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
    echo '<h1>Welkom</h1>';
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

    <div class="col-sm-9">
        <?php
        if ($huidigeRubriek != null) {
            include 'partial files\subrubrieken.php';
            loadSubrubrieken($rubriekArray, $huidigeRubriek);
        }
        ?>
        <?php
        loadVeilingItems($inputRubriekId);
        ?>


    </div>

<?php include_once('partial files\footer.php') ?>