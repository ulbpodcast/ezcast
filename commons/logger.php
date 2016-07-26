<?php

require_once(__DIR__ . '/lib_database.php');

/**
 * Describes log levels. Frm PSR-3 Logger Interface. (http://www.php-fig.org/psr/psr-3/)
 */
class LogLevel
{
    /**
     * System is unusable.
     */
    const EMERGENCY = 'emergency';
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    const ALERT     = 'alert';
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL  = 'critical';
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    const ERROR     = 'error';
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    const WARNING   = 'warning';
    /**
     * Normal but significant events.
     */
    const NOTICE    = 'notice';
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    const INFO      = 'info';
    /**
     * Detailed debug information.
     */
    const DEBUG     = 'debug';
    
    // index by LogLevel
    public static $log_levels = array(
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    );
}

//Structure used as argument to log calls
class AssetLogInfo
{
    public function __construct($author, $cam_slide, $course, $classroom) {
        $this->author = $author;
        $this->cam_slide = $cam_slide;
        $this->course = $course;
        $this->classroom = $classroom;
    }
    
    public $author = "todo";
    public $cam_slide = "todo";
    public $course = "todo";
    public $classroom = "todo";
}

class EventType {
    const TYPE1 = "type1";
    const TYPE2 = "type2";
    const TYPE3 = "type3";
    const TYPE4 = "type4";
    
    // index by EventType
    public static $event_type_id = array(
       EventType::TYPE1 => 0,
       EventType::TYPE2 => 1,
       EventType::TYPE3 => 2,
       EventType::TYPE4 => 3,
    );
}

class Logger {
    
    /* Reverted EventType array -> key: id, value: EventType
     * Filled at Logger construct.
     */
    public static $event_type_by_id = false;
    
    /* 
     * Reverted LogLevel array -> key: id, value: LogLevel name (string)
     * Filled at Logger construct
     */
    public static $log_level_name_by_id = false;
    
    const EVENT_TABLE_NAME = "events";
    const EVENT_STATUS_TABLE_NAME = "event_status";    
    
    public function __construct($log_level_threshold = LogLevel::INFO) {
        $this->fill_event_type_by_id();
        $this->fill_level_name_by_id();
        
        $this->log_level_threshold = $log_level_threshold;
        
        global $db_object;
        if($db_object == null)  //db is not yet prepared yet
            db_prepare(); 
    }
    
    public function get_type_name($index)
    {
        if(isset(Logger::$event_type_by_id[$index]))
            return Logger::$event_type_by_id[$index];
        else
            return false;
    }
    
    public function get_log_level_name($index)
    {
        if(isset(Logger::$log_level_name_by_id[$index]))
            return Logger::$log_level_name_by_id[$index];
        else
            return false;
    }
    
    /**
    * Return log level given in the form of LogLevel::* into an integer
    * Throws RuntimeException if invalid level given
    */
    public function get_log_level_integer($level)
    {
        if(isset(LogLevel::$log_levels[$level]))
            return LogLevel::$log_levels[$level];
        
        foreach(LogLevel::$log_levels as $key => $value)
        {
          if($key == $level)
            return $value;
        }

        throw new RuntimeException('get_log_level_integer: Invalid level given');
    }
    
    /**
     * Sets the Log Level Threshold
     *
     * @param string $log_level_threshold The log level threshold
     */
    public function set_log_level_threshold($log_level_threshold)
    {
        $this->log_level_threshold = $log_level_threshold;
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
     * @return null
     */
    public function log($type_id, $level, $message, $asset = "dummy", $assetInfo = null, array $context = array())
    {
        global $db_object;
        global $appname; // to be used as origin
        
        // do not log if log above threshold log level
        if (LogLevel::$log_levels[$this->log_level_threshold] < LogLevel::$log_levels[$level]) {
            return;
        }

        //
        try {
          $logLevelInteger = $this->get_log_level_integer($level);
        } catch (Exception $e) {
          //invalid level given, default to "error" and prepend this problem to the message
          $message = "(Invalid log level) " . $message;
          $logLevelInteger = LogLevel::$log_levels[LogLevel::ERROR];
        }

        //asset infos. May be null, we only give it at record start
        $classroom = null;
        $course = null;
        $author = null;
        $cam_slide = null;
        if($assetInfo) {
            $classroom = $assetInfo->classroom;
            $course = $assetInfo->course;
            $author = $assetInfo->author;
            $cam_slide = $assetInfo->cam_slide;
        }
        
        //pipes will be used as seperator between contexts
        //remove pipes
        $contextStr = str_replace($context, '/', '|');
        //concat contexts for db insert
        $contextStr = implode('|', $context);
        
        //public function emergency($type_id, $message, $assetLogInfo = null, array $context = array())
                
        $statement = $db_object->prepare(
          'INSERT INTO '. db_gettable(Logger::EVENT_TABLE_NAME) . ' (`asset`, `origin`, `asset_classroom_id`, `asset_course`, ' .
            '`asset_author`, `asset_cam_slide`, `event_time`, `type_id`, `context`, `loglevel`, `message`) VALUES (' .
          ':asset, :origin, :classroom, :course, :author, :cam_slide, NOW(), :type_id, :context, :loglevel, :message)');
        
        //(SELECT datetime())
        
        $statement->bindParam(':asset', $asset);
        $statement->bindParam(':origin', $appname);
        $statement->bindParam(':classroom', $classroom);
        $statement->bindParam(':course', $course);
        $statement->bindParam(':author', $author);
        $statement->bindParam(':cam_slide', $cam_slide);
        $statement->bindParam(':type_id', $type_id);        
        $statement->bindParam(':context', $contextStr);
        $statement->bindParam(':loglevel', $logLevelInteger);
        $statement->bindParam(':message', $message);
        
        $statement->execute();
    }
    
    // -- PRIVATE
    // ----------
    
    /**
     * Current minimum logging threshold. Logs with higher log level than this are ignored.
     * @var LogLevel::*
     */
    private $log_level_threshold = LogLevel::DEBUG;
    
    //fill $event_type_by_id from $event_type_id
    private function fill_event_type_by_id()
    {
        if(Logger::$event_type_by_id == false) {
            Logger::$event_type_by_id = array();
            foreach(EventType::$event_type_id as $key => $value) {
                Logger::$event_type_by_id[$value] = $key;
            }
        }
            
        return Logger::$event_type_by_id;
    }
    
    //fill $log_level_name_by_id from $log_levels
    private function fill_level_name_by_id()
    {
        if(Logger::$log_level_name_by_id == false) {
            Logger::$log_level_name_by_id = array();
            foreach(LogLevel::$log_levels as $key => $value) {
                Logger::$log_level_name_by_id[$value] = $key;
            }
        }
            
        return Logger::$log_level_name_by_id;
    }
}