<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();

require('partial files\header.php');

echo '<h1>Nieuwste veilingen</h1>';

require('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

?>

<?php
    loadVeilingItemsSearch($search);
?>

<?php require('partial files\footer.php') ?>