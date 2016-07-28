<?php

require_once 'web_request.php';
require_once __DIR__.'/../../commons/config.inc';

if(false && !isValidCaller()) {
    die;
}

$input = array_merge($_GET, $_POST);

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
    
    $strSQL = 'INSERT INTO '.db_gettable('events') . '
        (asset, origin, asset_classroom_id, asset_course, asset_author, 
        asset_cam_slide, event_time, type_id, context, loglevel, message)
        VALUES (:asset, :origin, :asset_classroom_id, :asset_course, :asset_author, 
        :asset_cam_slide, :event_time, :type_id, :context, :loglevel, :message)';
    global $db_object;
    
    $reqSQL = $db_object->prepare($strSQL);
    $error = false;
    $lastInsert = -1;
    $source = NULL;
    
    foreach ($infoJSON as $log) {
        
        $arrayToInsert = get_object_vars($log);
        
        if(!array_key_exists('id', $arrayToInsert)) {
            echo "ERROR 2";
            $error = true;
            break;
        }
        
        if(!array_key_exists('asset_classroom_id', $arrayToInsert) || 
                $arrayToInsert['asset_classroom_id'] == "") {
            echo "ERROR 5";
            $error = true;
            break;
        }
        $source = $arrayToInsert['asset_classroom_id'];
        
        
        if(isAllColValid($arrayToInsert)) {
            $lastInsert = $arrayToInsert['id'];
            unset($arrayToInsert['id']);
            $reqSQL->execute($arrayToInsert);
        } else {
            $error = true;
            break;
        }
    }
    
    if($lastInsert >= 0) {
        updateLastInsert($lastInsert, $source);
        if(!$error) {
            echo "SUCCESS";
        }
    } else {
        echo "ERROR 4";
    }

}

/**
 * Check if the column is valide (in the data base)
 * 
 * @param String $col to test
 * @return True if the data base have this column
 */
function isColName($col) {
    return in_array($col, array('id', 'asset', 'origin', 'asset_classroom_id', 
        'asset_course', 'asset_author', 'asset_cam_slide', 'event_time', 
        'type_id', 'context', 'loglevel', 'message'));
}

/**
 * Check if all the column is valid
 * 
 * @param Array $arrayToTest list of the column name
 * @return boolean True is all is ok else False (and echo the errors)
 */
function isAllColValid($arrayToTest) {
    foreach (array_keys($arrayToTest) as $col) {
        if(!isColName($col)) {
            echo "ERROR 3 - ";
            echo "Col ".$col." not good - ";
            print_r($db_object->errorInfo());
            return false;
        }
    }
    return true;
}



/**
 * Call SQL data base to save the last insert ID
 * 
 * @global PDO $db_object
 * @param int $lastInsert last id insert
 * @param String $source of the machine who send log
 * @return true if the id have been insert
 */
function updateLastInsert($lastInsert, $source) {
    
    $strSQL = 'INSERT INTO '.db_gettable('event_last_indexes') . '
        (source, id) VALUES(:source, :id) 
        ON DUPLICATE KEY UPDATE 
        id = :id';
    global $db_object;
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->bindParam(':source', $source);
    $reqSQL->bindParam(':id', $lastInsert);
    return $reqSQL->execute();
}


//////////// LAST SEND LOG ////////////

/**
 * Get last timestamp sent for this classroom
 * @global $input
 * 
 * Information to push must be on input['source']
 */
function last_log_sent() {
    global $input;
    
    if(!array_key_exists('source', $input)) {
        echo "ERROR 0";
        die;
    }

    $source = $input['source'];
    
    require_once __DIR__.'/../../commons/lib_database.php';
    global $db_object;
    
    db_prepare();
    
    $strSQL = 'SELECT id FROM '.  db_gettable("event_last_indexes") . ' 
            WHERE source = :source
            LIMIT 1';
    
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->bindParam(":source", $source);
    $reqSQL->execute();
    
    $res = $reqSQL->fetch();
    if(empty($res)) {
        echo -1;
    } else {
        echo $res['id'];
    }
}
