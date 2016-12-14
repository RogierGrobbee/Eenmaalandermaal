<?php
$db = new PDO ("sqlsrv:Server=mssql.iproject.icasites.nl;Database=iproject2;ConnectionPooling=0",
    "iproject2", "ekEu7bpJ");
$itemsPerPage = 10;

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
    $query = $db->prepare('execute sp_GetBestandenByVoorwerp @id=?');
    $query->bindParam(1, $voorwerpId);
    $query->execute();
    $bestandenList = array();
    while ($bestand = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($bestandenList, $bestand->filenaam);
    }

    $bestandenList[0] = $bestandenList != null ? $bestandenList[0] : "NoImageAvailable.jpg";
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

/*
* Returns all voorwerpen relevant to $searchQuery
* @param $searchQuery the word to search
*/
function loadVeilingItemsSearch($searchQuery, $currentPageNumber){
    global $db;
    global $itemsPerPage;
    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);

    $countQuery = $db->prepare("execute sp_CountSearchVoorwerpenByTitle @search=?");
    $countQuery->bindValue(1, '%'.$searchQuery.'%', PDO::PARAM_STR);
    $countQuery->execute();

    $totalItems = 0;
    while ($item = $countQuery->fetch(PDO::FETCH_OBJ)) {
        $totalItems = $item->amount;
    }

    $statement = $db->prepare("execute sp_SearchVoorwerpenByTitle @search=?, @nSkippedRecords=?, @itemPerPage=?, @filter=?");
    $statement->bindValue(1, '%'.$searchQuery.'%', PDO::PARAM_STR);
    $statement->bindParam(2, $nSkippedRecords, PDO::PARAM_INT);
    $statement->bindParam(3, $itemsPerPage, PDO::PARAM_INT);
    $statement->bindValue(4, 'laagstebod', PDO::PARAM_STR);

    $statement->execute();
    $voorwerpArray = array();
    while ($voorwerp = $statement->fetch(PDO::FETCH_OBJ)) {
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

    if (count($voorwerpArray) < 1){
        echo "Geen voorwerpen gevonden";
    }

    //Pagina's VVVV

    if ($totalItems > $itemsPerPage) {
        $nPages = ceil($totalItems / $itemsPerPage);
        echo '<div class="row">
            <div class="col-sm-12">
            ';
        if ($currentPageNumber > 1) {
            echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchQuery . "&page=" . ($currentPageNumber - 1) . "'\">Previous</button>");
        }
        if ($nPages > 9) {
            if ($currentPageNumber < 6) {
                for ($i = 1; $i < 10; $i++) {
                    echoSearchPageNumber($i, $currentPageNumber, $searchQuery);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoSearchPageNumber($nPages, $currentPageNumber, $searchQuery);
            } else if ($currentPageNumber > ($nPages - 5)) {
                echoSearchPageNumber(1, $currentPageNumber, $searchQuery);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($nPages - 8); $i < $nPages+1; $i++) {
                    echoSearchPageNumber($i, $currentPageNumber, $searchQuery);
                }
            } else {
                echoSearchPageNumber(1, $currentPageNumber, $searchQuery);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                    echoSearchPageNumber($i, $currentPageNumber, $searchQuery);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoSearchPageNumber($nPages, $currentPageNumber, $searchQuery);
            }

        } else {
            for ($i = 1; $i < $nPages + 1; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchQuery);
            }
        }
        if ($currentPageNumber < $nPages) {
            echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchQuery . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
        }
    }
}

function echoSearchPageNumber($pageNumber, $currentPageNumber, $search){
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./zoeken.php?search=' . $search . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}

function loadVeilingItems($rubriekId, $currentPageNumber)
{
    global $itemsPerPage;
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

        $voorwerpQuery = $db->prepare("execute sp_GetVoorwerpenInRubrieken @ids=?, @nSkippedRecords=?, @itemPerPage=?, @filter=?");
        $voorwerpQuery->bindParam(1, $ids, PDO::PARAM_STR);
        $voorwerpQuery->bindParam(2, $nSkippedRecords, PDO::PARAM_INT);
        $voorwerpQuery->bindParam(3, $itemsPerPage, PDO::PARAM_INT);
        $voorwerpQuery->bindValue(4, 'laagstebod', PDO::PARAM_STR);

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
function queryVoorwerpen($query, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber )
{
    echoFilterBox();
    global $db;
    $query->execute();
    $voorwerpArray = array();
    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

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

function calculateIncrease($prijs){
    switch(true){
        case $prijs >= 5000:
            return 50;
            break;
        case $prijs >= 1000:
            return 10;
            break;
        case $prijs >= 500:
            return 5;
            break;
        case $prijs >= 50:
            return 1;
            break;
        default:
            return 0.50;
            break;
    }
}


/**
 * Returns an array of biedingen on a voorwerp
 *
 * @param $voorwerpnummer the number of the voorwerp.
 */
function getVoorwerpBiedingen($voorwerpnummer){
    global $db;

    $query = $db->query("SELECT * FROM bod WHERE voorwerpnummer=$voorwerpnummer ORDER BY bodbedrag DESC");
    $biedingen = array();

    while ($bod = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($biedingen, $bod);
    }

    return $biedingen;
}


function insertNewBod($voorwerp, $bod, $gebruiker){
    global $db;
    $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);
    if($biedingen == null){
        if($biedingen < $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs)){
            return false;
        }
    }
    else{
        if($bod < $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag) ||
            $gebruiker == $biedingen[0]->gebruikersnaam){
            return false;
        }
    }

    $query = $db->query("INSERT INTO bod VALUES (".$voorwerp->voorwerpnummer.", ".$bod.", '".$gebruiker."', getdate())");
    if($query){
        return true;
    }
}

