<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 11:05
 */

namespace refactor;

require_once ('databaseString.php');

// Replaces: getPhoneNumbers($username)
function getPhoneNumbers($username) {
    global $db;
    $query = $db->prepare('select * from gebruikerstelefoon
                                  where gebruikersnaam = :gebruikersnaam
                                  order by volgnr');
    $query->execute(array(':gebruikersnaam' => $username));
    return $query->fetchAll(PDO::FETCH_OBJ);
}

// Replaces: addPhoneNumber($volgnr, $username, $phonenumber)
function insertPhoneNumber($volgnr, $username, $phonenumber) {
    global $db;
    $query = $db->prepare('insert into gebruikerstelefoon (volgnr,gebruikersnaam,telefoon)
                                values(:volgnr, :gebruikersnaam, :telefoon)');
    return $query->execute(array(
        ':volgnr' => $volgnr,
        ':gebruikersnaam' => $username,
        ':telefoon' => $phonenumber
    ));
}