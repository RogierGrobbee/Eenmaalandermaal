<?php
//input rubriekId wordt opgehaald
if (!empty($_GET['rubriek'])) {
    if (is_numeric($_GET['rubriek'])) {
        $inputRubriekId = $_GET['rubriek'];
    } else {
        $inputRubriekId = 0;
    }
} else {
    $inputRubriekId = 0;
}

include_once('partial files\rubrieken.php');
include_once('partial files\header.php');

//De koptekst wordt gezet, als er geen rubriek is geselecteerd id het Welkom
if ($huidigeRubriek != null) {
    echo '<h1>' . $huidigeRubriek->rubrieknaam . '</h1>';
} else {
    echo '<h1>Welkom</h1>';
}

//sidebar maken op basis van rubrieken
include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, $huidigeRubriek);
?>


    <div class="col-sm-9">
        <?php
        if ($huidigeRubriek != null) {
            include 'partial files\subrubrieken.php';
            loadSubrubrieken($rubriekArray, $huidigeRubriek);
        }
        ?>
        <?php
        include 'partial files\veilingsTabs.php';
        loadVeilingItems($inputRubriekId);
        ?>


    </div>

<?php include_once('partial files\footer.php') ?>