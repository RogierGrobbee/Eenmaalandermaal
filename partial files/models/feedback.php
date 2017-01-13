<?php
require_once ('databaseString.php');

function insertFeedbackKoper($voorwerp, $gebruiker, $feedbacksoort, $commentaar) {
    global $db;

    $query = $db->prepare("INSERT INTO bod(voorwerpnummer, gebruikersnaam, feedbacksoort, commentaar) VALUES 
    (:voorwerpnummer, :gebruikersnaam , :feedbacksoort , :commentaar)");

    if($query->execute(array(
        ':voorwerpnummer' => $voorwerp->voorwerpnummer,
        ':gebruiker' => $gebruiker,
        ':feedbacksoort' => $feedbacksoort,
        ':commentaar' => $commentaar
    ))){
        return true;
    }
    return false;
}
?>