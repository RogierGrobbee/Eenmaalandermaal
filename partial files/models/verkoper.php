<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 23-12-2016
 * Time: 14:06
 */

require_once('databaseString.php');

function getVerkoperByUsername($username)
{
    global $db;
    $query = $db->prepare('select v.*, g.verkoper
            FROM gebruiker AS g
            full outer join verkoper AS v
            ON g.gebruikersnaam = v.gebruikersnaam
            WHERE v.gebruikersnaam = :gebruikersnaam');
    $query->execute(array(':gebruikersnaam' => $username));
    return $query->fetch(PDO::FETCH_OBJ);
}


?>
