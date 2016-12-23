<?php
require_once('partial files/models/rubriek.php');

function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

function echoPagination($totalItems, $itemsPerPage, $currentPageNumber, $searchTerm) {
    $nPages = ceil($totalItems / $itemsPerPage);
    echo '<div class="row">
            <div class="col-sm-12">
            ';
    if ($currentPageNumber > 1) {
        echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchTerm . "&page=" . ($currentPageNumber - 1) . "'\">Previous</button>");
    }
    if ($nPages > 9) {
        if ($currentPageNumber < 6) {
            for ($i = 1; $i < 10; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        } else if ($currentPageNumber > ($nPages - 5)) {
            echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
        } else {
            echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        }

    } else {
        for ($i = 1; $i < $nPages + 1; $i++) {
            echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
        }
    }
    if ($currentPageNumber < $nPages) {
        echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchTerm . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
    }
    echo '</div></div>';
}

function echoSearchPageNumber($pageNumber, $currentPageNumber, $search)
{
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./zoeken.php?search=' . $search . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
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

//require('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();

require('partial files\header.php');

if(isset($search)){
    echo "<h1>Zoeken op $search</h1>";
}

require('partial files\sidebar.php');
__loadSidebar(null);

?>

<?php
    loadVeilingItemsSearch($search, $page, $filter);
?>

<?php require('partial files\footer.php') ?>