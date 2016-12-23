<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require_once('partial files/models/voorwerp.php');

function __echoSearchPageNumber($pageNumber, $currentPageNumber, $search)
{
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./zoeken.php?search=' . $search . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}

function echoFilterBox($param, $filter, $isRubriek)
{
    if ($isRubriek) {
        echo '<select onchange="rubriekFilterSelect(this.value, ' . $param . ')">';
    } else {
        echo '<select onchange="searchFilterSelect(this.value, \'' . $param . '\')">';
    }

    echo '<option value="looptijdeindeveilingASC"'; if ($filter == "looptijdeindeveilingASC") { echo 'selected'; } echo'>Tijd: eerst afglopen</option>';

    echo '<option value="looptijdbeginveilingDESC"'; if ($filter == "looptijdbeginveilingDESC") { echo 'selected'; } echo'>Tijd: nieuwst verschenen</option>';

    echo '<option value="laagstebod"'; if ($filter == "laagstebod") { echo 'selected'; } echo'>Prijs: laagst</option>';

    echo '<option value="hoogstebod"'; if ($filter == "hoogstebod") { echo 'selected'; } echo'>Prijs: hoogst</option>';

    echo '<option value="mostpopular"'; if ($filter == "mostpopular") { echo 'selected'; } echo'>Populairste veilingen</option>';

    echo '</select>';
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
                __echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            __echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        } else if ($currentPageNumber > ($nPages - 5)) {
            __echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                __echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
        } else {
            __echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                __echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            __echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        }

    } else {
        for ($i = 1; $i < $nPages + 1; $i++) {
            __echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
        }
    }
    if ($currentPageNumber < $nPages) {
        echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchTerm . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
    }
    echo '</div></div>';
}

function loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) {
    global $itemsPerPage;
    $itemsPerPage = 10;
    $searchCount = substr_count($searchQuery, ' ');
    $searchQuery .= " ";

    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);

    $totalItems = countVrwrpenBySTerm ($searchQuery, $searchCount);

    $voorwerpen = getVrwrpenSearch($searchQuery, $searchCount, $nSkippedRecords, $itemsPerPage, $filter);

    echoFilterBox($searchQuery, $filter, false);

    if (count($voorwerpen) < 1) {
        echo "Geen voorwerpen gevonden";
    }
    else {
        foreach($voorwerpen as $voorwerp) {
            array_push($voorwerpArray, $voorwerp);

            $list = loadbestanden($voorwerp->voorwerpnummer);
            $image = $list != null ? $list[0] : "NoImageAvailable.jpg";

            $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);

            if($biedingen == null){
                $prijs = $voorwerp->startprijs;
            }
            else{
                $prijs = $biedingen[0]->bodbedrag;
            }

            echoVoorwerp($voorwerp, $prijs, $image);
        }

        echoPagination($totalItems, $itemsPerPage, $currentPageNumber, $searchQuery);
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