<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:57
 */

namespace refactor;

require_once ('databaseString.php');

// Replaces: getUserByUsername($username)
function getUserByUsername($username) {
    global $db;
    $query = $db->prepare('SELECT * FROM gebruiker where gebruikersnaam= :gebruikersnaam');
    $query->execute(array(':gebruikersnaam' => $username));
    return $query->fetch(PDO::FETCH_OBJ);
}

// Replaces: validateUser($code)
function validateUser($code) {
    global $db;
    $query = $db->prepare("UPDATE g
            SET g.gevalideerd = 1
            FROM gebruiker AS g
            INNER JOIN validation AS v
            ON g.gebruikersnaam = v.gebruikersnaam
            WHERE v.validatiecode  = :validatie");
    return $query->execute(array(':validatie' => $code));
}

// Replaces: addPhoneNumber($volgnr, $username, $phonenumber)
function addPhoneNumber($volgnr, $username, $phonenumber) {
    global $db;
    $query = $db->prepare('insert into gebruikerstelefoon (volgnr,gebruikersnaam,telefoon)
                                  values(:volgnr,:gebruikersnaam,:telefoon)');
    return $query->execute(array(
            ':volgnr' => $volgnr,
            ':gebruikersnaam' => $username,
            ':telefoon' => $phonenumber
        ));
}

// Replaces: doesUsernameAlreadyExist($username)
function doesUsernameExist($username) {
    global $db;

    $query = $db->prepare("SELECT gebruikersnaam FROM gebruiker WHERE gebruikersnaam = :gebruikersnaam");
    $query->execute(array(':gebruikersnaam' => $username));

    return !is_null($query->fetch(PDO::FETCH_OBJ)) ? true : false;
}

// Replaces: getPassword($username)
function getPassword($username) {
    global $db;
    $query = $db->prepare("SELECT wachtwoord FROM gebruiker WHERE gebruikersnaam= :username ");
    $query->execute(array(':username' => $username));

    return ($query->fetch(PDO::FETCH_OBJ))->wachtwoord;
}

// Replaces: getValidation($username)
function getValidation($username) {
    global $db;
    $query = $db->prepare("SELECT gevalideerd FROM gebruiker WHERE gebruikersnaam= :username ");
    $query->execute(array(':username' => $username));

    return ($query->fetch(PDO::FETCH_OBJ))->gevalideerd;
}

// Replaces: getEmail($username)
function getEmail($username) {
    global $db;
    $statement = $db->prepare("SELECT email FROM gebruiker WHERE gebruikersnaam= :username");
    $statement->execute(array(':username' => $username));
    $row = $statement->fetch();
    return $row['email'];
}

// Replaces: hashPass($pass)
function hashPass($pass) {
    $options = [
        'cost' => 12,
        'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
    ];
    return password_hash($pass, PASSWORD_BCRYPT, $options);
}

function getVerkoperByVnr($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email FROM gebruiker
                                WHERE gebruikersnaam in(
                                    SELECT verkoper FROM voorwerp where voorwerpnummer = :voorwerpnummer
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}

function getTopBidderByVnr($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email from gebruiker WHERE gebruikersnaam in(
                                    SELECT TOP 1 gebruikersnaam FROM bod where voorwerpnummer = :voorwerpnummer
                                    ORDER BY gebruikersnaam ASC
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}