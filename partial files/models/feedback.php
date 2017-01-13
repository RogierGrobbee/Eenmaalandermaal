<?php
require_once ('databaseString.php');

function isFeedbackGiven($voorwerp){
    global $db;

    $query = $db->prepare("SELECT * FROM feedback WHERE voorwerpnummer = :voorwerpnummer");

    $query->execute(array(':voorwerpnummer' => $voorwerp));
    return $query->fetch(PDO::FETCH_OBJ);
}

function insertFeedbackKoper($voorwerp, $gebruiker, $feedbacksoort, $commentaar) {
    global $db;

    $query = $db->prepare("INSERT INTO feedback VALUES (:voorwerpnummer, :gebruikersnaam, 
    :feedbacksoort, getdate(), :commentaar)");

    if($query->execute(array(
        ':voorwerpnummer' => $voorwerp,
        ':gebruikersnaam' => $gebruiker,
        ':feedbacksoort' => $feedbacksoort,
        ':commentaar' => $commentaar
    ))){
        return true;
    }
    return false;
}
?>