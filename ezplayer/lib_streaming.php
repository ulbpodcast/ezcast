<?php

require_once 'config.inc';
require_once 'lib_ezmam.php';
require_once dirname(__FILE__) . '/../commons/lib_template.php';

/**
 * Reloads the streaming player
 * @param type $display
 */
function asset_streaming_player_update($display = true)
{
    global $input;
    global $asset_meta;
    global $m3u8_live_stream;
    global $streaming_video_player;
    global $repository_path;
    global $is_android;
    global $streaming_video_alternate_server_enable_redirect;
    global $streaming_video_alternate_server_redirect_chance;
    global $m3u8_external_master_filename;
    global $m3u8_master_filename;
    global $logger;
    
    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    ezmam_repository_path($repository_path);

    // gets metadata for the selected asset
    $asset_meta = ezmam_asset_metadata_get($album, $asset);
    $asset_token = ezmam_asset_token_get($album, $asset);

    $m3u8_file = $m3u8_master_filename;
    if ($streaming_video_alternate_server_enable_redirect) {
        $random = rand() % 100;
        if ($random < $streaming_video_alternate_server_redirect_chance) {
            $m3u8_file = $m3u8_external_master_filename;
        }
    }
    
    //should contain cam or slide
    if ($asset_meta['record_type'] == 'camslide') {
        $type = isset($input['type']) ? $input['type'] : 'cam';
    } else {
        $type = $asset_meta['record_type'];
    }
    if (!in_array($type, array('cam', 'slide'))) {
        $logger->log(EventType::MANAGER_STREAMING, LogLevel::WARNING, "Trying to use wrong record type '.$type."
                . "'. Resetting to 'cam'", array(__FUNCTION__));
        $type = 'cam';
    }
    
    $base_dir = 'videos/' . suffix_remove($album) . '/' . $asset_meta['stream_name'] . '_' . $asset_token;
    $m3u8_live_stream = "$base_dir/$type/$m3u8_file";
    $m3u8_slide = "$base_dir/slide/$m3u8_file"; //may not exist

    $_SESSION['current_type'] = $type;

    if ($display) { // the whole page must be displayed
        if ($is_android) {
            include_once template_getpath("div_streaming_player_android.php");
        } else {
            include_once template_getpath("div_streaming_player_$streaming_video_player.php");
        }
        return true;
    } else {
        return $asset_meta;
    }
}

/**
 * Refreshes the whole chat (all messages are reloaded)
 * @global array $input
 * @global type $repository_path
 * @global type $chat_messages
 * @param type $display
 * @return boolean
 */
function asset_streaming_chat_update($display = true)
{
    global $input;
    global $repository_path;
    global $chat_messages;

    ezmam_repository_path($repository_path);

    $album = $input['album'];
    $asset = $input['asset'];

    if (!isset($album) || $album == '' || $album == 'undefined') {
        $album = $_SESSION['album'];
    }

    if (!isset($asset) || $asset == '' || $asset == 'undefined') {
        $asset = $_SESSION['asset'];
    }
    $asset_meta = ezmam_asset_metadata_get($album, $asset);

    $chat_messages = messages_select_by_asset($album, $asset_meta['record_date'], $asset);
    // $chat_messages = chat_messages_remove_private($chat_messages);

    $last_message = end($chat_messages);
    if (isset($last_message['id'])) {
        $_SESSION['last_chat_message'] = $last_message['id'];
    }
    $_SESSION['last_chat_update'] = date('Y-m-d H:i:s');

    if ($display) {
        include_once template_getpath('div_chat.php');
        return true;
    } else {
        return $chat_messages;
    }
}

/**
 * Returns the new chat messages since the last request
 * @global array $input
 * @global type $repository_path
 * @global type $chat_messages
 * @param type $display
 * @return boolean
 */
