<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 20-12-2016
 * Time: 13:24
 */

// Replaces: loadRubrieken()
function loadAllRubrieken()
{
    global $db;
    $query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');

    return $query->fetchAll(PDO::FETCH_OBJ);
}