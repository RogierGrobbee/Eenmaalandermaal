<?php
require_once ('databaseString.php');

// Replaces: loadRubrieken()
function loadAllRubrieken()
{
    global $db;
    $query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');

    return $query->fetchAll(PDO::FETCH_OBJ);
}

function getRubriekenBySuperrubriek($superId = null) {
    global $db;

    if (is_null($superId)) {
        $query = $db->query("SELECT * FROM rubriek WHERE superrubriek is null ORDER BY rubrieknaam");
    }
    else {
        $query = $db->prepare("SELECT * FROM rubriek WHERE superrubriek = :id ORDER BY rubrieknaam");
        $query->execute(array(
            ':id' => $superId
        ));
    }

    return $query->fetchAll(PDO::FETCH_OBJ);
}