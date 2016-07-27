<?php

// This file is shared between server and recorder and should be kept identical in both projects.

// When adding a new type, add a class member + add it's ID into the $event_type_id array
class EventType {
    // Commons
    const TEST = "test";
    
    // Recorder
    const RECORDER_DB               = "recorder_db";
    const UPLOAD_WRONG_METADATA     = "upload_wrong_metadata";
    const CAPTURE_POST_PROCESSING   = "capture_post_processing";
    const UPLOAD_TO_EZCAST          = "upload_to_ezcast";
    const PUSH_STOP                 = "push_stop";
    // EZAdmin
    
    // EZManager
    
    // EZRenderer
    
    // EZPlayer
    
    
    // index by EventType
    public static $event_type_id = array(
       // Commons: 0->999
       EventType::TEST                                       => 0,
        
       // Recorder: 1000->1999
       EventType::RECORDER_DB                                => 1000,
       EventType::UPLOAD_WRONG_METADATA                      => 1001,
       EventType::CAPTURE_POST_PROCESSING                    => 1002,
       EventType::UPLOAD_TO_EZCAST                           => 1003,
       
       // EZAdmin: 2000->2999
       
       // EZManager: 3000->3999 
       
       // EZRenderer: 4000->4999
        
       // EZPlayer: 5000->5999
    );
}
