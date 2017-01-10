<?php
require_once ('databaseString.php');

// Replaces: loadRubrieken()
function loadAllRubrieken()
{
    global $db;
    $query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');

    return $query->fetchAll(PDO::FETCH_OBJ);
}