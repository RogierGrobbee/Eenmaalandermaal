<?php
require_once ('databaseString.php');

// Replaces part of the returnGeheimeVragen() function
function getAllVragen () {
    global $db;

    $query = $db->query("SELECT tekstvraag, vraagnummer FROM vraag");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_OBJ);
}