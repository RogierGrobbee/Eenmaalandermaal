<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:29
 */

namespace refactor;

require_once ('databaseString.php');

// Replaces: getVoorwerpRubriek($voorwerpId)
function getVoorwerpRubByVnr($voorwerpnummer)
{
    global $db;
    $query = $db->prepare("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));

    return ($query->fetch(PDO::FETCH_OBJ))->rubriekoplaagsteniveau;
}