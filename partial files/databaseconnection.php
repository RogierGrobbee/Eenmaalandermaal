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

function loadVeilingItemsSearch($searchQuery){
    global $db;
    $statement = $db->prepare("SELECT * FROM voorwerp WHERE titel LIKE :search 
                            AND looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())
                            ORDER BY looptijdeindeveiling ASC");
    $statement->bindValue(':search', '%'.$searchQuery.'%');
    $statement->execute();
    $voorwerpArray = array();
    while ($voorwerp = $statement->fetch(PDO::FETCH_OBJ)) {
        array_push($voorwerpArray, $voorwerp);

        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvalible.jpg";

        echoVoorwerp($voorwerp, $image);
    }

    if (count($voorwerpArray) < 1){
        echo "Geen voorwerpen gevonden";
    }
}

function loadVeilingItems($rubriekId)
{
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

        queryVoorwerpen("select voorwerpnummer,
                                titel,
                                beschrijving,
                                startprijs,
                                looptijdeindeveiling
                                from voorwerp where voorwerpnummer in(
	                          select voorwerpnummer from voorwerpinrubriek where rubriekoplaagsteniveau in ($ids) or rubriekoplaagsteniveau = $rubriekId 
	                          )
                            AND looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())
                            ORDER BY looptijdeindeveiling ASC");
    } else {
        queryVoorwerpen("SELECT voorwerpnummer,
                                titel,
                                beschrijving,
                                startprijs,
                                looptijdeindeveiling
                                FROM voorwerp
                                WHERE looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())
                                ORDER BY looptijdeindeveiling ASC");
    }
}

/**
 * Gets all the voorwerpen and prints the voorwerpen on the screen.
 *
 * @param $queryString The SELECT query in a string.
 */
function queryVoorwerpen($queryString)
{
    global $db;
    $query = $db->query($queryString);
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
                                FROM voorwerp WHERE looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())");
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

function echoHomepageVoorwerp($voorwerp, $image){
    echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-11 homepage-veiling">
                    <a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
                    <img src="bestanden/'. $image .'"alt="veiling">
                    <h4>'.$voorwerp->titel.'</h4>
                    <div class="homepage-veiling-prijstijd">€'. $voorwerp->startprijs .'<br>
                    <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span></div>
                    <button class="veiling-detail btn-homepage">Bied</button></a></div>';
}

function returnGeheimeVragen()
{
    global $db;

    $query = $db->query("SELECT tekstvraag FROM vraag");
    echo "<select>";
    foreach ($query as $row) {
        echo "<option value = " . $row['tekstvraag'] . " >" . $row['tekstvraag'] . "</option >";

    }
    echo "</select>";
}

function returnAllCountries()
{
    global $db;
    $query = $db->query("SELECT landnaam FROM land");
    echo "<select>";
    foreach ($query as $row) {
        echo "<option value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option >";

    }
    echo "</select>";
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

function doesValidationCodeexist($validationCode) {
    global $db;
    $exist = false;
    $query = $db->query("SELECT validatiecode FROM validation");
    foreach ($query as $row) {
        if ($row["validatiecode"] == $validationCode) {
            $exist = true;
        }
    }
    return $exist;
}

?>
