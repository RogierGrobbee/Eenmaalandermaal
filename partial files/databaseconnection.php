<?php
$db = new PDO ("sqlsrv:Server=mssql.iproject.icasites.nl;Database=iproject2;ConnectionPooling=0",
    "iproject2", "ekEu7bpJ");
$itemsPerPage = 10;

// Replacement: getVoorwerpById($voorwerpnummer)
function getVoorwerp($voorwerpId)
{
    global $db;
    $query = $db->query("SELECT * FROM voorwerp WHERE voorwerpnummer=$voorwerpId");
    $voorwerp = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerp;
}

// Replacement: getVoorwerpRubByVnr($voorwerpnummer)
function getVoorwerpRubriek($voorwerpId)
{
    global $db;
    $query = $db->query("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer=$voorwerpId");
    $voorwerpinrubriek = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerpinrubriek->rubriekoplaagsteniveau;
}

// Replacement: loadBestandenByVnr($voorwerpnummer)
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

// Replacement: loadRubrieken()
function loadRubrieken()
{
    global $db;
    $query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');
    $rubriekArray = array();
    $huidigeRubriek = null;

    while ($rubriek = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($rubriekArray, $rubriek);
    }
    return $rubriekArray;
}

// This function needs to be in the pages it is used in.
// All the database interactions in this functions are split up.
// Look at the voorwerp.php for more information.
function loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter)
{
    global $db;
    global $itemsPerPage;
    $searchCount = substr_count($searchQuery, ' ');
    $searchQuery .= " ";

    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);

    $countQuery = $db->prepare("execute sp_CountSearchVoorwerpenByTitle @search=?, @searchCount=?");
    $countQuery->bindValue(1, $searchQuery, PDO::PARAM_STR);
    $countQuery->bindParam(2, $searchCount, PDO::PARAM_INT);
    $countQuery->execute();

    $totalItems = 0;
    while ($item = $countQuery->fetch(PDO::FETCH_OBJ)) {
        $totalItems = $item->amount;
    }

    $statement = $db->prepare("execute sp_SearchVoorwerpenByTitle @search=?, @searchCount=?, @nSkippedRecords=?, @itemPerPage=?, @filter=?");
    $statement->bindValue(1, $searchQuery, PDO::PARAM_STR);
    $statement->bindParam(2, $searchCount, PDO::PARAM_INT);
    $statement->bindParam(3, $nSkippedRecords, PDO::PARAM_INT);
    $statement->bindParam(4, $itemsPerPage, PDO::PARAM_INT);
    $statement->bindValue(5, $filter, PDO::PARAM_STR);

    $statement->execute();
    echoFilterBox($searchQuery, $filter, false);

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

    if (count($voorwerpArray) < 1) {
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
                for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
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
        echo '</div></div>';
    }
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function echoSearchPageNumber($pageNumber, $currentPageNumber, $search)
{
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./zoeken.php?search=' . $search . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}

// This function needs to be in the pages it is used in.
// All the database interactions in this functions are split up.
// Made functions:
// - countVoorwerpenTitlesBySearchTerm
// - searchVoorwerpenByTitle
// - echoPagination
function loadVeilingItems($rubriekId, $currentPageNumber, $filter)
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
        $ids = $ids . ',' . $rubriekId;

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
        $voorwerpQuery->bindValue(4, $filter, PDO::PARAM_STR);

        echoFilterBox($rubriekId, $filter, true);

        queryVoorwerpen($voorwerpQuery, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber);


    } else {
        echo 'Geen rubriek geselecteerd';
    }

}

