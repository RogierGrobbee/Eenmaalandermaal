<?php
$db = new PDO ("sqlsrv:Server=Iproject2.icasites.nl;Database=Iproject2;ConnectionPooling=0", "iproject2", "ekEu7bpJ");

function getVoorwerp($voorwerpId){
    global $db;
    $query = $db->query("SELECT * FROM voorwerp WHERE voorwerpnummer=$voorwerpId");
    $voorwerp = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerp;
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
        global $db;
        $query = $db->query("select * from voorwerp where voorwerpnummer in(
	                          select voorwerpnummer from voorwerpinrubriek where rubriekoplaagsteniveau in
	                          (
		                        select rubrieknummer from rubriek where superrubriek='".$rubriekId."' or rubrieknummer = '".$rubriekId."'
	                          )
                            )");
        while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

            $list = loadbestanden($voorwerp->voorwerpnummer);
            $image = $list != null ? $list[0] : "NoImageAvalible.jpg";
            echo '  <div class="veilingitem">
                    <a href="./veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
                        <img src="./bestanden/'.$image.'" alt="veilingsfoto">
                        <h4>'. $voorwerp->titel .'</h4>
                        <p>' . $voorwerp->beschrijving . '</p>
                        <p class="prijs">€' . $voorwerp->startprijs . '</p>
                        <div class="veiling-info">
                            <div class="tijd">
                                <span class="tijd-hidden">'.$voorwerp->looptijdeindeveiling.'</span>
                                <span class="tijd-display"></span>
                            </div>
                            <button class="veiling-detail">Meer informatie</button>
                        </div>
                    </a>
                </div>';
        }
    }
    else {
        echo 'Rubriek niet gevonden';
    }
}


?>
