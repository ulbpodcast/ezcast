<?php

/**
 * Displays the asset details and video player for live HLS stream
 */
function index($param = array()) {
    global $input;
    global $repository_path;
    global $album;
    global $asset_meta;
    global $chat_messages;
    global $m3u8_live_stream;
    global $is_android;
    
    $refresh_center = (count($param) == 0 || $param[0]);
    
    global $login_error; // used to display error when anonymous user login
    // the session has expired, the whole page has to be refreshed
    if ($_SESSION['reloaded']) {
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
        if ($input['click']) // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_streaming: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        if ($input['click'])
            include_once template_getpath('error_asset_not_found.php');
        else {
            $error_path = template_getpath('error_asset_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_streaming: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    if (!acl_user_is_logged() || !acl_has_album_permissions($album)) {
        // either the user is not logged in or he doesn't have access to the album
        if (!ezmam_asset_token_check($album, $asset, $asset_token)) {
            if ($input['click']) // refresh a part of the page
                include_once template_getpath('error_permission_denied.php');
            else { // refresh the whole page
                $error_path = template_getpath('error_permission_denied.php');
                include_once template_getpath('main.php');
            }
            log_append('warning', 'view_asset_streaming: tried to access asset ' . $input['asset'] . 'in album ' . $input['album'] . ' with invalid token ' . $input['asset_token']);
            die;
        }
    }

    if (!isset($asset_token) || $asset_token == '')
        $asset_token = ezmam_asset_token_get($album, $asset);

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
            // lvl, action, album, asset, record type (cam|slide|camslide), permissions (view official | add personal), origin
            trace_append(array('3', 'view_asset_streaming', $album, $asset, $asset_meta['record_type'], 'view_only', 'from_ezplayer'));
            include_once template_getpath("div_streaming_center.php");
        } else {// called from the UV or a shared link
            trace_append(array('3', 'view_asset_streaming', $album, $asset, $asset_meta['record_type'], 'view_only', 'from_external'));
            include_once template_getpath('main.php');
        }
    }
}