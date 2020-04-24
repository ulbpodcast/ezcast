<?php

// This file is shared between server and recorder and should be kept identical in both projects.
// !!! When adding a new type, add a class member + add it's ID into the $event_type_id array

class EventType
{
    // Commons
    const TEST                               = "test";
    const LOGGER                             = "logger";
    const ASSET_CREATED                      = "asset_created";
    const ASSET_FINALIZED                    = "asset_finalized";
    const ASSET_RECORD_END                   = "asset_record_end";
    const PHP                                = "php";
    const ASSET_CANCELED                     = "asset_canceled";
    
    // Recorder
    const RECORDER_DB                        = "recorder_recorder_db";
    const RECORDER_UPLOAD_WRONG_METADATA     = "recorder_upload_wrong_metadata";
    const RECORDER_CAPTURE_POST_PROCESSING   = "recorder_capture_post_processing";
    const RECORDER_UPLOAD_TO_EZCAST          = "recorder_upload_to_ezcast";
    const RECORDER_PUSH_STOP                 = "recorder_push_stop";
    const RECORDER_REQUEST_TO_MANAGER        = "recorder_request_to_manager";
    const RECORDER_FFMPEG_INIT               = "recorder_ffmpeg_init";
    const RECORDER_FFMPEG_STOP               = "recorder_ffmpeg_stop";
    const RECORDER_MERGE_MOVIES              = "recorder_merge_movies";
    const RECORDER_LOG_SYNC                  = "recorder_log_sync";
    const RECORDER_PUBLISH                   = "recorder_publish";
    const RECORDER_USER_SUBMIT_INFO          = "recorder_user_submit_info";
    const RECORDER_CAPTURE_INIT              = "recorder_capture_init";
    const RECORDER_PAUSE_RESUME              = "recorder_pause_resume";
    const RECORDER_START                     = "recorder_start";
    const RECORDER_FINALIZE                  = "recorder_finalize";
    const RECORDER_INFO_GET                  = "recorder_info_get";
    const RECORDER_CANCEL                    = "recorder_cancel";  //user cancelled
    const RECORDER_FORCE_QUIT                = "recorder_force_quit"; //record was forcefully ended (ex: another user logs in)
    const RECORDER_LOGIN                     = "recorder_login";
    const RECORDER_STOP                      = "recorder_stop";
    const RECORDER_METADATA                  = "recorder_metadata";
    const RECORDER_SET_STATUS                = "recorder_set_status";
    const RECORDER_REMOTE_CALL               = "recorder_remote_call";
    const RECORDER_TIMEOUT_MONITORING        = "recorder_timeout_monitoring";
    const RECORDER_CAM_MANAGEMENT            = "recorder_cam_management";
    const RECORDER_STREAMING                 = "recorder_streaming";
    const RECORDER_SOUND_DETECTION           = "recorder_sound_detection";
    const RECORDER_FFMPEG_CUTLIST            = "recorder_ffmpeg_cutlist";
    const RECORDER_FFMPEG_PROCESS            = "recorder_ffmpeg_process";
    const RECORDER_MODULE_MONIT              = "recorder_module_monit";
    const RECORDER_SOUND_BACKUP              = "recorder_sound_backup";
    
    // EZAdmin
    
    // EZManager
    const MANAGER_LOG_SYNC                   = "manager_log_sync";
    const MANAGER_UPLOAD_TO_EZCAST           = "manager_upload_to_ezcast";
    const MANAGER_REQUEST_FROM_RECORDER      = "manager_request_from_recorder";
    const MANAGER_MAM_INSERT                 = "manager_mam_insert";
    const MANAGER_SUBMIT_RENDERING           = "manager_submit_rendering";
    const MANAGER_FILL_STATUS                = "manager_fill_status";
    const MANAGER_ASSET_DELETE               = "manager_asset_delete";
    const MANAGER_ASSET_MOVE                 = "manager_asset_move";
    const MANAGER_STREAMING                  = "manager_streaming";
    const MANAGER_BOOKMARKS                  = "manager_bookmarks";
    const MANAGER_SCHEDULING                 = "manager_scheduling";
    const MANAGER_RENDERING                  = "manager_rendering";
    const MANAGER_EXTERNAL_STREAM            = "manager_external_stream";
    const MANAGER_ASSET_COPY                 = "manager_asset_copy";
    const MANAGER_MODO_DELETE                = "manager_modo_delete";
    const MANAGER_EZMAM_CREATE_COURSE        = "manager_ezmam_create_course";
    const MANAGER_SUBSCRIPTION_FORM          = "manager_subscription_form";
    
