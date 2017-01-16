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

function getTop3Ratings($username){
    global $db;
    $query = $db->prepare("SELECT top 3 feedbacksoort, f.gebruikersnaam, f.dagtijdstip, f.commentaar from voorwerp as v inner join feedback as f on v.voorwerpnummer = f.voorwerpnummer where v.verkoper=? order by f.dagtijdstip desc");
    $query->bindParam(1, $username);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

function getRatingsByUser($username){
    global $db;

    $query = $db->prepare("SELECT feedbacksoort, count(f.feedbacksoort) as aantal from voorwerp as v inner join feedback as f on v.voorwerpnummer = f.voorwerpnummer where v.verkoper=? group by f.feedbacksoort");
    $query->bindParam(1, $username);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}
?>