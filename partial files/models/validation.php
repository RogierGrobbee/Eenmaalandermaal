<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:50
 */

namespace refactor;

// Replaces: part of the calculateExpire($code) function
function getDTByValCode($code) {
    global $db;
    $statement = $db->prepare("select datumTijd from validation where validatiecode = :validatiecode");
    $statement->execute(array(':validatiecode' => $code));
    return $statement->fetch();
}

// Replaces: doesValidationCodeExist($code)
function doesValidationCodeExist($code) {
    global $db;
    $statement = $db->prepare("SELECT validatiecode FROM validation WHERE validatiecode = :code");
    $statement->execute(array(':code' => $code));
    $row = $statement->fetch();
    if (!$row) {
        return false;
    } else {
        return true;
    }

}