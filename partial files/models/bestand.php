<?php

require_once ('databaseString.php');

// Replaces: loadBestanden($voorwerpId)
function loadBestandByVoorwerpnummer($voorwerpnummer)
{
    global $db;
    $query = $db->prepare('execute sp_GetBestandenByVoorwerp @id = :voorwerpnummer');
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $bestand = $query->fetch(PDO::FETCH_OBJ);

    return $bestand == null ? "NoImageAvailable.png" : $bestand->filenaam;
}

function loadBestandenByVoorwerpnummer($voorwerpnummer)
{
    global $db;
    $query = $db->prepare('execute sp_GetBestandenByVoorwerp @id = :voorwerpnummer');
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));

    return $query->fetchAll(PDO::FETCH_OBJ);
}

