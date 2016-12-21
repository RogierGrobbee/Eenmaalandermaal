<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 20-12-2016
 * Time: 12:58
 */

namespace refactor;

// Replaces: getVoorwerp($voorwerpId)
function getVoorwerpById($voorwerpnummer) {
    global $db;

    $query = $db->prepare("SELECT * FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer");
    $query->execute(array(':voorwerpnummer' => $voorwerpnummer));
    return $query->fetch(PDO::FETCH_OBJ);
}

// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function countVrwrpenBySTerm ($searchTerm) {
    global $db;

    $query = $db->prepare("execute sp_CountSearchVoorwerpenByTitle @search= :searchTerm");
    $query->execute(array(':searchTerm' => '%' . $searchTerm . '%'));

    return ($query->fetch(PDO::FETCH_OBJ))->amount;
}

// Replaces: Part of the loadVeilingItemsSearch($searchQuery, $currentPageNumber, $filter) function
function getVrwrpenByTitle ($searchTerm, $nSkippedRecords, $itemsPerPage, $filter) {
    global $db;

    $statement = $db->prepare("execute sp_SearchVoorwerpenByTitle @search = :searchTerm,
                                                                  @nSkippedRecords = :nSkippedRecords,
                                                                  @itemPerPage = :itemPerPage,
                                                                  @filter = :filter");

    $params = array(
        ':searchTerm' => '%' . $searchTerm . '%',
        ':nSkippedRecords' => $nSkippedRecords,
        ':itemPerPage' => $itemsPerPage,
        ':filter' => $filter,
    );

    return $statement->execute($params);
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
    $countQuery = $db->prepare("execute sp_CountVoorwerpenInRubrieken @ids = :ids");
    $countQuery->execute(array(':ids' => $idArray));

    return $countQuery->fetch(PDO::FETCH_OBJ);
}

function getVoorwerpenInRub($idArray, $nSkippedRecords, $itemsPerPage, $filter) {
    global $db;

    $voorwerpQuery = $db->prepare("execute sp_GetVoorwerpenInRubrieken @ids = :ids,
                                                                       @nSkippedRecords = :nSkippedRecords,
                                                                       @itemPerPage = :itemPerPage,
                                                                       @filter = :filter");

    $voorwerpQuery->execute(array(':ids' => $idArray,
        ':nSkippedRecords' => $nSkippedRecords,
        ':itemPerPage' => $itemsPerPage,
        ':filter' => $filter));

    return $voorwerpQuery->fetchAll();
}

// Replaces: loadVeilingItems($rubriekId, $currentPageNumber, $filter)
function loadVeilingItems($rubriekId, $currentPageNumber, $filter) {
    global $itemsPerPage;
    $nSkippedRecords = (($currentPageNumber - 1) * $itemsPerPage);
    if (is_numeric($rubriekId)) {

        $rubriekenArray = loadRubrieken();
        $subRubriekenArray = array();
        $subsubRubriekenArray = array();
        $subsubsubRubriekenArray = array();
        foreach ($rubriekenArray as $k => $rubriek) {
            if ($rubriek->superrubriek == $rubriekId) {
                array_push($subRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subRubriekenArray)) {
                array_push($subsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }
        foreach ($rubriekenArray as $k => $rubriek) {
            if (in_array($rubriek->superrubriek, $subsubRubriekenArray)) {
                array_push($subsubsubRubriekenArray, $rubriek->rubrieknummer);
            }
        }

        $temp = array_merge($subRubriekenArray, $subsubRubriekenArray, $subsubsubRubriekenArray);
        $ids = implode(',', $temp);
        if ($ids == "") {
            $ids = "0";
        }
        $ids = $ids . ',' . $rubriekId;

        $totalItems = CountVoorwerpenInRubs($ids);

        $voorwerpen = getVoorwerpenInRub();

        //queryVoorwerpen($voorwerpQuery, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber);


    } else {
        echo 'Geen rubriek geselecteerd';
    }

}

// Replaces: queryVoorwerpen($query, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber)
function queryVoorwerpen($query, $rubriekId, $itemsPerPage, $totalItems, $currentPageNumber) {
    global $db;
    $query->execute();
    $voorwerpArray = array();
    while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

        array_push($voorwerpArray, $voorwerp);

        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvailable.jpg";

        $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);

        if($biedingen == null){
            $prijs = $voorwerp->startprijs;
        }
        else{
            $prijs = $biedingen[0]->bodbedrag;
        }

        echoVoorwerp($voorwerp, $prijs, $image);
    }

    if (count($voorwerpArray) < 1) {
        echo "Geen voorwerpen gevonden";
    }

    //Pagina nummers VVVV

    if ($totalItems > $itemsPerPage) {
        $nPages = ceil($totalItems / $itemsPerPage);
        echo '<div class="row">
            <div class="col-sm-12">
            ';
        if ($currentPageNumber > 1) {
            echo("<button onclick=\"location.href='./rubriek.php?rubriek=" . $rubriekId . "&page=" . ($currentPageNumber - 1) . "'\">Previous</button>");
        }
        if ($nPages > 9) {
            if ($currentPageNumber < 6) {
                for ($i = 1; $i < 10; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber, $rubriekId);
            } else if ($currentPageNumber > ($nPages - 5)) {
                echoPageNumber(1, $currentPageNumber, $rubriekId);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
            } else {
                echoPageNumber(1, $currentPageNumber, $rubriekId);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                    echoPageNumber($i, $currentPageNumber, $rubriekId);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber, $rubriekId);
            }

        } else {
            for ($i = 1; $i < $nPages + 1; $i++) {
                echoPageNumber($i, $currentPageNumber, $rubriekId);
            }
        }
        if ($currentPageNumber < $nPages) {
            echo("<button onclick=\"location.href='./rubriek.php?rubriek=" . $rubriekId . "&page=" . ($currentPageNumber + 1) . "'\">Next</button>");
        }
        echo '</div></div>';
    }

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
    $bodQuery = $db->prepare('SELECT v.voorwerpnummer, v.titel, b.bodbedrag, b.bodtijdstip FROM voorwerp as v full outer join bod as b on v.voorwerpnummer = b.voorwerpnummer where b.gebruikersnaam =? Order by b.bodtijdstip desc');
    $bodQuery->bindParam(1, $username);
    $bodQuery->execute();
    return $bodQuery->fetchAll(PDO::FETCH_OBJ);
}

//Replaces: veilingEnded($voorwerpnummer)
function veilingEnded($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("SELECT isVoltooid FROM voorwerp WHERE voorwerpnummer = :voorwerpnummer ");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
    $row = $statement->fetch();
    return $row['isVoltooid'];
}

//Replaces: endVeilingByVoorwerpnummer($voorwerpnummer)
function endVeilingByVnr($voorwerpnummer) {
    global $db;
    $statement = $db->prepare("UPDATE voorwerp SET isVoltooid = 1 WHERE voorwerpnummer = :voorwerpnummer");
    $statement->execute(array(':voorwerpnummer' => $voorwerpnummer));
}