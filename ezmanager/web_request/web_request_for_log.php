<?php

require_once 'web_request.php';
require_once __DIR__.'/../../commons/config.inc';

if(false && !isValidCaller()) {
    die;
}

$input = array_merge($_GET, $_POST);

print_r($input);

// Witch action
switch($input['action']) {
    case "push_logs":
        // input -> log_data
        push_log();
        break;
    
    case "last_log_sent":
        last_log_sent();
        break;
    
    
}

//////////// PUSH ////////////

/**
 * Push log into the data base
 * Information to push must be on input['log_data']
 * 
 * echo SUCCESS if all is ok, ERROR (and informations) else
 */
function push_log() {
    global $input;
    
    if(!array_key_exists('log_data', $input)) {
        echo "ERROR 0";
        die;
    }

    $infoJSON = json_decode($input['log_data']);

    if(json_last_error() != JSON_ERROR_NONE) {
        echo "ERROR 1";
        die;
    }

    require_once __DIR__.'/../../commons/lib_database.php';
    global $db_object;
    
    foreach ($infoJSON as $log) {
        if(!insertLog($log)) {
            echo "ERROR 2 ";
            print_r($db_object->errorInfo());
            die;
        }
    }

    echo "SUCCESS";

}

/**
 * Check if the column is valide (in the data base)
 * 
 * @param String $col to test
 * @return True if the data base have this column
 */
function isColName($col) {
    return in_array($col, array('asset', 'origin', 'asset_classroom_id', 
        'asset_course', 'asset_author', 'asset_cam_slide', 'event_time', 
        'type_id', 'context', 'loglevel', 'message'));
}

/**
 * Call SQL data base to save the log
 * 
 * @global PDO $db_object
 * @param stdClass $objToInsert informations to insert
 * @return true if the log have been insert
 */
function insertLog($objToInsert) {
    
    $arrayToInsert = get_object_vars($objToInsert);
    
    foreach (array_keys($arrayToInsert) as $col) {
        echo "col: ".$col."<br />";
        if(!isColName($col)) {
            return false;
        }
    }
    
    
    $strSQL = 'INSERT INTO '.db_gettable('events') . '
            (asset, origin, asset_classroom_id, asset_course, asset_author, 
            asset_cam_slide, event_time, type_id, context, loglevel, message)
            VALUES (:asset, :origin, :asset_classroom_id, :asset_course, :asset_author, 
            :asset_cam_slide, :event_time, :type_id, :context, :loglevel, :message)';
    global $db_object;
    
    $reqSQL = $db_object->prepare($strSQL);
    return $reqSQL->execute($arrayToInsert);
}


//////////// LAST SEND LOG ////////////

/**
 * Get last timestamp sent for this classroom
 * @global $input
 * 
 * Information to push must be on input['classroom_id']
 */
function last_log_sent() {
    global $input;
    
    if(!array_key_exists('classroom_id', $input)) {
        echo "ERROR 0";
        die;
    }

    $classroom_id = $input['classroom_id'];
    
    require_once __DIR__.'/../../commons/lib_database.php';
    global $db_object;
    
    db_prepare();
    
    // Logger::EVENT_TABLE_NAME
    $strSQL = 'SELECT event_time FROM '.  db_gettable("events") . ' 
            WHERE asset_classroom_id = :asset_classroom_id
            ORDER BY event_time DESC 
            LIMIT 1';
    
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->bindParam(":asset_classroom_id", $classroom_id);
    $reqSQL->execute();
    
    $res = $reqSQL->fetch();
    echo $res['event_time'];
}
