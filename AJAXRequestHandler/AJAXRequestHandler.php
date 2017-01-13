<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 19-12-2016
 * Time: 11:14
 */

/*
 * This file is used to invoke the right function according to the AJAX.
 * The AJAX NEEDS to send action to the server. The data in the Javascript needs this as a bare minimum:
 * { action: 'someAction' }
 * When a new action needs to be added it needs to be added to the switch statement in this file.
 */

include_once('endVeiling.php');
include_once('..\partial files\models\rubriek.php');

/**
 * Converts the output of 'file_get_contents('php://input')' to a hash table.
 * @return array The php://input in a hash table.
 */
function fileContToHashTable() {
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

/**
 * Checks if the posted data is legal (if the variable is not empty and set).
 * @param $postData The variable to check .
 * @return bool True if the given value is Legal, false if it's not.
 */
function isLegalPostData($postData) {
    return isset ($postData) && !empty($postData);
}

$content = fileContToHashTable();

$action = $content['action'];

if (isLegalPostData($action)) {
    switch ($action) {
        case 'endAuction':
            if (isLegalPostData($content['voorwerpnummer'])) {
                endAuction($content['voorwerpnummer']);
            }
            break;
        case 'getSubrubrieken':
            if (isLegalPostData($content['rubrieknummer'])) {
                echo json_encode(getRubriekenBySuperrubriek($content['rubrieknummer']));
            }
    }
}