<?php

const TIME_LIMIT = 43200; // 12 hours
require_once __DIR__.'/../commons/config.inc';
require_once __DIR__.'/../commons/event_status.php';
require_once __DIR__.'/../commons/lib_database.php';
db_prepare();


function get_db_event_status_not_check() {
    global $db_object;

    $strSQL = 'SELECT event.asset, event.event_time, event.loglevel, event.type_id '
            . 'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' event ' .
            ' WHERE NOT EXISTS ('.
                'SELECT asset FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' status ' .
                'WHERE status.asset = event.asset' .
            ')';

    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute();
    
    return $reqSQL->fetchAll();
}

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


$allEvent = get_db_event_status_not_check();

// Dictionnary with asset in key and array with info in value
$resListAsset = array();
// List with asset who must be treat (false in value if it's timedout)
$listAsset = array();

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

print_r($listAsset);

foreach($listAsset as $asset => $inListBecause) {
    
    if($inListBecause == "timeout") { 
        // End with an time out
        add_db_event_status($asset, EventStatus::AUTO_FAILURE, "Time out after: 43200 seconds");
        
    } else {
        
        $maxLogLevel = 7;
        foreach($resListAsset[$asset] as $assetInfo) {
            if($assetInfo['loglevel'] < $maxLogLevel) {
                $maxLogLevel = $assetInfo['loglevel'];
            }
        }
        
        
        if($maxLogLevel <= LogLevel::$log_levels[LogLevel::CRITICAL]) {
            $eventStatus = EventStatus::AUTO_SUCCESS_ERRORS;
            
        } else if($maxLogLevel <= LogLevel::$log_levels[LogLevel::WARNING]) {
            $eventStatus = EventStatus::AUTO_SUCCESS_WARNINGS;
            
        } else {
            $eventStatus = EventStatus::AUTO_SUCCESS;
            
        }
        
        add_db_event_status($asset, $eventStatus, "");
        
    }
    
}
