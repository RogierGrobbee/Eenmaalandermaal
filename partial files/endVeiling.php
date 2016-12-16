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

if ((isLegalPostData($_POST['action']) && $action == 'endVeiling')
    && isLegalPostData($_POST['voorwerpId'])) {
    eindVeiling($_POST['voorwerpId']);
}


function endVeiling($voorwerpId) {
    if (veilingEnded($voorwerpId)) {
        echo 'Veiling is al beëindigd';
    }
}

?>