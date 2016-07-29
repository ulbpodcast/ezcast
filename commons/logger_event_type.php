<?php

// This file is shared between server and recorder and should be kept identical in both projects.

// When adding a new type, add a class member + add it's ID into the $event_type_id array
class EventType {
    // Commons
    const TEST = "test";
    const LOGGER = "logger";
    const ASSET_CREATED = "asset_created";
    const ASSET_FINALIZED = "asset_finalized";
    
    // Recorder
    const RECORDER_DB                        = "recorder_db";
    const RECORDER_UPLOAD_WRONG_METADATA     = "::RECORDER_UPLOAD_WRONG_METADATA";
    const RECORDER_CAPTURE_POST_PROCESSING   = "capture_post_processing";
    const RECORDER_UPLOAD_TO_EZCAST          = "upload_to_ezcast";
    const RECORDER_PUSH_STOP                 = "push_stop";
    const RECORDER_REQUEST_TO_MANAGER        = "request_to_manager";
    const RECORDER_FFMPEG_INIT               = "ffmpeg_init";
    const RECORDER_FFMPEG_STOP               = "ffmpeg_stop";
    const RECORDER_MERGE_MOVIES              = "merge_movies";
    const RECORDER_LOG_SYNC                  = "log_sync";
    const RECORDER_PUBLISH                   = "recorder_publish";
    const RECORDER_USER_SUBMIT_INFO          = "recorder_user_submit_info";
    
    // EZAdmin
    
    // EZManager
    
    // EZRenderer
    
    // EZPlayer
    
    
    // index by EventType. Do NOT change already existing values unless you're ready to loose the EventType of all previous logs
    public static $event_type_id = array(
       // Commons: 0->999
       EventType::TEST                                       => 0,
       EventType::LOGGER                                     => 1,
       EventType::ASSET_CREATED                              => 2,
       EventType::ASSET_FINALIZED                            => 3,
        
       // Recorder: 1000->1999
       EventType::RECORDER_DB                                => 1000,
       EventType::RECORDER_UPLOAD_WRONG_METADATA             => 1001,
       EventType::RECORDER_CAPTURE_POST_PROCESSING           => 1002,
       EventType::RECORDER_UPLOAD_TO_EZCAST                  => 1003,
       EventType::RECORDER_PUSH_STOP                         => 1004,
       EventType::RECORDER_REQUEST_TO_MANAGER                => 1005,
       EventType::RECORDER_FFMPEG_INIT                       => 1006,
       EventType::RECORDER_FFMPEG_STOP                       => 1007,
       EventType::RECORDER_MERGE_MOVIES                      => 1008,
       EventType::RECORDER_LOG_SYNC                          => 1009,
       EventType::RECORDER_PUBLISH                           => 1010,
       EventType::RECORDER_USER_SUBMIT_INFO                  => 1011,
       
       // EZAdmin: 2000->2999
       
       // EZManager: 3000->3999 
       
       // EZRenderer: 4000->4999
        
       // EZPlayer: 5000->5999
    );
}
