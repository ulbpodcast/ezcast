<?php

require_once(__DIR__ . '/lib_database.php');
require_once("logger.php");

class ServerLogger extends Logger {
    
    const EVENT_TABLE_NAME = "events";
    const EVENT_STATUS_TABLE_NAME = "event_status";    
    const EVENT_LAST_INDEXES_TABLE_NAME = "event_last_indexes";
    const EVENT_ASSET_PARENT = "event_asset_parent";
    
    private $statement = NULL;
    
    public function __construct() {
        parent::__construct();
        
        global $db_object;
        if($db_object == null) { //db is not yet prepared yet
            db_prepare(); 
        }
        
        
        $this->statement = $db_object->prepare(
          'INSERT INTO '. db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' (`asset`, `origin`, `asset_classroom_id`, `asset_course`, ' .
            '`asset_author`, `asset_cam_slide`, `event_time`, `type_id`, `context`, `loglevel`, `message`) VALUES (' .
          ':asset, :origin, :classroom, :course, :author, :cam_slide, NOW(), :type_id, :context, :loglevel, :message)');
        
        if($this->statement == false) {
            echo __CLASS__ . ": Prepared statement failed";
            print_r($this->db->errorInfo());
            throw new Exception("Prepared statement failed");
        }
        
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $type type in the form of EventType::*
     * @param mixed $level in the form of LogLevel::*
     * @param string $message
     * @param string $asset asset identifier
     * @param array $context Context can have several levels, such as array('module', 'capture_ffmpeg'). Cannot contain pipes (will be replaced with slashes if any).
     * @param string $asset asset name
     * @param AssetLogInfo $asset_info Additional information about asset if any, in the form of a AssetLogInfo structure
     * @return LogData temporary data, used by children functions
     */
    public function log($type, $level, $message, array $context = array(), $asset = "dummy", $asset_info = null)
    {
        $tempLogData = parent::log($type, $level, $message, $context, $asset, $asset_info);
        
        
        global $appname; // to be used as origin
        
        insert_log($tempLogData->type_id, $tempLogData->log_level_integer, 
                $message, $tempLogData->context, $asset, $appname, 
                $asset_info->classroom, $asset_info->course, $asset_info->author, 
                $asset_info->cam_slide);
        
        
        return $tempLogData;
    }
    
    public function insert_log($type, $level, $message, $context, $asset, $origin, 
            $classroom, $course, $author, $cam_slide) {
        
        $this->statement->bindParam(':asset', $asset);
        $this->statement->bindParam(':origin', $origin);
        $this->statement->bindParam(':classroom', $classroom);
        $this->statement->bindParam(':course', $course);
        $this->statement->bindParam(':author', $author);
        $this->statement->bindParam(':cam_slide', $cam_slide);
        $this->statement->bindParam(':type_id', $type);        
        $this->statement->bindParam(':context', $context);
        $this->statement->bindParam(':loglevel', $level);
        $this->statement->bindParam(':message', $message);
        
        $this->statement->execute();
    }
    
    
}