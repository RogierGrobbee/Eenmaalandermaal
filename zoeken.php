<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require_once('partial files/models/voorwerp.php');
require_once('partial files/models/bestand.php');
require_once('partial files/models/bod.php');
require_once('partial files/models/miscellaneous.php');
require_once('partial files/voorwerplist.php');


function loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) {
    $itemsPerPage = 10;
    $searchCount = substr_count($searchQuery, ' ');
    $searchQuery .= " ";

    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);

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
                        <img src="pics/' . $image . '" alt="veilingsfoto">
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