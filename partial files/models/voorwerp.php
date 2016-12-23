<?php

require_once ('databaseString.php');

// Replaces: getVoorwerp($voorwerpId)
function getVoorwerpById($voorwerpnummer) {
    global $db;

    $query = $db->prepare("SELECT * FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    return $query->fetch(PDO::FETCH_OBJ);
}

// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function countVrwrpenBySTerm ($searchTerm, $searchCount) {
    global $db;

    $query = $db->prepare("execute sp_CountSearchVoorwerpenByTitle @search= :searchTerm,
                                                                   @searchCount = :searchCount");
    $query->execute(array(
        ':searchTerm' => $searchTerm,
        ':searchCount' => $searchCount
        ));

    return $query->fetch(PDO::FETCH_OBJ);

}

// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function getVrwrpenSearch ($searchTerm, $searchCount, $nSkippedRecords, $itemsPerPage, $filter) {
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

//TODO: These functions need to be executed in the search page.
/*
function echoPagination($totalItems, $itemsPerPage, $currentPageNumber, $searchTerm) {
    $nPages = ceil($totalItems / $itemsPerPage);
    echo '<div class="row">
            <div class="col-sm-12">
            ';
    if ($currentPageNumber > 1) {
        echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchTerm . "&page=" . ($currentPageNumber - 1) . "'\">Previous</button>");
    }
    if ($nPages > 9) {
        if ($currentPageNumber < 6) {
            for ($i = 1; $i < 10; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        } else if ($currentPageNumber > ($nPages - 5)) {
            echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
        } else {
            echoSearchPageNumber(1, $currentPageNumber, $searchTerm);
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
            }
            echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
            echoSearchPageNumber($nPages, $currentPageNumber, $searchTerm);
        }

    } else {
        for ($i = 1; $i < $nPages + 1; $i++) {
            echoSearchPageNumber($i, $currentPageNumber, $searchTerm);
        }
    }
    if ($currentPageNumber < $nPages) {
        echo("<button onclick=\"location.href='./zoeken.php?search=" . $searchTerm . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
    }
    echo '</div></div>';
}

function echoSearchPageNumber($pageNumber, $currentPageNumber, $search)
{
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./zoeken.php?search=' . $search . '&page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}
*/

function countVoorwerpenInRubs($idArray) {
    global $db;
    $query = $db->prepare("execute sp_CountVoorwerpenInRubrieken @ids = :ids");
    $query->execute(array(':ids' => $idArray));

    return $query->fetch(PDO::FETCH_OBJ);
}

function getVoorwerpenInRub($idArray, $nSkippedRecords, $itemsPerPage, $filter) {
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
function endVeilingByVnr($voorwerpnummer) {
    global $db;
    $query = $db->prepare("UPDATE voorwerp SET isVoltooid = 1 WHERE voorwerpnummer = :voorwerpnummer");
    return $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
}