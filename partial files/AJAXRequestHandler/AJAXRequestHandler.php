<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 19-12-2016
 * Time: 11:14
 */

include_once('endVeiling.php');

function fileContentsToHashTable() {
    $content = file_get_contents('php://input');
    $contentHashTable = array();

    foreach (explode('&', $content) AS &$data) {
        $dataSplit = explode('=', $data);
        $key = $dataSplit[0];
        $val = $dataSplit[1];
        $contentHashTable[$key] = $val;
    }

    return $contentHashTable;
}

function isLegalPostData($postData) {
    return isset ($postData) && !empty($postData);
}

$content = fileContentsToHashTable();

$action = $content['action'];

if (isLegalPostData($action)) {
    switch ($action) {
        case 'endVeiling':
            if (isLegalPostData($content['voorwerpnummer'])) {
                endVeiling($content['voorwerpnummer']);
            }
            break;
    }
}