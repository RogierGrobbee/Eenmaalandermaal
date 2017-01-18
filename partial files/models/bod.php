<?php

require_once ('databaseString.php');

// Replaces: getVoorwerpBiedingen($voorwerpnummer)
function getBiedingenByVoorwerpnummer($voorwerpnummer){
    global $db;

    $query = $db->prepare("SELECT * FROM bod WHERE voorwerpnummer = :voorwerpnummer ORDER BY bodbedrag DESC");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));

    return $query->fetchAll(PDO::FETCH_OBJ);
}

// Replaces: insertNewBod($voorwerp, $amount, $gebruiker)
function insertBod($voorwerp, $amount, $gebruiker) {
    global $db;

    $query = $db->prepare("INSERT INTO bod VALUES (:voorwerpnummer, :amount , :gebruiker , getdate())");

    return $query->execute(array(
        ':voorwerpnummer' => $voorwerp->voorwerpnummer,
        ':amount' => $amount,
        ':gebruiker' => $gebruiker
    ));
}