<?php
require_once ('databaseString.php');

// Replaces: part of the calculateExpire($code) function
function getDatumTijdByValicationCode($code) {
    global $db;
    $query = $db->prepare("select datumTijd from validation where validatiecode = :validatiecode");
    $query->execute(array(':validatiecode' => $code));
    return $query->fetch(PDO::FETCH_OBJ);
}

// Replaces: doesValidationCodeExist($code)
function doesValidationCodeExist($code) {
    global $db;
    $query = $db->prepare("SELECT validatiecode FROM validation WHERE validatiecode = :code");
    $query->execute(array(':code' => $code));

    return !is_null($query->fetch(PDO::FETCH_OBJ)) ? true : false;

}