/**
 * Returns the most popular voorwerp. This will be used on the banner for the frontpage
 * @param $queryString send a query to echo voorwerpen on the homepage
 */
function queryHomepageVoorwerpen($queryString)
{
    global $db;

    $query = $db->query($queryString);

    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {
        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvailable.jpg";
        $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);

        if($biedingen == null){
            $prijs = $voorwerp->startprijs;
        }
        else{
            $prijs = $biedingen[0]->bodbedrag;
        }

        echoHomepageVoorwerp($voorwerp, $prijs, $image);
    }
}

/**
 * Returns the most popular voorwerp. This will be used on the banner for the frontpage
 */
function featuredVoorwerp()
{
    global $db;

    $query = $db->query("SELECT TOP 4 v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                v.looptijdeindeveiling FROM voorwerp as v 
                                FULL OUTER JOIN Bod as b ON v.voorwerpnummer=b.voorwerpnummer 
                                WHERE v.looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE()) 
								GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,v.looptijdeindeveiling
								ORDER BY count(b.voorwerpnummer) DESC");

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
                    <a href="./veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
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


/**
 * Prints a voorwerp on the frontpage
 *
 * @param $voorwerp The voorwerp.
 * @param $image The image of the voorwerp.
 */
function echoHomepageVoorwerp($voorwerp, $prijs, $image){
    if($prijs < 1){
        $prijs = "0".$prijs;
    }

    echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 homepage-veiling">
            <a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
            <img src="pics/'. $image .'"alt="veiling">
            <h4>'.$voorwerp->titel.'</h4>
            <div class="homepage-veiling-prijstijd">€'. $prijs .'<br>
            <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span></div>
            <button class="veiling-detail btn-homepage">Bied</button></a></div>';
}

function echoFilterBox(){
    echo '<select>
  <option value="volvo">Volvo</option>
  <option value="saab">Saab</option>
  <option value="mercedes">Mercedes</option>
  <option value="audi">Audi</option>
</select>';
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

function returnGeheimeVragen()
{
    global $db;

    $query = $db->query("SELECT tekstvraag, vraagnummer FROM vraag");
    echo "<select  name='geheimeVraag'>";
    foreach ($query as $row) {
        echo "<option value = " . $row['vraagnummer'] . " >" . $row['tekstvraag'] . "</option >";

    }
    echo "</select>";
}


function returnAllCountries()
{
    global $db;
    $query = $db->query("SELECT landnaam FROM land");
    echo "<select name='country'>";
    foreach ($query as $row) {
        echo "<option value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option >";
    }
    echo "</select>";
}

function calculateExpire($code)
{
    global $db;
    $statement = $db->prepare("select datumTijd from validation where validatiecode = :validatiecode");
    $statement->execute(array(':validatiecode' => $code));
    $row = $statement->fetch();

    $expire = date("Y-m-d H:i:s", strtotime('+0 hour'));
    $timestamp1 = strtotime($expire);
    $timestamp2 = strtotime($row['datumTijd']);
    $hour = abs($timestamp2 - $timestamp1)/(60*60);
    if ($hour > 4 ) {
        return false;
    } else {
        return true;
    }


}

function validateUser($code)
{
    global $db;
    $sth = "UPDATE g
            SET g.gevalideerd = 1
            FROM gebruiker AS g
            INNER JOIN validation AS v
            ON g.gebruikersnaam = v.gebruikersnaam
            WHERE v.validatiecode  = :validatie";
    $sthm = $db->prepare($sth);
    $sthm->bindParam(':validatie', $code);
    $sthm->execute();

}


function doesUsernameAlreadyExist($username)
{
    global $db;
    $exist = false;
    $query = $db->query("SELECT gebruikersnaam FROM gebruiker");
    foreach ($query as $row) {
        if ($row["gebruikersnaam"] == $username) {
            $exist = true;
        }
    }
    return $exist;
}

function postCodeCheck($postcode)
{
    $remove = str_replace(" ","", $postcode);
    $upper = strtoupper($remove);

    if( preg_match("/^\W*[1-9]{1}[0-9]{3}\W*[a-zA-Z]{2}\W*$/",  $upper)) {
        return $upper;
    } else {
        return false;
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function doesValidationCodeexist($code)
{
    global $db;
    $statement = $db->prepare("SELECT validatiecode FROM validation WHERE validatiecode = :code");
    $statement->execute(array(':code' => $code));
    $row = $statement->fetch();
    if (!$row) {
        return false;
    } else {
        return true;
    }

}

function hashPass($pass) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($pass, PASSWORD_BCRYPT, $options)."\n";
}

?>
