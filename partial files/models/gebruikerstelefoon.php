<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 11:05
 */

namespace refactor;

// Replaces: getPhoneNumbers($username)
function getPhoneNumbers($username) {
    global $db;
    $phoneQuery = $db->prepare('select * from gebruikerstelefoon where gebruikersnaam =? order by volgnr');
    $phoneQuery->bindParam(1, $username);
    $phoneQuery->execute();
    return $phoneQuery->fetchAll(PDO::FETCH_OBJ);
}

// Replaces: addPhoneNumber($volgnr, $username, $phonenumber)
function insertPhoneNumber($volgnr, $username, $phonenumber) {
    global $db;
    $phoneQuery = $db->prepare('insert into gebruikerstelefoon (volgnr,gebruikersnaam,telefoon) values(?,?,?)');
    $phoneQuery->bindParam(1, $volgnr);
    $phoneQuery->bindParam(2, $username);
    $phoneQuery->bindParam(3, $phonenumber);
    $phoneQuery->execute();
}