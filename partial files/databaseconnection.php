<?php
$db = new PDO ("sqlsrv:Server=mssql.iproject.icasites.nl;Database=iproject2;ConnectionPooling=0",
    "iproject2", "ekEu7bpJ");

function getVoorwerp($voorwerpId)
{
    global $db;
    $query = $db->query("SELECT * FROM voorwerp WHERE voorwerpnummer=$voorwerpId");
    $voorwerp = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerp;
}

function getVoorwerpRubriek($voorwerpId)
{
    global $db;
    $query = $db->query("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer=$voorwerpId");
    $voorwerpinrubriek = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerpinrubriek->rubriekoplaagsteniveau;
}

function loadBestanden($voorwerpId)
{
    global $db;
    $query = $db->query("SELECT * FROM bestand WHERE voorwerpnummer ='" . $voorwerpId . "' ORDER BY filenaam");
    $bestandenList = array();
    while ($bestand = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($bestandenList, $bestand->filenaam);
    }

    $bestandenList[0] = $bestandenList != null ? $bestandenList[0] : "NoImageAvalible.jpg";
    return $bestandenList;
}

function loadRubrieken()
{
    global $db;
    $query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');
    $rubriekArray = array();
    $huidigeRubriek = null;
//lijst wordt gevult met alle rubrieken
    while ($rubriek = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($rubriekArray, $rubriek);
    }
    return $rubriekArray;
}

function loadVeilingItems($rubriekId, $currentPageNumber)
{
    $itemsPerPage = 10;
    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);
    if (is_numeric($rubriekId)) {

        $rubriekenArray = loadRubrieken();
        $subRubriekenArray = array();
        $subsubRubriekenArray = array();
        $subsubsubRubriekenArray = array();
        foreach ($rubriekenArray as $k => $rubriek) {
            if ($rubriek->superrubriek == $rubriekId) {
                array_push($subRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subRubriekenArray)) {
                array_push($subsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subsubRubriekenArray)) {
                array_push($subsubsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }

        $temp = array_merge($subRubriekenArray, $subsubRubriekenArray, $subsubsubRubriekenArray);
        $ids = implode(',', $temp);
        if ($ids == "") {
            $ids = "0";
        }
        $ids = $ids.','.$rubriekId;

        //count number of record to determine pages
        global $db;
        $countQuery = $db->prepare("execute sp_CountVoorwerpenInRubrieken @ids=?");
        $countQuery->bindParam(1, $ids, PDO::PARAM_STR);
        $countQuery->execute();

        $totalItems = 0;
        while ($item = $countQuery->fetch(PDO::FETCH_OBJ)) {
            $totalItems = $item->amount;
        }

        $voorwerpQuery = $db->prepare("execute sp_GetVoorwerpenInRubrieken @ids=?, @nSkippedRecords=?, @itemPerPage=?");
        $voorwerpQuery->bindParam(1, $ids, PDO::PARAM_STR);
        $voorwerpQuery->bindParam(2, $nSkippedRecords, PDO::PARAM_INT);
        $voorwerpQuery->bindParam(3, $itemsPerPage, PDO::PARAM_INT);

        queryVoorwerpen($voorwerpQuery, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber);


    } else {
        echo 'Geen rubriek geselecteerd';
    }

}

/**
 * Gets all the voorwerpen and prints the voorwerpen on the screen.
 *
 * @param $queryString The SELECT query in a string.
 */
function queryVoorwerpen($query, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber)
{
    global $db;
    $query->execute();
    $voorwerpArray = array();
    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

        array_push($voorwerpArray, $voorwerp);

        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvalible.jpg";

        echoVoorwerp($voorwerp, $image);
    }

    if (count($voorwerpArray) < 1) {
        echo "Geen voorwerpen gevonden";
    }

    //Pagina nummers VVVV

    if ($totalItems > $itemsPerPage) {
        $nPages = ceil($totalItems / $itemsPerPage);
        echo '<div class="row">
            <div class="col-sm-12">
            ';
        if ($currentPageNumber > 1) {
            echo("<button onclick=\"location.href='./rubriek.php?rubriek=" . $rubriekId . "&page=" . ($currentPageNumber - 1) . "'\">Previous</button>");
        }
        if ($nPages > 9) {
            if ($currentPageNumber < 6) {
                for ($i = 1; $i < 10; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber, $rubriekId);
            } else if ($currentPageNumber > ($nPages - 5)) {
                echoPageNumber(1, $currentPageNumber, $rubriekId);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($nPages - 8); $i < $nPages+1; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
            } else {
                echoPageNumber(1, $currentPageNumber, $rubriekId);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber, $rubriekId);
            }

        } else {
            for ($i = 1; $i < $nPages + 1; $i++) {
                echoPageNumber($i, $currentPageNumber, $rubriekId);
            }
        }
        if ($currentPageNumber < $nPages) {
            echo("<button onclick=\"location.href='./rubriek.php?rubriek=" . $rubriekId . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
        }
    }
    echo '</div></div>';
}

