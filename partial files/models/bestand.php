<?php

require_once ('databaseString.php');

// Replaces: loadBestanden($voorwerpId)
function loadBestandenByVoorwerpnummer($voorwerpnummer)
{
    global $db;
    $query = $db->prepare('execute sp_GetBestandenByVoorwerp @id = :voorwerpnummer');
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $bestand = $query->fetch(PDO::FETCH_OBJ);

    return $bestand == null ? "NoImageAvailable.jpg" : $bestand->filenaam;
}