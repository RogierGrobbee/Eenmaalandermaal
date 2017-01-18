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
function validateUser($code)
{
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
                         $vraag, $telefoon, $validatieCode, $antwoord){
    global $db;
    $sql = "INSERT INTO gebruiker (gebruikersnaam, voornaam, achternaam, adresregel1, postcode, plaatsnaam, land, geboortedatum, email, wachtwoord, verkoper, vraag, gevalideerd) VALUES
                (:username, :firstname, :lastname, :adres, :postcode, :plaatsnaam, :land, :geboortedatum, :email, :wachtwoord, :verkoper, :vraag, :gevalideerd)";
    $query = $db->prepare($sql);
    $query->bindValue(':username', $gebruikersnaam, PDO::PARAM_STR);
    $query->bindValue(':firstname', $voornaam, PDO::PARAM_STR);
    $query->bindValue(':lastname', $achternaam, PDO::PARAM_STR);
    $query->bindValue(':adres', $adres, PDO::PARAM_STR);
    $query->bindValue(':postcode', $postcode, PDO::PARAM_STR);
    $query->bindValue(':plaatsnaam', $plaatsnaam, PDO::PARAM_STR);
    $query->bindValue(':land', $land, PDO::PARAM_STR);                 //////////////////
    $query->bindValue(':geboortedatum', $geboortedatum, PDO::PARAM_STR);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':wachtwoord', $wachtwoord, PDO::PARAM_STR);
    $query->bindValue(':verkoper', 0, PDO::PARAM_INT);
    $query->bindValue(':vraag', $vraag, PDO::PARAM_INT);
    $query->bindValue(':gevalideerd', 0, PDO::PARAM_INT);
    $query->execute();

    $sql2 = "INSERT INTO validation (gebruikersnaam, validatiecode) VALUES
                (:gebruiker, :validate)";
    $query = $db->prepare($sql2);
    $query->bindValue(':gebruiker', $gebruikersnaam, PDO::PARAM_STR);
    $query->bindValue(':validate', $validatieCode, PDO::PARAM_STR);
    $query->execute();

    $sql3 = "INSERT INTO antwoord (vraagnummer, gebruikersnaam, antwoordtekst) VALUES
                (:nummer, :gebruikersnaam, :antwoord)";
    $query = $db->prepare($sql3);
    $query->bindValue(':nummer', $vraag, PDO::PARAM_STR);
    $query->bindValue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
    $query->bindValue(':antwoord', $antwoord, PDO::PARAM_STR);
    $query->execute();

    $sql4 = "INSERT INTO gebruikerstelefoon (volgnr, gebruikersnaam, telefoon) VALUES
                (:nummer, :gebruikersnaam, :tel)";
    $query = $db->prepare($sql4);
    $query->bindValue(':nummer', 0, PDO::PARAM_STR);
    $query->bindValue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
    $query->bindValue(':tel', $telefoon, PDO::PARAM_STR);
    $query->execute();
}

// Replaces: doesUsernameAlreadyExist($username)
function doesUsernameExist($username) {
    global $db;

    $query = $db->prepare("SELECT gebruikersnaam FROM gebruiker WHERE gebruikersnaam = :gebruikersnaam");
    $query->execute(array(':gebruikersnaam' => $username));

    if($query->fetch(PDO::FETCH_OBJ)->gebruikersnaam == "username"){
        return true;
    }
    else{
        return false;
    }
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
    $query = $db->prepare("SELECT email FROM gebruiker WHERE gebruikersnaam= :username");
    $query->execute(array(':username' => $username));
    $row = $query->fetch();
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

function updateWachtwoord($username, $hash) {
    global $db;
    $query = $db->prepare("update gebruiker set wachtwoord=? where gebruikersnaam=?");
    $query->bindParam(1, $hash);
    $query->bindParam(2, $username);
    $query->execute();
}

function getVerkoperByVerkoopnummer($voorwerpnummer) {
    global $db;
    $query = $db->prepare("SELECT email FROM gebruiker
                                WHERE gebruikersnaam in(
                                    SELECT verkoper FROM voorwerp where voorwerpnummer = :voorwerpnummer
                                )");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $query->fetch(PDO::FETCH_OBJ);
    return $row;
}

function getTopBidderByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $query = $db->prepare("SELECT gebruikersnaam, email from gebruiker WHERE gebruikersnaam in(
                                    SELECT TOP 1 gebruikersnaam FROM bod where voorwerpnummer = :voorwerpnummer
                                    ORDER BY bodbedrag DESC
                                )");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $query->fetch(PDO::FETCH_OBJ);
    return $row;
}

function IsGebruikerVerkoper($username)
{
    global $db;
    $query = $db->prepare('SELECT verkoper FROM gebruiker WHERE gebruikersnaam = :gebruikersnaam');
    $query->execute(array(':gebruikersnaam' => $username));
    $row = $query->fetch();
    return $row['verkoper'];
}