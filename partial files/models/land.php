<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:49
 */

namespace refactor;

require_once ('databaseString.php');

function getAllLanden() {
    global $db;

    $query = $db->query("SELECT landnaam FROM land");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_OBJ);
}