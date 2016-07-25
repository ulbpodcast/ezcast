<?php

include_once(__DIR__ . '/AbstractLogger.php');

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class EventType {
    const TYPE1 = "type1";
    const TYPE2 = "type2";
    const TYPE3 = "type3";
    const TYPE4 = "type4";
}

class Logger extends AbstractLogger {
    
    public $event_type_id = array(
       EventType::TYPE1 => 0,
       EventType::TYPE2 => 1,
       EventType::TYPE3 => 2,
       EventType::TYPE4 => 3,
    );
    
    /* Reverted array -> key: id, value: EventType
     * Filled at Logger construct.
     */
    public static $event_type_by_id = false;
    
    public function __construct() {
        fill_type_by_id();
    }
    
    const EVENT_TABLE_NAME = "events";
    const EVENT_STATUS_TABLE_NAME = "event_status";    

    public $log_levels = array(
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    );
        
    public function get_type_name($index)
    {
        if(isset($this->$event_type_by_id[$index]))
            return $this->$event_type_by_id[$index];
        else
            return false;
    }
    
    /**
    * Return log level given in the form of LogLevel::* into an integer
    */
    public function get_log_level_integer($level)
    {
        if($isset($this->log_levels[$level]))
            return $this->log_levels[$level];
        
        foreach($this->log_levels as $key => $value)
        {
          if($key == $level)
            return $value;
        }

        throw new RuntimeException('get_log_level_integer: Invalid level given');
    }
    
    /**
     * Sets the Log Level Threshold
     *
     * @param string $logLevelThreshold The log level threshold
     */
    public function set_log_level_threshold($logLevelThreshold)
    {
        $this->logLevelThreshold = $logLevelThreshold;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context Context can have several levels, such as array('module', 'capture_ffmpeg'). Cannot contain pipes (will be replaced with slashes if any).
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        //do not log if log above log level
        if ($this->logLevels[$this->logLevelThreshold] < $this->logLevels[$level]) {
            return;
        }

        try {
          $logLevelInteger = $this->get_log_level_integer($level);
        } catch (Exception $e) {
          //invalid level given, default to "error" and prepend this problem to the message
          $message = "(Invalid log level) " . $message;
          $logLevelInteger = $this->logLevels[LogLevel::ERROR];
        }

        //todo: fill these
        $author = "todo";
        $cam_slide = "todo";
        
        //pipes will be used as seperator between contexts
        //remove pipes
        $contextStr = str_replace($context, '/', '|');
        //concat contexts for db insert
        $contextStr = implode('|', $context);
        
        $insertQuery = 'INSERT INTO '.$this->EVENT_TABLE_NAME.' (`classroom`, `event_time`, `author`, `cam_slide`, `context`, `loglevel`, `message`) VALUES ('.
          '"'.$this->classroomName.'",'.
          '(SELECT datetime()),'.
          '"'.$author.'",'.
          '"'.$cam_slide.'",'.
          '"'.$contextStr.'",'. //just concat everything in context with slashes between
          $logLevelInteger.','.
          '"'.$message.'"'.
          ')';

        $this->db->exec($insertQuery);
    }
    
    // ----
    
    
    /**
     * Current minimum logging threshold. Logs with higher log level than this are ignored.
     * @var LogLevel::*
     */
    protected $logLevelThreshold = LogLevel::DEBUG;
    
    private function fill_type_by_id()
    {
        if($this->$event_type_by_id == false) {
            $this->$event_type_by_id = array();
            foreach($this->event_type_id as $key => $value) {
                $this->$event_type_by_id[$value] = $key;
            }
        }
            
        return $this->$event_type_by_id;
    }
        
}

//Ex: $logger->warning(EventType::$TYPE1, );