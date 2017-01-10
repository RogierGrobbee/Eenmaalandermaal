<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:57
 */


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

//registerGebruiker instead of insertGebruiker because it does more than just inserting a gebruiker.
function registerGebruiker($gebruikersnaam, $voornaam, $achternaam, $adres, $postcode,
                         $plaatsnaam, $land, $geboortedatum, $email, $wachtwoord,
                         $vraag, $validatieCode, $antwoord){
    global $db;
    $sql = "INSERT INTO gebruiker (gebruikersnaam, voornaam, achternaam, adresregel1, postcode, plaatsnaam, land, geboortedatum, email, wachtwoord, verkoper, vraag, gevalideerd) VALUES
                (:username, :firstname, :lastname, :adres, :postcode, :plaatsnaam, :land, :geboortedatum, :email, :wachtwoord, :verkoper, :vraag, :gevalideerd)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', $gebruikersnaam, PDO::PARAM_STR);
    $stmt->bindValue(':firstname', $voornaam, PDO::PARAM_STR);
    $stmt->bindValue(':lastname', $achternaam, PDO::PARAM_STR);
    $stmt->bindValue(':adres', $adres, PDO::PARAM_STR);
    $stmt->bindValue(':postcode', $postcode, PDO::PARAM_STR);
    $stmt->bindValue(':plaatsnaam', $plaatsnaam, PDO::PARAM_STR);
    $stmt->bindValue(':land', $land, PDO::PARAM_STR);                 //////////////////
    $stmt->bindValue(':geboortedatum', $geboortedatum, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':wachtwoord', $wachtwoord, PDO::PARAM_STR);
    $stmt->bindValue(':verkoper', 0, PDO::PARAM_INT);
    $stmt->bindValue(':vraag', $vraag, PDO::PARAM_INT);                 //////////////////
    $stmt->bindValue(':gevalideerd', 0, PDO::PARAM_INT);
    $stmt->execute();

    $sql2 = "INSERT INTO validation (gebruikersnaam, validatiecode) VALUES
                (:gebruiker, :validate)";
    $stmt = $db->prepare($sql2);
    $stmt->bindValue(':gebruiker', $gebruikersnaam, PDO::PARAM_STR);
    $stmt->bindValue(':validate', $validatieCode, PDO::PARAM_STR);
    $stmt->execute();

    $sql3 = "INSERT INTO antwoord (vraagnummer, gebruikersnaam, antwoordtekst) VALUES
                (:nummer, :gebruikersnaam, :antwoord)";
    $stmt = $db->prepare($sql3);
    $stmt->bindValue(':nummer', $vraag, PDO::PARAM_STR);
    $stmt->bindValue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
    $stmt->bindValue(':antwoord', $antwoord, PDO::PARAM_STR);
    $stmt->execute();
}

// Replaces: doesUsernameAlreadyExist($username)
function doesUsernameExist($username) {
    global $db;

    $query = $db->prepare("SELECT gebruikersnaam FROM gebruiker WHERE gebruikersnaam = :gebruikersnaam");
    $query->execute(array(':gebruikersnaam' => $username));

    if($query->fetch(PDO::FETCH_OBJ)->gebruikersnaam == $username){
        return true;
    }
    return false;
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

function getVerkoperByVerkoopnummer($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email FROM gebruiker
                                WHERE gebruikersnaam in(
                                    SELECT verkoper FROM voorwerp where voorwerpnummer = :voorwerpnummer
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}

function getTopBidderByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT email from gebruiker WHERE gebruikersnaam in(
                                    SELECT TOP 1 gebruikersnaam FROM bod where voorwerpnummer = :voorwerpnummer
                                    ORDER BY gebruikersnaam ASC
                                )");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row;
}