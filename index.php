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
//rubrieken worden opgehaald uit db
$db = new PDO ("sqlsrv:Server=LAPTOP-AOSH53E4\SQLEXPRESS;Database=eenmaalandermaal;ConnectionPooling=0", "sa", "Kanarie//////////");
$query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');
$rubriekArray = array();
$huidigeRubriek = null;
//lijst wordt gevult met alle rubrieken
while ($rubriek = $query->fetch(PDO::FETCH_OBJ)) {
    array_push($rubriekArray, $rubriek);
}

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}
?>

<?php include_once('partial files\header.php') ?>


<?php
//De koptekst wordt gezet, als er geen rubriek is geselecteerd id het Welkom
if ($huidigeRubriek != null) {
    echo '<h1>' . $huidigeRubriek->rubrieknaam . '</h1>';
} else {
    echo '<h1>Welkom</h1>';
}
?>

<?php
include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, $huidigeRubriek);
?>


    <div class="col-sm-9">
        <?php
        if ($huidigeRubriek != null) {
            include 'subrubrieken.php';
            loadSubrubrieken($rubriekArray, $huidigeRubriek);
        }
        ?>
        <?php
        include 'veilingsTabs.php';
        loadVeilingItems($inputRubriekId);
        ?>


    </div>

<?php include_once('partial files\footer.php') ?>