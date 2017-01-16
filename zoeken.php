<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require_once('partial files/models/voorwerp.php');
require_once('partial files/models/bestand.php');
require_once('partial files/models/bod.php');
require_once('partial files/models/miscellaneous.php');

function echoSearchPageNumber($pageNumber, $currentPageNumber, $search)
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

function loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) {
    $itemsPerPage = 10;
    $searchCount = substr_count($searchQuery, ' ');
    $searchQuery .= " ";

    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);

//TODO: These functions need to be executed in the search page.
    $totalItems = countVoorwerpenBySearchTerm ($searchQuery, $searchCount);

    $voorwerpen = getVoorwerpenBySearch($searchQuery, $searchCount, $nSkippedRecords, $itemsPerPage, $filter);

    echoFilterBox(trim($searchQuery), $filter, false);

    if (count($voorwerpen) < 1) {
        echo "Geen voorwerpen gevonden";
    }
    else {
        foreach($voorwerpen as $voorwerp) {
            $image = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);

            $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);

            if($biedingen == null){
                $prijs = $voorwerp->startprijs;
            }
            else{
                $prijs = $biedingen[0]->bodbedrag;
            }

            echoVoorwerp($voorwerp, $prijs, $image);
        }
        if ($totalItems > $itemsPerPage) {
            echoPagination($totalItems, $itemsPerPage, $currentPageNumber, $searchQuery);
        }
    }
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
/**
 * Prints the voorwerp onto the page.
 *
 * @param $voorwerp The voorwerp.
 * @param $image The image of the voorwerp.
 */
function echoVoorwerp($voorwerp, $prijs, $image)
{
    $beschrijving = $voorwerp->beschrijving;

    $beschrijving = strip_html_tags($beschrijving);

    if (strlen($beschrijving) > 300) {
        $beschrijving = substr($beschrijving, 0, 280) . "... <span>lees verder</span>";
    }

    if($prijs < 1){
        $prijs = "0".$prijs;
    }

    echo '  <div class="veilingitem">
                    <a href="/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <img src="pics/' . $image . '" alt="veilingsfoto" onError="this.onerror=null;this.src=\'itemImages/'. $image . '\'">
                        <h4>' . $voorwerp->titel . '</h4></a>
                        <a href="/rubriek.php?rubriek=' . $voorwerp->rubrieknummer . '">
                        <h5>Uit rubriek '. $voorwerp->rubrieknaam .'</h5></a>
                        <a href="/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $prijs. '</p>
                        <div class="veiling-info">
                            <span data-tijd="' . $voorwerp->looptijdeindeveiling . '" class="tijd"></span>
                            <button class="veiling-detail">Bied</button>
                        </div>
                    </a>
                </div>';
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
loadRubriekenSidebar(null);

?>

<?php
    loadVeilingItemsSearch($search, $page, $filter);
?>

<?php require('partial files\footer.php') ?>