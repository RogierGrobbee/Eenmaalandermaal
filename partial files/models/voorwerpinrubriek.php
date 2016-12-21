<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:29
 */

namespace refactor;

// Replaces: getVoorwerpRubriek($voorwerpId)
function getVoorwerpRubByVnr($voorwerpnummer)
{
    global $db;
    $query = $db->prepare("SELECT * FROM voorwerpinrubriek WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $voorwerpinrubriek = $query->fetch(PDO::FETCH_OBJ);
    return $voorwerpinrubriek->rubriekoplaagsteniveau;
}