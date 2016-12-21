<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 21-12-2016
 * Time: 10:07
 */

namespace refactor;

// Replaces: getVoorwerpBiedingen($voorwerpnummer)
function getBiedingenByVnr($voorwerpnummer){
    global $db;

    $query = $db->prepare("SELECT * FROM bod WHERE voorwerpnummer = :voorwerpnummer ORDER BY bodbedrag DESC");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));

    return $query->fetchAll(PDO::FETCH_OBJ);
}

function insertBod($voorwerp, $amount, $gebruiker) {
    global $db;

    $return= new stdClass(
        $bodSuccesful = false
    );

    $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);
    if($biedingen == null){
        if($biedingen < $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs)){
            $return->bodSuccesful = false;
            $return->message = "U moet minimaal €".calculateIncrease($voorwerp->startprijs)." hoger bieden!";
            return $return;
        }
    }
    else{
        if($amount < $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag)){
            $return->bodSuccesful = false;
            $return->message = "U moet minimaal €".calculateIncrease($voorwerp->startprijs)." hoger bieden!";
            return $return;
        }
        else if($gebruiker == $biedingen[0]->gebruikersnaam){
            $return->bodSuccesful = false;
            $return->message = "U heeft al het hoogste bod!";
            return $return;
        }
    }

    $query = $db->query("INSERT INTO bod VALUES (".$voorwerp->voorwerpnummer.", ".$amount.", '".$gebruiker."', getdate())");
    if($query){
        $return->bodSuccesful = true;
        return $return;
    }

    $return->bodSuccesful = false;
    $return->message = "Er kan niet hoger geboden worden dan 100.000!";
    return $return;
}