// This function needs to be in the pages it is used in.
// All the database interactions in this functions are split up.
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
                for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
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

}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function echoPageNumber($pageNumber, $currentPageNumber, $rubriekId)
{
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

// Replacement: insertBod($voorwerp, $amount, $gebruiker)
function insertNewBod($voorwerp, $bod, $gebruiker){
    global $db;

    $return= new stdClass();

    $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);
    if($biedingen == null){
        if($biedingen < $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs)){
            $return->bodSuccesful = false;
            $return->message = "U moet minimaal €".calculateIncrease($voorwerp->startprijs)." hoger bieden!";
            return $return;
        }
    }
    else{
        if($bod < $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag)){
            $return->bodSuccesful = false;
            $return->message = "U moet minimaal €".calculateIncrease($voorwerp->startprijs)." hoger bieden!";
            return $return;
        }
        else if($gebruiker == $biedingen[0]->gebruikersnaam){
            $return->bodSuccesful = false;
            $return->message = "U heeft al het hoogste bod!";
            return $return;
        }
        else if(date("d/m/y H:i:s", strtotime($voorwerp->looptijdeindeveiling)) < date('d/m/y H:i:s')){
            $return->bodSuccesful = false;
            $return->message = "De veiling is al afgelopen!";
            return $return;
        }
    }

    $query = $db->query("INSERT INTO bod VALUES (".$voorwerp->voorwerpnummer.", ".$bod.", '".$gebruiker."', getdate())");
    if($query){
        $return->bodSuccesful = true;
        return $return;
    }

    $return->bodSuccesful = false;
    $return->message = "Er kan niet hoger geboden worden dan 100.000!";
    return $return;
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
// When this function is defined in the page it needs to get all voorwerpen through the model.
/**
 * Returns the most popular voorwerp. This will be used on the banner for the frontpage
 * @param $queryString send a query to echo voorwerpen on the homepage
 */
function queryHomepageVoorwerpen($queryString)
{
    global $db;

    $query = $db->query($queryString);
    $count = 0;

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

        $count++;
        echoHomepageVoorwerp($voorwerp, $prijs, $image);
    }

    if($count == 0){
        echo "<div class='error'>U heeft nog niet geboden op een veiling.</div>";
    }
}

// Replacement: getFeaturedVoorwerp()
/**
 * Returns the most popular voorwerp. This will be used on the banner for the frontpage
 */
