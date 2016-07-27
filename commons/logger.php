<?php

// This file is shared between server and recorder and should be kept identical in both projects.
// Specialized loggers for each are implemented with this one as base.
         
require_once("logger_event_type.php");

/**
 * 
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
    public function __construct($author = "", $cam_slide = "", $course = "", $classroom = "") {
        $this->author = $author;
        $this->cam_slide = $cam_slide;
        $this->course = $course;
        $this->classroom = $classroom;
    }
    
    public $author;
    public $cam_slide;
    public $course;
    public $classroom;
}

/* This structure is used to pass temporary results from the `log` parent function to its child. 
 * Feel free to change it if you find another more elegant solution.
 */
class LogData {
    public $log_level_integer = null;
    public $context = null;
    public $type_id = null;
    public $message = null;
    
    public $asset_info = null; //type AssetLogInfo
}
    
class Logger {
    
    /* Reverted EventType array -> key: id, value: EventType
     * Filled at Logger construct.
     */
    public static $event_type_by_id = false;
    
    //set this to true to echo all logs
    public static $print_logs = false;
    
    /* 
     * Reverted LogLevel array -> key: id, value: LogLevel name (string)
     * Filled at Logger construct
     */
    public static $log_level_name_by_id = false;
        
    public function __construct() {
        $this->fill_event_type_by_id();
        $this->fill_level_name_by_id();
    }
    
    public function get_type_name($index)
    {
        //var_dump(Logger::$event_type_by_id);
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
     * Logs with an arbitrary level.
     *
     * @param mixed $type type in the form of EventType::*
     * @param mixed $level in the form of LogLevel::*
     * @param string $message
     * @param string $asset asset identifier
     * @param AssetLogInfo $asset asset identifier
     * @param array $context Context can have several levels, such as array('module', 'capture_ffmpeg'). Cannot contain pipes (will be replaced with slashes if any).
     * @return LogData temporary data, used by children functions
     */
    public function log($type, $level, $message, array $context = array(), $asset = "dummy", $assetInfo = null)
    {
        $tempLogData = new LogData();
        $tempLogData->message = $message;
                
        // convert given loglevel to integer for db storage
        try {
          $tempLogData->log_level_integer = $this->get_log_level_integer($level);
        } catch (Exception $e) {
          //invalid level given, default to "error" and prepend this problem to the message
          $tempLogData->message = "(Invalid log level) " . $message;
          $tempLogData->log_level_integer = LogLevel::$log_levels[LogLevel::ERROR];
        }

        //convert given type_id to integer for db storage
       $tempLogData->type_id = isset(EventType::$event_type_id[$type]) ? EventType::$event_type_id[$type] : 0;
       
        // asset infos. May be null, we only give it at record start
        if($assetInfo)
            $tempLogData->assetInfo = $assetInfo;
        else
            $tempLogData->asset_info = new AssetLogInfo(); //if no asset info, init with default values
        
        // pipes will be used as seperator between contexts
        // remove pipes
        $contextStr = str_replace($context, '/', '|');
        // concat contexts for db insert
        $contextStr = implode('|', $context);
        
        $tempLogData->context = $contextStr;
            
        // okay, all data ready

        if(Logger::$print_logs)
            echo "log: [$level] / type: $contextStr / $type / $tempLogData->message" .PHP_EOL;
        
        //idea: if level is critical or below, push back trace to message
        
        return $tempLogData;
    }
    
    // -- PROTECTED
    // ----------
    
    //fill $event_type_by_id from $event_type_id
    protected function fill_event_type_by_id()
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
    protected function fill_level_name_by_id()
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