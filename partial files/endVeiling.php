<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 16-12-2016
 * Time: 10:16
 */

include_once('databaseconnection.php');


function isLegalPostData($postData) {
    return isset ($postData) && !empty($postData);
}

function endVeiling($voorwerpId) {
    if (!veilingEnded($voorwerpId)) {
        endVeilingByVoorwerpId($voorwerpId);
    }
}

//$voorwerpnummer = $_POST['voorwerpId'];

echo 'Length $_GET: ' . count($_GET) . ' ';
echo 'Length $_POST: ' . count($_POST) . ' ';
echo 'Length $_REQUEST: ' . count($_REQUEST) . ' ';

//echo $voorwerpnummer;

/*if (isLegalPostData($_POST['voorwerp'])) {
    endVeiling($_POST['voorwerp']);
}/*

/*if (isLegalPostData($_GET['voorwerpnummer'])) {
    endVeiling($_GET['voorwerpnummer']);
}*/

?>