function asset_streaming_chat_get_last($display = true)
{
    global $input;
    global $repository_path;
    global $chat_messages;

    ezmam_repository_path($repository_path);

    $album = (isset($input['album'])) ? $input['album'] : $_SESSION['album'];
    $asset = (isset($input['asset'])) ? $input['asset'] : $_SESSION['asset'];
    $asset_token = (isset($input['asset_token'])) ? $input['asset_token'] : $_SESSION['asset_token'];

    if (!isset($album) || $album == '' || $album == 'undefined') {
        $album = $_SESSION['album'];
    }

    if (!isset($asset) || $asset == '' || $asset == 'undefined') {
        $asset = $_SESSION['asset'];
    }

    if (!isset($_SESSION['last_chat_message'])) {
        $_SESSION['last_chat_message'] = 0;
    }
    $asset_meta = ezmam_asset_metadata_get($album, $asset);

    if (!isset($_SESSION['last_chat_update'])) {
        $chat_messages = messages_select_by_asset($album, $asset_meta['record_date'], $asset);
    } else {
        $chat_messages = messages_select_by_date($album, $asset_meta['record_date'], $_SESSION['last_chat_update'], $_SESSION['last_chat_message']);
    }

    $last_message = end($chat_messages);
    if (isset($last_message['id'])) {
        $_SESSION['last_chat_message'] = $last_message['id'];
    }
    $_SESSION['last_chat_update'] = date('Y-m-d H:i:s');

    // $chat_messages = chat_messages_remove_private($chat_messages);
    if ($display) {
        include_once template_getpath('div_chat_messages.php');
        return true;
    } else {
        return $chat_messages;
    }
}

/**
 * Displays the asset details and video player for live HLS stream
 */
function asset_streaming_view($refresh_center = true)
{
    global $input;
    global $repository_path;
    global $album;
    global $asset_meta;
    global $chat_messages;
    global $m3u8_live_stream;
    global $is_android;
    global $logger;
    
    //$logger->log(EventType::TEST, LogLevel::ERROR, "test", array(basename(__FILE__));
    
    global $login_error; // used to display error when anonymous user login
    // the session has expired, the whole page has to be refreshed
    if (isset($_SESSION['reloaded']) && $_SESSION['reloaded']) {
        unset($input['click']);
        unset($_SESSION['reloaded']);
        $refresh_center = true;
    }

    // Setting up various variables we'll need later
    $album = (isset($input['album'])) ? $input['album'] : $_SESSION['album'];
    $asset = (isset($input['asset'])) ? $input['asset'] : $_SESSION['asset'];
    $asset_token = (isset($input['asset_token'])) ? $input['asset_token'] : $_SESSION['asset_token'];

    // init paths
    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($album) || !ezmam_album_exists($album)) {
        if ($input['click']) { // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        } else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_streaming: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        if ($input['click']) {
            include_once template_getpath('error_asset_not_found.php');
        } else {
            $error_path = template_getpath('error_asset_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_streaming: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    if (!acl_user_is_logged() || !acl_has_album_permissions($album)) {
        // either the user is not logged in or he doesn't have access to the album
        if (!ezmam_asset_token_check($album, $asset, $asset_token)) {
            if ($input['click']) { // refresh a part of the page
                include_once template_getpath('error_permission_denied.php');
            } else { // refresh the whole page
                $error_path = template_getpath('error_permission_denied.php');
                include_once template_getpath('main.php');
            }
            log_append('warning', 'view_asset_streaming: tried to access asset ' . $input['asset'] .
                    'in album ' . $input['album'] . ' with invalid token ' . $input['asset_token']);
            die;
        }
    }

    if (!isset($asset_token) || $asset_token == '') {
        $asset_token = ezmam_asset_token_get($album, $asset);
    }

    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['asset_token'] = $asset_token;

    $asset_meta = asset_streaming_player_update(false);
    $chat_messages = asset_streaming_chat_update(false);
    
    $is_android = strtolower($_SESSION["user_os"]) == "android" && version_compare("4.2", $_SESSION["user_os_version"], ">");


    //  $m3u8_live_stream = 'videos/life.m3u8';
    log_append('view_asset_streaming: album = ' . $album . ", asset = " . $asset);
    $_SESSION['ezplayer_mode'] = 'view_asset_streaming';

    if ($refresh_center) { // the whole page must be displayed
        if ($input['click']) { // called from a local link
            $origin = 'from_ezplayer';
            include_once template_getpath("div_streaming_center.php");
        } else {// called from the UV or a shared link
            $origin = 'from_external';
            include_once template_getpath('main.php');
        }
        // lvl, action, album, asset, record type (cam|slide|camslide), permissions (view_only | add personal), origin
        trace_append(array('3', 'view_asset_streaming', $album, $asset, $asset_meta['record_type'], 'view_only', $origin));
    }
}
