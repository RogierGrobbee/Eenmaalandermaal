<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
include_once('partial files\models\rubriek.php');
?>

    <h1>Test</h1>

<?php include_once('partial files\sidebar.php');

function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
    echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
    echo '<script type="text/javascript" src="js/veilingToevoegen.js"></script>';
}

loadRubriekenSidebar(null);
$rootRubriek = getRubriekenBySuperrubriek();

foreach ($rootRubriek as $row) {
    $id = $row->rubrieknummer;
    $idString = "#" . $row->rubrieknummer;

    echo "<a href='#" . $id . "' data-id='" . $id . "' data-toggle='collapse' onclick='loadSubrubrieken(this)'>" . $row->rubrieknaam . "</a>";
    echo "<br>";
    echo "<div id='" . $id . "' class='collapse margin-left'>";
    echo '</div>';
}
include_once('partial files\footer.php') ?>