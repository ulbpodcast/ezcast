<?php

require_once 'web_request.php';
require_once __DIR__.'/../config.inc';

if (!is_authorized_caller()) {
    print "not talking to you ($caller_ip)";
    die;
}

$input = array_merge($_GET, $_POST);

// Witch action
switch ($input['action']) {
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
 * Push log into the database
 * Information to push must be on input['log_data']
 * This is used by recorders to sync their logs with server.
 *
 * echo SUCCESS if all is ok, else ERROR (and informations)
 */
function push_log()
{
    global $input;
    global $appname;
    
    if (!array_key_exists('log_data', $input)) {
        echo "ERROR 0";
        die;
    }

    $infoJSON = json_decode($input['log_data']);

    if (json_last_error() != JSON_ERROR_NONE) {
        echo "ERROR 1: " . json_last_error() ;
        die;
    }
    
    // Get last instert ID
    $lastInsert = -1;
    // Source of the log
    $source = "";
    
    $lastAsset = "";
    $listBugID = array();
    $lastDataError = array();
    
    foreach ($infoJSON as $log) {
        $arrayToInsert = get_object_vars($log);
        
        $tempSource = get_source($arrayToInsert);
        if ($tempSource != null) {
            $source = $tempSource;
        }
        if (!array_key_exists('id', $arrayToInsert)) {
            $msgLog = 'Log without any ID';
            if ($source != "") {
                $msgLog .= ' from '.$source;
            }
            
            save_log(
            
                'Service',
            
                $appname,
            
                LogLevel::get_log_level_integer(LogLevel::ALERT),
                    EventType::$event_type_id[EventType::MANAGER_LOG_SYNC],
            
                $source,
                    date('Y-m-d G:i:s'),
            
                $msgLog
            
            );
            continue;
        }
        $id = $arrayToInsert['id'];
        
        if ($tempSource == null) {
            $listBugID[$id] = 'Classroom id not defined';
            $lastDataError = $arrayToInsert;
            continue;
        }
        
        if (!array_key_exists('asset', $arrayToInsert) ||
                $arrayToInsert['asset'] == "") {
            $listBugID[$id] = 'No asset defined';
            $lastDataError = $arrayToInsert;
            continue;
        }
        $lastAsset = $arrayToInsert['asset'];
        
        // If there is an error in the data column
        if (!is_all_col_valid($arrayToInsert)) {
            $listBugID[$id] = 'Error with column name';
            $lastDataError = $arrayToInsert;
            continue;
        } else {
            if ($lastInsert < $arrayToInsert['id']) {
                $lastInsert = $arrayToInsert['id'];
            }
            save_log(
                $arrayToInsert['asset'],
                $arrayToInsert['origin'],
                    $arrayToInsert['loglevel'],
                $arrayToInsert['type_id'],
                    $arrayToInsert['asset_classroom_id'],
                $arrayToInsert['id'],
                $arrayToInsert['event_time'],
                    $arrayToInsert['message'],
                $arrayToInsert['asset_course'],
                    $arrayToInsert['asset_author'],
                $arrayToInsert['asset_cam_slide'],
                    $arrayToInsert['context']
            );
        }
    }
    
    if ($lastInsert >= 0) {
        update_last_inserted_index($lastInsert, $source);
    }
    
    if (!empty($lastDataError)) {
        $resMsg = "Error save log:\n";
        foreach ($listBugID as $id => $msg) {
            $resMsg .= 'Id: '.$id . " (".$msg.")\n";
        }
        $resMsg .= "\nLast Data: ".json_encode($lastDataError);
        
        
        save_log(
        
        
            $lastAsset,
        
        
            $appname,
                LogLevel::get_log_level_integer(LogLevel::CRITICAL),
                EventType::$event_type_id[EventType::MANAGER_LOG_SYNC],
        
        
            $source,
                date('Y-m-d G:i:s'),
        
        
            $resMsg
        
        
        );
    }
    
    echo "SUCCESS";
}

/**
 * Check if the column is valide (in the data base)
 *
 * @param String $col to test
 * @return True if the data base have this column
 */
function is_col_name($col)
{
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
function is_all_col_valid($arrayToTest)
{
    foreach (array_keys($arrayToTest) as $col) {
        if (!is_col_name($col)) {
            return false;
        }
    }
    return true;
}

/**
 * Get the origin of the request
 *
 * @param Array $input array with all informations
 * @return NULL or String with source
 */
function get_source($input)
{
    if (array_key_exists('asset_classroom_id', $input)) {
        $source = $input['asset_classroom_id'];
        if ($source != null && $source != '') {
            return $source;
        }
    }
    return null;
}


function save_log(
    $asset,
    $origin,
    $loglevel,
    $type_id,
    $asset_classroom,
    $classroom_event_id,
        $event_time,
    $message = "",
    $asset_course = "",
    $asset_author ="",
        $asset_cam_slide = "",
    $context = ""
) {
    global $logger;
    
    $logger->insert_log(
    
        $type_id,
    
        $loglevel,
    
        $message,
    
        $context,
    
        $asset,
    
        $origin,
            $asset_classroom,
    
        $asset_course,
    
        $asset_author,
    
        $asset_cam_slide,
    
        $event_time,
    
        $classroom_event_id
    
    );
}



/**
 * Call SQL data base to save the last insert ID
 *
 * @global PDO $db_object
 * @param int $lastInsert last id insert
 * @param String $source of the machine who send log
 * @return true if the id have been insert
 */
function update_last_inserted_index($lastInsert, $source)
{
    
//    $strSQL = 'INSERT INTO '.db_gettable('event_last_indexes') . '
    //        (source, id) VALUES(:source, :id)
    //        ON DUPLICATE KEY UPDATE
    //        id = :id'; // WHERE id = :id // Not work :/
    
    $strSQL = 'REPLACE INTO '.db_gettable('event_last_indexes') . ' (id, source)
        SELECT :id, :source
        FROM dual
        WHERE NOT EXISTS (SELECT 1 FROM '.db_gettable('event_last_indexes') . '
         WHERE id >= :id AND source = :source)';
    
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
function last_log_sent()
{
    global $input;
    
    if (!array_key_exists('source', $input)) {
        echo "ERROR 1 (invalid arguments)";
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
    if (empty($res)) {
        echo -1;
    } else {
        echo $res['id'];
    }
}
