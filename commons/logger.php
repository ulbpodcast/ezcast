<?php

class EventType
{
    public function __construct($name, $id) {
        $this->name = $name;
        $this->id = $id;
        init();
    }
    
    private function init() {
        if(isset($this->TYPE1))
            return;
        
        $this->TYPE1 = new EventType('type1', 0);
        $this->TYPE2 = new EventType('type2', 1);
        $this->TYPE3 = new EventType('type3', 1);
    }
    
    public static $TYPE1;
    public static $TYPE2;
    public static $TYPE3;

    
    public $name;
    public $id;
};
 
class Logger {
    
    function log($dummy)
    {

    }

}

//ex: $logger->info(EventType::$TYPE1, "message");