<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require_once('partial files/models/voorwerp.php');
require_once('partial files/models/bestand.php');
require_once('partial files/models/bod.php');
require_once('partial files/models/miscellaneous.php');
require_once('partial files/models/rubriek.php');
require_once('partial files/voorwerplist.php');

//input rubriekId wordt opgehaald
$inputRubriekId = null;
if (isset($_GET['rubriek'])) {
    if (is_numeric($_GET['rubriek'])) {
        $inputRubriekId = $_GET['rubriek'];
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

$rubriekArray = loadAllRubrieken();
$huidigeRubriek = null;

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}

function loadVeilingItems($rubriekId, $currentPageNumber, $filter)
{
    $itemsPerPage = 10;

    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);
    if (is_numeric($rubriekId)) {
        global $rubriekArray;
        $subRubriekenArray = array();
        $subsubRubriekenArray = array();
        $subsubsubRubriekenArray = array();
        foreach ($rubriekArray as $k => $rubriek) {
            if ($rubriek->superrubriek == $rubriekId) {
                array_push($subRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subRubriekenArray)) {
                array_push($subsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subsubRubriekenArray)) {
                array_push($subsubsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }

        $temp = array_merge($subRubriekenArray, $subsubRubriekenArray, $subsubsubRubriekenArray);
        $ids = implode(',', $temp);
        if ($ids == "") {
            $ids = "0";
        }
        $ids = $ids . ',' . $rubriekId;

        $count = countVoorwerpenInRubrieken($ids);

        $voorwerpen = getVoorwerpenInRubriek($ids, $nSkippedRecords, $itemsPerPage, $filter);

        echoFilterBox($rubriekId, $filter);

        if (count($voorwerpen) < 1) {
            echo "Geen voorwerpen gevonden";
        }
        else{
            foreach($voorwerpen as $voorwerp){
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
        }

        if($count > $itemsPerPage){
            echoPagination($count, $itemsPerPage, $currentPageNumber, $rubriekId);
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

    echo '<div class="veilingitem">
                    <a href="/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <img src="pics/' . $image . '" alt="veilingsfoto">
                        <h4>' . $voorwerp->titel . '</h4>
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $prijs. '</p>
                        <div class="veiling-info">
                            <span data-tijd="' . $voorwerp->looptijdeindeveiling . '" class="tijd"></span>
                            <button class="veiling-detail">Bied</button>
                        </div>
                    </a>
                </div>';
}

require('partial files\header.php');
require('partial files\navigatie.php');

//De koptekst wordt gezet, als er geen rubriek is geselecteerd id het Welkom
if ($huidigeRubriek != null) {
    echo '<h1>' . $huidigeRubriek->rubrieknaam . '</h1>';
} else {
    echo '<h1>Nieuwste veilingen</h1>';
}

//sidebar maken op basis van rubrieken
require('partial files\sidebar.php');
if(isset($navigatieArray)) {
    loadRubriekenSidebar($navigatieArray[count($navigatieArray) - 1]);
}
else{
    loadRubriekenSidebar($huidigeRubriek);
}
?>


    <?php
    if (!is_null($huidigeRubriek)) {
        include 'partial files\subrubrieken.php';
        loadSubrubrieken($rubriekArray, $huidigeRubriek);
    }
    ?>

    <?php
    loadVeilingItems($inputRubriekId, $page, $filter);
    ?>

<?php require('partial files\footer.php') ?>