    // EZRenderer
    
    // EZPlayer
    const EZPLAYER_STREAMING                 = "ezplayer_external_stream";
    
    // index by EventType. Do NOT change already existing values unless you're ready to loose the EventType of all previous logs
    public static $event_type_id = array(
        // Commons: 0->999
        EventType::TEST                                       => 0,
        EventType::LOGGER                                     => 1,
        EventType::ASSET_CREATED                              => 2,
        EventType::ASSET_FINALIZED                            => 3,
        EventType::ASSET_RECORD_END                           => 4,
        EventType::PHP                                        => 5,
        EventType::ASSET_CANCELED                             => 6,
        
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
        EventType::RECORDER_CAPTURE_INIT                      => 1012,
        EventType::RECORDER_PAUSE_RESUME                      => 1013,
        EventType::RECORDER_START                             => 1014,
        EventType::RECORDER_FINALIZE                          => 1015,
        EventType::RECORDER_INFO_GET                          => 1016,
        EventType::RECORDER_CANCEL                            => 1017,
        EventType::RECORDER_FORCE_QUIT                        => 1018,
        EventType::RECORDER_LOGIN                             => 1019,
        EventType::RECORDER_STOP                              => 1020,
        EventType::RECORDER_METADATA                          => 1021,
        EventType::RECORDER_SET_STATUS                        => 1022,
        EventType::RECORDER_REMOTE_CALL                       => 1023,
        EventType::RECORDER_TIMEOUT_MONITORING                => 1024,
        EventType::RECORDER_CAM_MANAGEMENT                    => 1025,
        EventType::RECORDER_STREAMING                         => 1026,
        EventType::RECORDER_SOUND_DETECTION                   => 1027,
        EventType::RECORDER_FFMPEG_CUTLIST                    => 1028,
        EventType::RECORDER_FFMPEG_PROCESS                    => 1029,
        EventType::RECORDER_MODULE_MONIT                      => 1030,
        EventType::RECORDER_SOUND_BACKUP                      => 1031,
        
        // EZAdmin: 2000->2999
       
        // EZManager: 3000->3999
        EventType::MANAGER_LOG_SYNC                           => 3000,
        EventType::MANAGER_UPLOAD_TO_EZCAST                   => 3001,
        EventType::MANAGER_REQUEST_FROM_RECORDER              => 3002,
        EventType::MANAGER_MAM_INSERT                         => 3003,
        EventType::MANAGER_SUBMIT_RENDERING                   => 3004,
        EventType::MANAGER_FILL_STATUS                        => 3005,
        EventType::MANAGER_ASSET_DELETE                       => 3006,
        EventType::MANAGER_ASSET_MOVE                         => 3007,
        EventType::MANAGER_STREAMING                          => 3008,
        EventType::MANAGER_BOOKMARKS                          => 3009,
        EventType::MANAGER_SCHEDULING                         => 3010,
        EventType::MANAGER_RENDERING                          => 3011,
        EventType::MANAGER_EXTERNAL_STREAM                    => 3012,
        EventType::MANAGER_ASSET_COPY                         => 3013,
        EventType::MANAGER_MODO_DELETE                        => 3014,
        EventType::MANAGER_EZMAM_CREATE_COURSE                => 3015,
        EventType::MANAGER_SUBSCRIPTION_FORM                  => 3016,
        
        // EZRenderer: 4000->4999
        
        // EZPlayer: 5000->5999
        EventType::EZPLAYER_STREAMING                         => 5000,
    );
}
