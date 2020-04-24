<?php

/*
 * This CLI will check assets logs from the last two weeks and set a status for those having none.
 * Usage: php cli_fill_assets_status [all]/[asset_name]
 * If 'all' arg is set, this will check all assets not yet processed for all time.
 */

/*
    Here is a PlantUML diagram for status decision
 *
 *
 *
    @startuml
    title Automatic cron

    'accessibilité
    skinparam classAttributeIconSize 0

    'pas d'ombre
    skinparam shadowing false

    'taille de l'image
    skinparam dpi 200

    'couleurs
    skinparam activity {
      StartColor Navy
      BackgroundColor AliceBlue
      ArrowColor CornflowerBlue
      BorderColor CornflowerBlue
      EndColor Navy
    }
    skinparam NoteBackgroundColor PapayaWhip
    skinparam NoteBorderColor LightSalmon
    skinparam stereotypeCBackgroundColor OldLace

    start

    :Get all logs having no status;
    if (Is there an END log for this status?) then (non)
      if (Is the last log for this asset older than X ?) then (yes)
        :FAILURE;
      else (no)
        :Do nothing (asset will be checked again at next execution);
      endif;
    else (yes)
      if (Is there an event with critical level ?) then (yes)
        :FAILURE;
      else (no)
        :OKAY;
      endif;
    endif

    stop
    @enduml
*/

require_once __DIR__.'/../commons/config.inc';
require_once __DIR__.'/../commons/event_status.php';
require_once __DIR__.'/../commons/lib_database.php';
db_prepare();

Logger::$print_logs = true;

const TIME_LIMIT = 43200; // 12 hours
const LOG_LEVEL_ERROR_THRESHOLD = LogLevel::CRITICAL; //if an event of this level appears, warn a serious problem occured in status
const LOG_LEVEL_WARNING_THRESHOLD = LogLevel::ERROR; //if an event of this level, warn a possible problem occured


/**
 * Database request to get all event from assets having no status
 *
 * @param boolean $check_all check all event or juste the last two weeks
 * @global PDO $db_object
 * @return Array with the result
 */
function get_db_event_status_not_check($check_all, $specific_asset)
{
    global $db_object;

    $strSQL = 'SELECT event.asset, event.event_time, event.loglevel, event.type_id '
            . 'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' event ' .
            ' WHERE ';
    if ($specific_asset != null) {
        $strSQL .= "event.asset = '$specific_asset' AND";
    } elseif (!$check_all) {
        $strSQL .= 'event.event_time >= DATE_SUB(curdate(), INTERVAL 1 WEEK) AND';
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
function add_db_event_status($asset, $status, $description)
{
    global $db_object;
    global $logger;
    
    $whereParam = array();
    $strSQL = 'INSERT INTO ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' ' .
            '(asset, status, status_time, description) ' .
            'VALUES(:asset, :status, NOW(), :description)';
    $whereParam[':asset'] = $asset;
    $whereParam[':status'] = $status;
    $whereParam[':description'] = $description;
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($whereParam);
    
    $logger->log(EventType::MANAGER_FILL_STATUS, LogLevel::DEBUG, "Inserted asset status $status for asset $asset", array(__FUNCTION__), $asset);
}


/**
 * Select the asset who must be check and saved them in two list
 *
 * @param boolean $check_all
 * @global Array $resListAsset dictionnary with asset in key and array with info in value
 * @global Array $listAsset List of the asset who must be check (timeout in value if it's timeout)
 */
function select_asset_to_check($check_all, $specific_asset)
{
    global $resListAsset;
    global $listAsset;
    global $logger;
    
    if ($specific_asset) {
        echo "Checking asset $specific_asset" . PHP_EOL;
    } elseif ($check_all) {
        echo 'Check all event'.PHP_EOL;
    }
    
    $allEvent = get_db_event_status_not_check($check_all, $specific_asset);

    if ($specific_asset && empty($allEvent)) {
        echo "Could not find any event for asset $specific_asset" . PHP_EOL;
    }
    
    
    foreach ($allEvent as $event) {
        $asset = $event['asset'];

        if (array_key_exists($asset, $resListAsset)) {
            array_push($resListAsset[$asset], $event);
        } else {
            $resListAsset[$asset] = array($event);
        }

        //stop looking if we already got a status for this asset
        if (array_key_exists($asset, $listAsset) && ($listAsset[$asset] == "final" || $listAsset[$asset] == "cancel")) {
            continue;
        }

        if ($event['type_id'] == EventType::$event_type_id[EventType::ASSET_FINALIZED]) {
            $listAsset[$asset] = "final";
            $logger->log(EventType::MANAGER_FILL_STATUS, LogLevel::DEBUG, "Found finalization event for asset $asset", array(__FUNCTION__), $asset);
        } elseif ($event['type_id'] == EventType::$event_type_id[EventType::ASSET_CANCELED]) {
            $listAsset[$asset] = "cancel";
            $logger->log(EventType::MANAGER_FILL_STATUS, LogLevel::DEBUG, "Found cancel event for asset $asset", array(__FUNCTION__), $asset);
        } elseif (time()-strtotime($event['event_time']) > TIME_LIMIT) {
            $listAsset[$asset] = "timeout"; //Default to timeout, may be overwritten in the next loops
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
function write_asset_status()
{
    global $listAsset;
    global $resListAsset;
    
    foreach ($listAsset as $asset => $inListBecause) {
        switch ($inListBecause) {
            case "timeout":
                 // End with an time out
                add_db_event_status($asset, EventStatus::AUTO_FAILURE, 'Time out after: ' .
                        TIME_LIMIT . ' seconds');
                break;
            case "cancel":
                add_db_event_status($asset, EventStatus::AUTO_IGNORE, "Canceled by user");
                break;
            case "final":
                 $maxLogLevel = 7;
                foreach ($resListAsset[$asset] as $assetInfo) {
                    if ($assetInfo['loglevel'] < $maxLogLevel) {
                        $maxLogLevel = $assetInfo['loglevel'];
                    }
                }

                if ($maxLogLevel <= LogLevel::$log_levels[LOG_LEVEL_ERROR_THRESHOLD]) {
                    $eventStatus = EventStatus::AUTO_SUCCESS_ERRORS;
                } elseif ($maxLogLevel <= LogLevel::$log_levels[LOG_LEVEL_WARNING_THRESHOLD]) {
                    $eventStatus = EventStatus::AUTO_SUCCESS_WARNINGS;
                } else {
                    $eventStatus = EventStatus::AUTO_SUCCESS;
                }

                add_db_event_status($asset, $eventStatus, "");
                break;
        }
    }
}

$logger->log(EventType::MANAGER_FILL_STATUS, LogLevel::DEBUG, "Cli fill status started", array(__FUNCTION__));

// Dictionnary with asset in key and event info (array) in value
$resListAsset = array();
// For each asset 'final' or 'timeout' status
$listAsset = array();

$fill_all = false;
$specific_asset = null;
if ($argc > 1) {
    if ($argv[1] == 'all') {
        $fill_all = true;
    } else {
        $specific_asset = trim($argv[1]);
    }
}
select_asset_to_check($fill_all, $specific_asset);
write_asset_status();
