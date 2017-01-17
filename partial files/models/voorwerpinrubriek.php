<?php
require_once ('databaseString.php');

// Replaces: getVoorwerpRubriek($voorwerpId)
function getVoorwerpRubriekByVoorwerpnummer($voorwerpnummer)
{
    global $db;
    $query = $db->prepare("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));

    return ($query->fetch(PDO::FETCH_OBJ))->rubriekoplaagsteniveau;
}

function insertVoorwerpInRubriek($voorwerpnummer,$rubrieknummer) {
    global $db;

    $query = $db->prepare("INSERT INTO voorwerpinrubriek (voorwerpnummer,rubriekoplaagsteniveau ) VALUES(:voorwerpnummer, :rubrieknummer)");
    $query->bindValue(':voorwerpnummer', $voorwerpnummer, PDO::PARAM_STR);
    $query->bindValue(':rubrieknummer', $rubrieknummer, PDO::PARAM_STR);
    return $query->execute(array(
        ':voorwerpnummer' => $voorwerpnummer,
        ':rubrieknummer' => $rubrieknummer
    ));
}