<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 18-1-2017
 * Time: 13:22
 */

function getAllLooptijden() {
    global $db;
    $query = $db->query("SELECT looptijd FROM looptijd");

    return $query->fetchAll(PDO::FETCH_OBJ);
}

?>