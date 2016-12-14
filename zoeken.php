<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
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

require('partial files\header.php');

if(isset($search)){
    echo "<h1>Zoeken op $search</h1>";
}

require('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

?>

<?php
    loadVeilingItemsSearch($search, $page, $filter);
?>

<?php require('partial files\footer.php') ?>