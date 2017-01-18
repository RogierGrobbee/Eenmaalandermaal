<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 18-1-2017
 * Time: 13:12
 */

function getAllbetalingswijzen() {
    global $db;
    $query = $db->query("SELECT betalingswijze FROM betalingswijze");

    return $query->fetchAll(PDO::FETCH_OBJ);
}

?>