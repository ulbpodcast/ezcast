<?php

/**
 * Called by client to save a use trace
 */
function index($param = array())
{
    global $input;
    
    $infos = $input['info'];
    if (count($infos) <= 1) {
        echo 'Trace error (not level and action) !';
        return false;
    }
    
    $nbr_param = 2;
    $action = $infos[1];
    switch ($action) {
        case 'thread_detail_from_trending':
            $nbr_param = 6;
            break;
        
        case 'thread_detail_show':
            $nbr_param = 5;
            break;
        
        case 'settings_hide':
        case 'settings_show':
        case 'contact_hide':
        case 'contact_show':
            $nbr_param = 4;
            break;
        
        case 'bookmark_hide':
        case 'bookmark_show':
            $nbr_param = 5;
            break;
        
        case 'video_playing':
            $nbr_param = 6;
            break;
        
        case 'video_seeked':
            $nbr_param = 9;
            break;
        
        case 'video_switch':
            $nbr_param = 10;
            break;
        
        case 'video_quality':
            $nbr_param = 9;
            break;
        
        case 'video_bookmark_click':
            $nbr_param = 11;
            break;
        
        case 'playback_speed_up':
        case 'playback_speed_down':
            $nbr_param = 10;
            break;
        
        case 'video_mute':
            $nbr_param = 10;
            break;
        
        case 'bookmark_form_show':
            $nbr_param = 10;
            break;
        
        case 'video_play':
        case 'video_pause':
        case 'link_open':
        case 'video_rewind':
        case 'video_forward':
        case 'video_volume_up':
        case 'video_volume_down':
        case 'bookmark_form_hide':
        case 'thread_form_hide':
        case 'thread_form_show':
        case 'video_fullscreen_enter':
        case 'video_fullscreen_exit':
        case 'panel_hide':
        case 'panel_show':
        case 'shortcuts_hide':
        case 'shortcuts_show':
            $nbr_param = 9;
            break;
        
        case 'browser_fullscreen_enter':
        case 'browser_fullscreen_exit':
            $nbr_param = 8;
            break;
        
        
        case 'comment_form_show':
        case 'comment_form_hide':
            $nbr_param = 7;
            break;
        
        case 'answer_form_hide':
        case 'answer_form_show':
            $nbr_param = 8;
            break;
        
        case 'thread_list_refresh':
        case 'thread_list_back':
            $nbr_param = 4;
            break;
        
        case 'thread_detail_refresh':
        case 'thread_detail_from_notif':
            $nbr_param = 5;
            break;
        
        case 'bookmarks_swap':
            $nbr_param = 5;
            break;
        
        case 'slide_download_open':
        case 'cam_download_open':
            $nbr_param = 8;
            break;
        
        case 'cam_download':
        case 'slide_download':
            $nbr_param = 6;
            break;
        
        case 'description_link':
            $nbr_param = 5;
            break;
        
        case 'hashtag_click':
            $nbr_param = 5;
            break;
        
        case 'link_copy':
            $nbr_param = 8;
            break;
        
        case 'video_play_time':
            $nbr_param = 8;
            break;
        
    }
    
    if (count($infos) == $nbr_param) {
        trace_append($infos);
    }
}
