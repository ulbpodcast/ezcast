<?php

// This file is shared between server and recorder and should be kept identical in both projects.
// Specialized loggers for each are implemented with this one as base.
// example :
// $logger->log(EventType::RECORDER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Couldn't get info from slide module. Slides will be ignored", array("cli_process_upload"), $asset);
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
     * Those are not sent to server from recorders if $send_debug_logs_to_server is disabled (recorder global config)
     */
    const DEBUG     = 'debug';
    
    /**
    * Return log level given in the form of LogLevel::* into an integer
    */
    public static function get_log_level_integer($log_level)
    {
        return LogLevel::$log_levels[$log_level];
    }
    
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

/* This structure is used to pass temporary results from the `log` parent function to its child.
 * Feel free to change it if you find another more elegant solution.
 */
class LogData
{
    public $log_level_integer = null;
    public $type_id = null;
    public $context = null;
}

//Log serverside structure for db insertion using the ezmanager services
class ServersideLogEntry
{
    public $id = 0;
    public $asset = "";
    public $origin = "";
    public $asset_classroom_id = null;
    public $asset_course = null;
    public $asset_author = null;
    public $asset_cam_slide = null;
    public $event_time;
    public $type_id = 0;
    public $context = "";
    public $loglevel = 7;
    public $message = "";
}

abstract class Logger
{
    
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
        
    protected function __construct()
    {
        $this->fill_event_type_by_id();
        $this->fill_level_name_by_id();
    }
    
    public function get_type_name($index)
    {
        if (isset(Logger::$event_type_by_id[$index])) {
            return Logger::$event_type_by_id[$index];
        } else {
            return false;
        }
    }
    
    public function get_log_level_name($index)
    {
        if (isset(Logger::$log_level_name_by_id[$index])) {
            return Logger::$log_level_name_by_id[$index];
        } else {
            return false;
        }
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $type type in the form of EventType::*
     * @param mixed $level in the form of LogLevel::*
     * @param string $message
     * @param string $asset asset identifier
     * @param array $context Additional context info. Context can have several levels, such as array('module', 'capture_ffmpeg').
     * @param string $asset asset name
     * @param AssetLogInfo $asset_info Additional information about asset if any, in the form of a AssetLogInfo structure
     * @return LogData temporary data, used by children functions
     */
    protected function _log(
        &$type,
        &$level,
        &$message,
        array &$context = array(),
        &$asset = "dummy",
            &$author = null,
        &$cam_slide = null,
        &$course = null,
        &$classroom = null
    ) {
        global $debug_mode;
        global $service;
        
        if (!isset($message) || !$message) {
            $message = "";
        }
        
        if (!isset($asset) || !$asset) {
            $asset = "dummy";
        }
        
        $tempLogData = new LogData();
        //limit string size
        $message = substr($message, 0, 1000);
                
        // convert given loglevel to integer for db storage
        try {
            $tempLogData->log_level_integer = LogLevel::get_log_level_integer($level);
        } catch (Exception $e) {
            //invalid level given, default to "error" and prepend this problem to the message
            $message = "(Invalid log level) " . $message;
            $tempLogData->log_level_integer = LogLevel::$log_levels[LogLevel::ERROR];
        }

        //convert given type_id to integer for db storage
        $tempLogData->type_id = isset(EventType::$event_type_id[$type]) ? EventType::$event_type_id[$type] : 0;
       
        if (!isset($author)) {
            $author = "";
        }
        if (!isset($cam_slide)) {
            $cam_slide = "";
        }
        if (!isset($course)) {
            $course = "";
        }
        if (!isset($classroom)) {
            $classroom = "";
        }
       
        // pipes will be used as seperator between contexts
        // concat contexts for db insert
        $tempLogData->context = implode('|', $context);
        
        // okay, all data ready
        

        $print_str = "log| [$level] / context: $tempLogData->context / type: $type / " . htmlspecialchars($message);
        if (Logger::$print_logs) {
            echo $print_str . PHP_EOL;
        }
        
        if ($debug_mode
                && $type != EventType::PHP //PHP events are already printed in custom_error_handling.php
                && (!isset($service) || $service == false) //some services will try to parse the response, and our <script> may interfere in this case
                && php_sapi_name() != "cli") { //don't print in CLI
            echo("<script>console.log('$print_str');</script>");
        }
        
        //idea: if level is critical or below, add strack trace to message
        
        return $tempLogData;
    }
    
    // -- PROTECTED
    // ----------
    
    //fill $event_type_by_id from $event_type_id
    protected function fill_event_type_by_id()
    {
        if (Logger::$event_type_by_id == false) {
            Logger::$event_type_by_id = array();
            foreach (EventType::$event_type_id as $key => $value) {
                Logger::$event_type_by_id[$value] = $key;
            }
        }
            
        return Logger::$event_type_by_id;
    }
    
    //fill $log_level_name_by_id from $log_levels
    protected function fill_level_name_by_id()
    {
        if (Logger::$log_level_name_by_id == false) {
            Logger::$log_level_name_by_id = array();
            foreach (LogLevel::$log_levels as $key => $value) {
                Logger::$log_level_name_by_id[$value] = $key;
            }
        }
        
        return Logger::$log_level_name_by_id;
    }
}
