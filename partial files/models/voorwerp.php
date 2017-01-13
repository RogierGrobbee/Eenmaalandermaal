<?php

require_once ('databaseString.php');

// Replaces: getVoorwerp($voorwerpId)
function getVoorwerpById($voorwerpnummer) {
    global $db;

    $query = $db->prepare("SELECT * FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    return $query->fetch(PDO::FETCH_OBJ);
}

function countVoorwerpenByVerkoper($username){
    global $db;

    $query = $db->prepare("select count(voorwerpnummer) as amount from voorwerp where verkoper = :username");
    $query->execute(array(':username' => $username));
    return ($query->fetch(PDO::FETCH_OBJ))->amount;
}

function getVoorwerpenByVerkoper($username, $page, $itemsPerPage) {
    global $db;
    $nSkippedRecords = ($page - 1) * $itemsPerPage;

    $query = $db->prepare("execute sp_getVeilingenByVerkoper @username=?, @skippedRecords=?, @itemsPerPage=?");
    $query->bindParam(1, $username);
    $query->bindParam(2, $nSkippedRecords);
    $query->bindParam(3, $itemsPerPage);

    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}


// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function countVoorwerpenBySearchTerm ($searchTerm, $searchCount) {
    global $db;

    $query = $db->prepare("execute sp_CountSearchVoorwerpenByTitle @search= :searchTerm,
                                                                   @searchCount = :searchCount");
    $query->execute(array(
        ':searchTerm' => $searchTerm,
        ':searchCount' => $searchCount
        ));

    return ($query->fetch(PDO::FETCH_OBJ))->amount;
}

// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function getVoorwerpenBySearch ($searchTerm, $searchCount, $nSkippedRecords, $itemsPerPage, $filter) {
    global $db;

    $query = $db->prepare("execute sp_SearchVoorwerpenByTitle @search= :searchTerm,
                                                                   @searchCount = :searchCount,
                                                                   @nSkippedRecords = :nSkippedRecords,
                                                                   @itemPerPage = :itemsPerPage,
                                                                   @filter = :filter");

    $query->execute(array(
        ':searchTerm' => '%' . $searchTerm . '%',
        ':searchCount' => $searchCount,
        ':nSkippedRecords' => $nSkippedRecords,
        ':itemsPerPage' => $itemsPerPage,
        ':filter' => $filter,
    ));

    return $query->fetchAll(PDO::FETCH_OBJ);
}

function countVoorwerpenInRubrieken($idArray) {
    global $db;
    $query = $db->prepare("execute sp_CountVoorwerpenInRubrieken @ids = :ids");
    $query->execute(array(':ids' => $idArray));

    return $query->fetch(PDO::FETCH_OBJ)->amount;
}

function getVoorwerpenInRubriek($idArray, $nSkippedRecords, $itemsPerPage, $filter) {
    global $db;

    $query = $db->prepare("execute sp_GetVoorwerpenInRubrieken @ids = :ids,
                                                                       @nSkippedRecords = :nSkippedRecords,
                                                                       @itemPerPage = :itemPerPage,
                                                                       @filter = :filter");

    $query->execute(array(':ids' => $idArray,
        ':nSkippedRecords' => $nSkippedRecords,
        ':itemPerPage' => $itemsPerPage,
        ':filter' => $filter));

    return $query->fetchAll(PDO::FETCH_OBJ);
}

//Replaces: featuredVoorwerp()
function getFeaturedVoorwerp()
{
    global $db;

    $query = $db->query("SELECT TOP 1 v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                v.looptijdeindeveiling FROM voorwerp as v 
                                FULL OUTER JOIN Bod as b ON v.voorwerpnummer=b.voorwerpnummer 
                                WHERE v.looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE()) 
								GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,v.looptijdeindeveiling
								ORDER BY count(b.voorwerpnummer) DESC");

    return $query->fetch(PDO::FETCH_OBJ);
}

function getVoorwerpenByQuery($query){
    global $db;

    $query = $db->query($query);

    return $query->fetchAll(PDO::FETCH_OBJ);
}

function getSuggestedVoorwerpen($rubrieknummer){
    global $db;

    $query = $db->query("SELECT TOP 4 * FROM VOORWERP V INNER JOIN voorwerpinrubriek vr ON
                                        v.voorwerpnummer = vr.voorwerpnummer
                                        where vr.rubriekoplaagsteniveau=" . $rubrieknummer);

    return $query->fetchAll(PDO::FETCH_OBJ);
}


//Replaces: getBiedingenByUsername($username)
function getBiedingenByUsername($username) {
    global $db;
    $query = $db->prepare('SELECT v.voorwerpnummer, v.titel, v.looptijdeindeveiling, v.beschrijving, b.bodbedrag, b.bodtijdstip FROM voorwerp as v full outer join bod as b on v.voorwerpnummer = b.voorwerpnummer where b.gebruikersnaam =? Order by b.bodtijdstip desc');
    $query->bindParam(1, $username);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

//Replaces: veilingEnded($voorwerpnummer)
function veilingEnded($voorwerpnummer) {
    global $db;
    $query = $db->prepare("SELECT isVoltooid FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer ");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    return ($query->fetch(PDO::FETCH_OBJ));
}

//Replaces: endVeilingByVoorwerpnummer($voorwerpnummer)
function endVeilingByVoorwerpnummer($voorwerpnummer) {
    global $db;
    $query = $db->prepare("UPDATE voorwerp SET isVoltooid = 1 WHERE voorwerpnummer = :voorwerpnummer");
    return $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
}