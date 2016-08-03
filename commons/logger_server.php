<?php

require_once(__DIR__ . '/lib_database.php');
require_once("logger.php");

class ServerLogger extends Logger {
    
    const EVENT_TABLE_NAME = "events";
    const EVENT_STATUS_TABLE_NAME = "event_status";    
    const EVENT_LAST_INDEXES_TABLE_NAME = "event_last_indexes";
    const EVENT_ASSET_PARENT_TABLE_NAME = "event_asset_parent";
    const EVENT_ASSET_INFO_TABLE_NAME = "asset_infos";
    
    private $statement = array();
    
    public function __construct() {
        parent::__construct();
        
        global $db_object;
        if($db_object == null) { //db is not yet prepared yet
            db_prepare(); 
        }
        
        
        $this->statement['insert_log'] = $db_object->prepare(
          'INSERT INTO '. db_gettable(ServerLogger::EVENT_TABLE_NAME) . ' (`asset`, `origin`, `asset_classroom_id`, `asset_course`, ' .
            '`asset_author`, `asset_cam_slide`, `event_time`, `type_id`, `context`, `loglevel`, `message`) VALUES (' .
          ':asset, :origin, :classroom, :course, :author, :cam_slide, :event_time, :type_id, :context, :loglevel, :message)');
        
        $this->statement['insert_asset_info'] = $db_object->prepare(
            'INSERT INTO ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' (asset, ' .
                'start_time, asset_classroom_id, asset_course, asset_author) VALUES(' .
                ':asset, :start_time, :asset_classroom_id, :asset_course, :asset_author)'
            );
        
        $this->statement['update_asset_info'] = $db_object->prepare(
            'UPDATE ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' '
                . 'SET end_time = :end_time '
                . 'WHERE asset = :asset'
            );
        
        
        foreach ($this->statement as $state) {
            if($state == false) {
                echo __CLASS__ . ": Prepared statement failed";
                print_r($this->db->errorInfo());
                throw new Exception("Prepared statement failed");
            }
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
    public function log($type, $level, $message, array $context = array(), $asset = "dummy", 
            $author = null, $cam_slide = null, $course = null, $classroom = null)
    {
        $tempLogData = parent::log($type, $level, $message, $context, $asset, $author, $cam_slide, $course, $classroom);
        
        global $appname; // to be used as origin
        
        $this->insert_log($tempLogData->type_id, $tempLogData->log_level_integer, 
                $message, $tempLogData->context, $asset, $appname, 
                $classroom, $course, $author, 
                $cam_slide, date('Y-m-d H:i:s'));
        
        return $tempLogData;
    }
    
    public function insert_log($type, $level, $message, $context, $asset, $origin, 
            $classroom, $course, $author, $cam_slide, $event_time) {
        
        $type_name = $this->get_type_name($type);
        
        if($type_name== EventType::ASSET_CREATED) {
            $this->insert_asset_infos($asset, $event_time, $classroom, $course, $author);
            
        } else if ($type_name == EventType::ASSET_RECORD_END) {
            $this->insert_asset_infos_end($asset, $event_time);
            
        }
        
        $this->statement['insert_log']->bindParam(':asset', $asset);
        $this->statement['insert_log']->bindParam(':origin', $origin);
        $this->statement['insert_log']->bindParam(':classroom', $classroom);
        $this->statement['insert_log']->bindParam(':course', $course);
        $this->statement['insert_log']->bindParam(':author', $author);
        $this->statement['insert_log']->bindParam(':cam_slide', $cam_slide);
        $this->statement['insert_log']->bindParam(':type_id', $type);        
        $this->statement['insert_log']->bindParam(':context', $context);
        $this->statement['insert_log']->bindParam(':loglevel', $level);
        $this->statement['insert_log']->bindParam(':message', $message);
        $this->statement['insert_log']->bindParam(':event_time', $event_time);
        
        $this->statement['insert_log']->execute();
    }
    
    public function insert_asset_infos($asset, $start_date, $classroom, $course, $author) {
        $this->statement['insert_asset_info']->bindParam(':asset', $asset);
        $this->statement['insert_asset_info']->bindParam(':start_time', $start_date);
        $this->statement['insert_asset_info']->bindParam(':asset_classroom_id', $classroom);
        $this->statement['insert_asset_info']->bindParam(':asset_course', $course);
        $this->statement['insert_asset_info']->bindParam(':asset_author', $author);
        
        $this->statement['insert_asset_info']->execute();
    }
    
    public function insert_asset_infos_end($asset, $end_date) {
        $this->statement['update_asset_info']->bindParam(':end_time', $end_date);
        $this->statement['update_asset_info']->bindParam(':asset', $asset);
        
        $this->statement['update_asset_info']->execute();
    }
}