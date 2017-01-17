<?php
require_once ('databaseString.php');

function getAllLanden() {
    global $db;

    $query = $db->query("SELECT landnaam FROM land");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_OBJ);
}