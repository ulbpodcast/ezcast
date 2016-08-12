<?php

require_once __DIR__.'/../commons/config.inc';
require_once __DIR__.'/../commons/event_status.php';
require_once __DIR__.'/../commons/lib_database.php';
db_prepare();

const TIME_LIMIT = 43200; // 12 hours
const LOG_LEVEL_ERROR = LogLevel::CRITICAL;
const LOG_LEVEL_WARNINGS = LogLevel::WARNING;


/**
 * Database request to get all event from assets having no status
 * 
 * @param boolean $check_all check all event or juste the last two weeks
 * @global PDO $db_object
 * @return Array with the result
 */
function get_db_event_status_not_check($check_all) {
    global $db_object;

    $strSQL = 'SELECT event.asset, event.event_time, event.loglevel, event.type_id '
            . 'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' event ' .
            ' WHERE ';
    if(!$check_all) {
        $strSQL .= 'event.event_time >= DATE_SUB(curdate(), INTERVAL 2 WEEK) AND';
    }
    $strSQL .= ' NOT EXISTS ('.
                'SELECT asset FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' status ' .
                'WHERE status.asset = event.asset' .
            ')';

    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute();
    
    return $reqSQL->fetchAll();
}

/**
 * Database request to add an event status
 * 
 * @global PDO $db_object
 * @param String $asset witch record
 * @param int $status who represent an EventType
 * @param String $description of the status
 */
function add_db_event_status($asset, $status, $description) {
    global $db_object;
    
    $whereParam = array();
    $strSQL = 'INSERT INTO ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' ' .
            '(asset, status, status_time, description) ' .
            'VALUES(:asset, :status, NOW(), :description)';
    $whereParam[':asset'] = $asset;
    $whereParam[':status'] = $status;
    $whereParam[':description'] = $description;
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($whereParam);
}


/**
 * Select the asset who must be check and saved them in two list
 * 
 * @param boolean $check_all 
 * @global Array $resListAsset dictionnary with asset in key and array with info in value
 * @global Array $listAsset List of the asset who must be check (timeout in value if it's timeout)
 */
function selectAssetWhoMustBeCheck($check_all) {
    global $resListAsset;
    global $listAsset;
    
    $allEvent = get_db_event_status_not_check($check_all);
    if($check_all) {
        echo 'Check all event'.PHP_EOL;
    }
    
    
    foreach ($allEvent as $event) {
        $asset = $event['asset'];

        if(array_key_exists($asset, $resListAsset)) {
            array_push($resListAsset[$asset], $event);
        } else {
            $resListAsset[$asset] = array($event);
        }


        if(array_key_exists($asset, $listAsset) && $listAsset[$asset] == "final") {
            continue;
        }

        if($event['type_id'] == EventType::$event_type_id[EventType::ASSET_FINALIZED]) {
            $listAsset[$asset] = "final";

        } else if(time()-strtotime($event['event_time']) > TIME_LIMIT) {
            $listAsset[$asset] = "timeout";
        }

    }
}

/**
 * Calcul the status and send it to the database
 * 
 * @global Array $listAsset list of all the asset which must be check
 * @global Array $resListAsset dictionnary of all events (asset in key and
 * all event about this in value)
 */
function sendAssetStatus() {
    global $listAsset;
    global $resListAsset;
    
    foreach($listAsset as $asset => $inListBecause) {

        if($inListBecause == "timeout") { 
            // End with an time out
            add_db_event_status($asset, EventStatus::AUTO_FAILURE, 'Time out after: ' . 
                    TIME_LIMIT . ' seconds');

        } else {
            $maxLogLevel = 7;
            foreach($resListAsset[$asset] as $assetInfo) {
                if($assetInfo['loglevel'] < $maxLogLevel) {
                    $maxLogLevel = $assetInfo['loglevel'];
                }
            }


            if($maxLogLevel <= LogLevel::$log_levels[LOG_LEVEL_ERROR]) {
                $eventStatus = EventStatus::AUTO_SUCCESS_ERRORS;

            } else if($maxLogLevel <= LogLevel::$log_levels[LOG_LEVEL_WARNINGS]) {
                $eventStatus = EventStatus::AUTO_SUCCESS_WARNINGS;

            } else {
                $eventStatus = EventStatus::AUTO_SUCCESS;

            }
            
            add_db_event_status($asset, $eventStatus, "");
        }   
    }
}

// Dictionnary with asset in key and array with info in value
$resListAsset = array();
// List with asset who must be treat (timeout in value if it's timeout)
$listAsset = array();

selectAssetWhoMustBeCheck($argc > 1 && $argv[1] == 'all');
sendAssetStatus();
