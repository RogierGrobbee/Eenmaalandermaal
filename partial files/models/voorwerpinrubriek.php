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