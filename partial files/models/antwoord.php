<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 14:38
 */

require_once ('databaseString.php');

function getAntwoordByUsrName($username) {
    global $db;
    $query = $db->prepare("SELECT vraagnummer, gebruikersnaam, antwoordtekst
                                  FROM antwoord WHERE gebruikersnaam= :username ");
    $query->execute(array(':username' => $username));
    return $query->fetch(PDO::FETCH_OBJ);
}