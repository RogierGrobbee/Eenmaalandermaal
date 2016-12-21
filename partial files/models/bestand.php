<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 20-12-2016
 * Time: 13:03
 */

namespace refactor;

// Replaces: loadBestanden($voorwerpId)
function loadBestandenByVnr($voorwerpnummer)
{
    global $db;
    $query = $db->prepare('execute sp_GetBestandenByVoorwerp @id = :voorwerpnummer');
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $bestand = $query->fetch(PDO::FETCH_OBJ);

    return $bestand == null ? "NoImageAvailable.jpg" : $bestand->filenaam;
}