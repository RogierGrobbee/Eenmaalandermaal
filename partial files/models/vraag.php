<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:47
 */

namespace refactor;

require_once ('databaseString.php');

// Replaces part of the returnGeheimeVragen() function
function getAllvragen () {
    global $db;

    $query = $db->query("SELECT tekstvraag, vraagnummer FROM vraag");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_OBJ);
}