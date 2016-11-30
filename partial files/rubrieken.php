<?php
include_once('databaseconnection.php');
$query = $db->query('SELECT * FROM rubriek ORDER BY volgnr, rubrieknaam');
$rubriekArray = array();
$huidigeRubriek = null;
//lijst wordt gevult met alle rubrieken
while ($rubriek = $query->fetch(PDO::FETCH_OBJ)) {
    array_push($rubriekArray, $rubriek);
}

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}
?>