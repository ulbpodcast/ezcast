<?php

require_once(__DIR__ . '/lib_database.php');
require_once("logger.php");

class ServerLogger extends Logger {
    
    const EVENT_TABLE_NAME = "events";
    const EVENT_STATUS_TABLE_NAME = "event_status";    
    
    public function __construct() {
        parent::__construct();
        
        global $db_object;
        if($db_object == null)  //db is not yet prepared yet
            db_prepare(); 
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $type_id type in the form of EventType::*
     * @param mixed $level in the form of LogLevel::*
     * @param string $message
     * @param string $asset asset identifier
     * @param AssetLogInfo $asset asset identifier
     * @param array $context Context can have several levels, such as array('module', 'capture_ffmpeg'). Cannot contain pipes (will be replaced with slashes if any).
     * @return LogData temporary data, used by children functions
     */
    public function log($type, $level, $message, array $context = array(), $asset = "dummy", $assetInfo = null)
    {
        $tempLogData = parent::log($type, $level, $message, $context, $asset, $assetInfo);
        
        global $db_object;
        global $appname; // to be used as origin
        
        // insert into db
        $statement = $db_object->prepare(
          'INSERT INTO '. db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' (`asset`, `origin`, `asset_classroom_id`, `asset_course`, ' .
            '`asset_author`, `asset_cam_slide`, `event_time`, `type_id`, `context`, `loglevel`, `message`) VALUES (' .
          ':asset, :origin, :classroom, :course, :author, :cam_slide, NOW(), :type_id, :context, :loglevel, :message)');
        
        if($statement == false) {
            echo __CLASS__ . ": Prepared statement failed";
            print_r($this->db->errorInfo());
            return null;
        }
        
        $statement->bindParam(':asset', $asset);
        $statement->bindParam(':origin', $appname);
        $statement->bindParam(':classroom', $tempLogData->asset_info->classroom);
        $statement->bindParam(':course', $tempLogData->asset_info->course);
        $statement->bindParam(':author', $tempLogData->asset_info->author);
        $statement->bindParam(':cam_slide', $tempLogData->asset_info->cam_slide);
        $statement->bindParam(':type_id', $tempLogData->type_id);        
        $statement->bindParam(':context', $tempLogData->context);
        $statement->bindParam(':loglevel', $tempLogData->log_level_integer);
        $statement->bindParam(':message', $tempLogData->message);
        
        $statement->execute();
        
        return $tempLogData;
    }
}