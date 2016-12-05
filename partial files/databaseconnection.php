<?php
$db = new PDO ("sqlsrv:Server=Iproject2.icasites.nl;Database=Iproject2;ConnectionPooling=0", "iproject2", "ekEu7bpJ");

function getVoorwerp($voorwerpId){
    global $db;
    $query = $db->query("SELECT * FROM voorwerp WHERE voorwerpnummer=$voorwerpId");
    $voorwerp = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerp;
}

function getVoorwerpRubriek($voorwerpId){
    global $db;
    $query = $db->query("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer=$voorwerpId");
    $voorwerpinrubriek = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerpinrubriek->rubriekoplaagsteniveau;
}

function loadBestanden($voorwerpId){
    global $db;
    $query = $db->query("SELECT * FROM bestand WHERE voorwerpnummer ='" . $voorwerpId . "' ORDER BY filenaam");
    $bestandenList = array();
    while ($bestand = $query->fetch(PDO::FETCH_OBJ)) {
        array_push($bestandenList, $bestand->filenaam);
    }

    $bestandenList[0] = $bestandenList != null ? $bestandenList[0] : "NoImageAvalible.jpg";
    return $bestandenList;
}

function loadRubrieken(){
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

function loadVeilingItems($rubriekId)
{
    if (is_numeric($rubriekId)) {

        $rubriekenArray = loadRubrieken();
        $subRubriekenArray = array();
        $subsubRubriekenArray = array();
        $subsubsubRubriekenArray = array();
        foreach ($rubriekenArray as $k => $rubriek) {
            if ($rubriek->superrubriek == $rubriekId){
                array_push($subRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek){
            if ( in_array($rubriek->superrubriek, $subRubriekenArray)){
                array_push($subsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek){
            if (in_array($rubriek->superrubriek, $subsubRubriekenArray)){
                array_push($subsubsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }

        $temp = array_merge($subRubriekenArray, $subsubRubriekenArray, $subsubsubRubriekenArray);

        $ids = implode(',',$temp);
        if ($ids == ""){
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
    }
    else {
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
function queryVoorwerpen($queryString) {
    global $db;
    $query = $db->query($queryString);
    $voorwerpArray = array();
    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

        array_push($voorwerpArray, $voorwerp);

        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvalible.jpg";

        echoVoorwerp($voorwerp, $image);
    }

    if (count($voorwerpArray) < 1){
        echo "Geen voorwerpen gevonden";
    }

}

/**
 * Prints the voorwerp onto the page.
 *
 * @param $voorwerp The voorwerp.
 * @param $image The image of the voorwerp.
 */
function echoVoorwerp($voorwerp, $image) {
    $beschrijving = $voorwerp->beschrijving;
    if(strlen($beschrijving)>300){
        $beschrijving = substr($beschrijving,0,280) . "... <span>lees verder</span>";
    }

    echo '  <div class="veilingitem">
                    <a href="./veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
                        <img src="./bestanden/'.$image.'" alt="veilingsfoto">
                        <h4>'. $voorwerp->titel .'</h4>
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $voorwerp->startprijs . '</p>
                        <div class="veiling-info">
                            <span data-tijd="'.$voorwerp->looptijdeindeveiling.'" class="tijd"></span>
                            <button class="veiling-detail more-info">Meer informatie</button>
                        </div>
                    </a>
                </div>';
}

?>