function featuredVoorwerp()
{
    global $db;

    $query = $db->query("SELECT TOP 1 v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                v.looptijdeindeveiling FROM voorwerp as v 
                                FULL OUTER JOIN Bod as b ON v.voorwerpnummer=b.voorwerpnummer 
                                WHERE v.looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE()) 
								GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,v.looptijdeindeveiling
								ORDER BY count(b.voorwerpnummer) DESC");

    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {
        return $voorwerp;
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


// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
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

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
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

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function strip_html_tags($str)
{
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(
        array(// Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
        ),
        "", //replace above with nothing
        $str);
    $str = replaceWhitespace($str);
    $str = strip_tags($str, '<br>');
    return $str;
} //function strip_html_tags ENDS

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
//To replace all types of whitespace with a single space
function replaceWhitespace($str)
{
    $result = $str;
    foreach (array(
                 "  ", " \t", " \r", " \n",
                 "\t\t", "\t ", "\t\r", "\t\n",
                 "\r\r", "\r ", "\r\t", "\r\n",
                 "\n\n", "\n ", "\n\t", "\n\r",
             ) as $replacement) {
        $result = str_replace($replacement, $replacement[0], $result);
    }
    return $str !== $result ? replaceWhitespace($result) : $result;
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
// The database interactions are saved in one of the models.
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

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
// The database interactions are saved in one of the models.
function returnAllCountries()
{
    global $db;
    $query = $db->query("SELECT landnaam FROM land");
    echo "<select name='country'>";
    foreach ($query as $row) {
        if ($row['landnaam'] == 'Nederland') {
            echo "<option selected='selected' value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option>";
        } else {
            echo "<option value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option>";
        }
    }
    echo "</select>";
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
// The database interactions are saved in one of the models.
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

// Replacement: validateUser($code)
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

// Replacement: getUserByUsername($username)
function getUserByUsername($username){
    global $db;
    $userQuery = $db->prepare('SELECT * FROM gebruiker where gebruikersnaam=?');
    $userQuery->bindParam(1, $username);
    $userQuery->execute();
    return $userQuery->fetch(PDO::FETCH_OBJ);
}

// Replacement: getPhoneNumbers($username)
function getPhoneNumbers($username){
    global $db;
    $phoneQuery = $db->prepare('select * from gebruikerstelefoon where gebruikersnaam =? order by volgnr');
    $phoneQuery->bindParam(1, $username);
    $phoneQuery->execute();
    return $phoneQuery->fetchAll(PDO::FETCH_OBJ);
}

// Replacement: insertPhoneNumber($volgnr, $username, $phonenumber)
function addPhoneNumber($volgnr, $username, $phonenumber){
    global $db;
    $phoneQuery = $db->prepare('insert into gebruikerstelefoon (volgnr,gebruikersnaam,telefoon) values(?,?,?)');
    $phoneQuery->bindParam(1, $volgnr);
    $phoneQuery->bindParam(2, $username);
    $phoneQuery->bindParam(3, $phonenumber);
    $phoneQuery->execute();
}

// Replacement: getBiedingenByUsrName($username)
function getBiedingenByUsername($username){
    global $db;
    $bodQuery = $db->prepare('SELECT v.voorwerpnummer, v.titel, b.bodbedrag, b.bodtijdstip FROM voorwerp as v full outer join bod as b on v.voorwerpnummer = b.voorwerpnummer where b.gebruikersnaam =? Order by b.bodtijdstip desc');
    $bodQuery->bindParam(1, $username);
    $bodQuery->execute();
    return $bodQuery->fetchAll(PDO::FETCH_OBJ);
}

// Replacement: doesUsernameExist($username)
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

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function postCodeCheck($postcode)
{
    $remove = str_replace(" ", "", $postcode);
    $upper = strtoupper($remove);

    if (preg_match("/^\W*[1-9]{1}[0-9]{3}\W*[a-zA-Z]{2}\W*$/", $upper)) {
        return $upper;
    } else {
        return false;
    }
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Replacement: doesValidationCodeExist($code)
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

// Replacement: getUserByUsername($username)
function getPassword($username)
{
    global $db;
    $statement = $db->prepare("SELECT wachtwoord FROM gebruiker WHERE gebruikersnaam= :username ");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['wachtwoord'];
}

// Replacement: getUserByUsername($username)
function getValidation($username)
{
    global $db;
    $statement = $db->prepare("SELECT gevalideerd FROM gebruiker WHERE gebruikersnaam= :username ");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['gevalideerd'];
}

// Replacement: getAntwoordByUsrName($username)
function getSecretAnswer($username)
{
    global $db;
    $statement = $db->prepare("SELECT antwoordtekst FROM antwoord WHERE gebruikersnaam= :username ");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['antwoordtekst'];
}

// Replacement: getAntwoordByUsrName($username)
function getQuestionNumber($username)
{
    global $db;
    $statement = $db->prepare("SELECT vraagnummer FROM antwoord WHERE gebruikersnaam= :username ");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['vraagnummer'];
}

// Replacement: getEmail($username)
function getEmail($username)
{
    global $db;
    $statement = $db->prepare("SELECT email FROM gebruiker WHERE gebruikersnaam= :username");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['email'];
}

// Replacement: hashPass($pass)
function hashPass($pass)
{
    $options = [
        'cost' => 12,
        'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
    ];
    return password_hash($pass, PASSWORD_BCRYPT, $options);
}

// Replacement: veilingEnded($voorwerpnummer)
function veilingEnded($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT isVoltooid FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer ");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch();
    return $row['isVoltooid'];
}

// Replacement: endVeilingByVnr($voorwerpnummer)
function endVeilingByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("UPDATE voorwerp SET isVoltooid = 1 WHERE voorwerpnummer = :voorwerpnummer");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
}

// Replacement: getVerkoperByVnr($voorwerpnummer)
function getVerkoperByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email FROM gebruiker
                                WHERE gebruikersnaam in(
                                    SELECT verkoper FROM voorwerp where voorwerpnummer = :voorwerpnummer
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}

// Replacement: getTopBidderByVnr($voorwerpnummer)
function getHighestBidderByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email from gebruiker WHERE gebruikersnaam in(
                                    SELECT TOP 1 gebruikersnaam FROM bod where voorwerpnummer = :voorwerpnummer
                                    ORDER BY gebruikersnaam ASC
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}

// This function needs to be in the pages where this function is used.
// This function will not be in one of the models.
function cantVisitLoggedIn() {
    if (!empty($_SESSION['user'])) {
        header('Location: index.php');
    }
}
?>