function echoPageNumber($pageNumber, $currentPageNumber, $rubriekId){
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./rubriek.php?rubriek=' . $rubriekId . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}

function queryHomepageVoorwerpen($queryString)
{
    global $db;

    $query = $db->query($queryString);

    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {
        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvalible.jpg";

        echoHomepageVoorwerp($voorwerp, $image);
    }
}

function featuredVoorwerp()
{
    global $db;

    $query = $db->query("SELECT TOP 1 voorwerpnummer,
                                titel,
                                beschrijving,
                                startprijs,
                                looptijdeindeveiling
                                FROM voorwerp");
    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {
        return $voorwerp;
    }
}

/**
 * Prints the voorwerp onto the page.
 *
 * @param $voorwerp The voorwerp.
 * @param $image The image of the voorwerp.
 */
function echoVoorwerp($voorwerp, $image)
{
    $beschrijving = $voorwerp->beschrijving;

    $beschrijving = strip_html_tags($beschrijving);

    if (strlen($beschrijving) > 300) {
        $beschrijving = substr($beschrijving, 0, 280) . "... <span>lees verder</span>";
    }

    echo '  <div class="veilingitem">
                    <a href="./veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <img src="./bestanden/' . $image . '" alt="veilingsfoto">
                        <h4>' . $voorwerp->titel . '</h4>
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $voorwerp->startprijs . '</p>
                        <div class="veiling-info">
                            <span data-tijd="' . $voorwerp->looptijdeindeveiling . '" class="tijd"></span>
                            <button class="veiling-detail btn-bied">Bied</button>
                        </div>
                    </a>
                </div>';
}

function strip_html_tags($str){
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(
        array(// Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
        ),
        "", //replace above with nothing
        $str );
    $str = replaceWhitespace($str);
    $str = strip_tags($str, '<br>');
    return $str;
} //function strip_html_tags ENDS

//To replace all types of whitespace with a single space
function replaceWhitespace($str) {
    $result = $str;
    foreach (array(
                 "  ", " \t",  " \r",  " \n",
                 "\t\t", "\t ", "\t\r", "\t\n",
                 "\r\r", "\r ", "\r\t", "\r\n",
                 "\n\n", "\n ", "\n\t", "\n\r",
             ) as $replacement) {
        $result = str_replace($replacement, $replacement[0], $result);
    }
    return $str !== $result ? replaceWhitespace($result) : $result;
}

function echoHomepageVoorwerp($voorwerp, $image)
{
    echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-11 homepage-veiling">
                    <a href="veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                    <img src="bestanden/' . $image . '"alt="veiling">
                    <h4>' . $voorwerp->titel . '</h4>
                    <div class="homepage-veiling-prijstijd">€' . $voorwerp->startprijs . '<br>
                    <span data-tijd="' . $voorwerp->looptijdeindeveiling . '" class="tijd"></span></div>
                    <button class="veiling-detail btn-homepage">Bied</button></a></div>';
}

?>
