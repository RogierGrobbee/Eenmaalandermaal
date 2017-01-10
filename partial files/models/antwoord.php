<?php

require_once ('databaseString.php');

function getAntwoordByUsername($username) {
    global $db;
    $query = $db->prepare("SELECT vraagnummer, gebruikersnaam, antwoordtekst
                                  FROM antwoord WHERE gebruikersnaam= :username ");
    $query->execute(array(':username' => $username));
    return $query->fetch(PDO::FETCH_OBJ);
}