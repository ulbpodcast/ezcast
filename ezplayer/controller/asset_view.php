<?php

/**
 * Displays the asset details and video player
 * If $seek is true, user clicked on a bookmark or a thread timecode and
 * the player should be loaded at a specific timecode
 * @global type $input
 * @global type $appname
 * @global type $user_files_path
 * @global type $repository_path
 * @global type $ezplayer_url
 * @global type $album the current album
 * @global type $asset_meta metadata for the current asset
 * @global type $has_bookmark determines that the user is logged and has access to the album
 * @global type $asset_bookmarks list of personal bookmarks for the current asset
 * @global type $toc_bookmarks list of official bookmarks for the current asset
 * @global type $timecode the current timecode
 * @global type $default_personal_bm_order
 * @global type $default_official_bm_order
 * @global type $login_error
 * @param boolean $refresh_center determines if the whole page must be refreshed or only the bookmarks on the right
 * @param type $seek true if the user specifies a timecode (click on bookmark or thread); false otherwise
 */
function index($param = array())
{
    global $input;
    global $appname;
    global $user_files_path;
    global $repository_path;
    global $album;
    global $asset_meta;
    global $has_bookmark;
    global $timecode;
    global $login_error; // used to display error when anonymous user login
    // determines if the user is logged and has access to the selected album
    $has_bookmark = false;
    $seek = (count($param) == 1 && $param[0]);
    $ezplayer_mode = ($seek) ? 'view_asset_bookmark' : 'view_asset_details';
    
    // the session has expired, the whole page has to be refreshed
    if (isset($_SESSION['reloaded'])) {
        unset($input['click']);
        unset($_SESSION['reloaded']);
        $refresh_center = true;
    }

    // Setting up various variables we'll need later
    if (isset($input['album'])) {
        $album = $input['album'];
    } else {
        $album = $_SESSION['album'];
    }

    if (isset($input['asset'])) {
        $asset = $input['asset'];
    } else {
        $asset = $_SESSION['asset'];
    }

    if ($seek && isset($input['t'])) {
        $timecode = $input['t'];
    } else {
        $timecode = 0;
    }

    if ($seek && isset($input['thread_id'])) {
        $thread_id = $input['thread_id'];
    }

    if (isset($input['asset_token'])) {
        $asset_token = $input['asset_token'];
    } else {
        $asset_token = $_SESSION['asset_token'];
    }

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

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
        log_append('warning', $ezplayer_mode . ': tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        if ($input['click']) {
            include_once template_getpath('error_asset_not_found.php');
        } else {
            $error_path = template_getpath('error_asset_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', $ezplayer_mode . ': tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    if (acl_user_is_logged() && acl_has_album_permissions($album)) {
        // the user has access to the album so we don't need a token
        $has_bookmark = true;
    } else {
        // either the user is not logged in or he doesn't have access to the album
        if (!ezmam_asset_token_check($album, $asset, $asset_token)) {
            if ($input['click']) { // refresh a part of the page
                include_once template_getpath('error_permission_denied.php');
            } else { // refresh the whole page
                $error_path = template_getpath('error_permission_denied.php');
                include_once template_getpath('main.php');
            }
            log_append('warning', $ezplayer_mode . ': tried to access asset ' . $input['asset'] . 'in album ' . $input['album'] . ' with invalid token ' .
                    $input['asset_token']);
            die;
        }
    }

    // adds the asset to the watched assets list if needed
    if (acl_user_is_logged()) {
        if (user_prefs_watched_add($_SESSION['user_login'], $album, $asset) && acl_show_notifications()) {
            acl_update_watched_assets();
        }
    }
    
    $album_title = ezmam_album_metadata_get($album);
    if (isset($album_title['course_code_public'])) {
        $course_code_public = $album_title['course_code_public'];
    }
    // gets metadata for the selected asset

    $asset_meta = ezmam_asset_metadata_get($album, $asset);

    // prepares the different sources for the video HTML5 tag
    if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'cam') {
        $asset_meta['high_cam_src'] = get_link_to_media($album, $asset, 'high_cam');
        $asset_meta['low_cam_src'] = get_link_to_media($album, $asset, 'low_cam');
        // #t=$timecode stands for W3C temporal Media Fragments URI (working in Firefox and Chrome)
        $asset_meta['src'] = $asset_meta['low_cam_src'] . '&origin=' . $appname . "#t=" . $timecode;
    }

    if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'slide') {
        $asset_meta['high_slide_src'] = get_link_to_media($album, $asset, 'high_slide');
        $asset_meta['low_slide_src'] = get_link_to_media($album, $asset, 'low_slide');
        if ($asset_meta['record_type'] == 'slide') {
            $asset_meta['src'] = $asset_meta['low_slide_src'] . '&origin=' . $appname . "#t=" . $timecode;
        }
    }
    /*
      // user is logged and has access to the selected album
      if ($has_bookmark) {
      // prepares all bookmarks for the selected asset (displayed in 'div_right_details.php')
      $asset_bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
      // sorts the bookmarks following user's prefs
      $order = acl_value_get("personal_bm_order");
      if (isset($order) && $order != '' && $order != $default_personal_bm_order) {
      $asset_bookmarks = array_reverse($asset_bookmarks);
      }
      }

      // prepares the table of contents for the selected asset (displayed in 'div_right_details.php')
      $toc_bookmarks = toc_asset_bookmark_list_get($album, $asset);
      // sorts the bookmarks following user's prefs
      $order = acl_value_get("official_bm_order");
      if (isset($order) && $order != '' && $order != $default_official_bm_order) {
      $toc_bookmarks = array_reverse($toc_bookmarks);
      }
     */
    log_append($ezplayer_mode . ': album = ' . $album . ", asset = " . $asset);
    $_SESSION['ezplayer_mode'] = $ezplayer_mode; // used in div_thread_details.php
    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['asset_meta'] = $asset_meta;
    $_SESSION['timecode'] = $timecode;
    if ($seek && isset($thread_id)) {
        $_SESSION['current_thread'] = $thread_id;
    }
    $_SESSION['asset_token'] = $asset_token;
    
    if ($seek) {
        if (array_key_exists('type', $input)) {
            $_SESSION['loaded_type'] = $input['type'];
        }
    } else {
        if ($asset_meta['record_type'] == 'camslide') {
            $_SESSION['loaded_type'] = 'cam';
        } else {
            $_SESSION['loaded_type'] = $asset_meta['record_type'];
        }
    }
    

    bookmarks_list_update(false, $official_bookmarks, $personal_bookmarks);

    if (acl_display_threads()) {
        if (isset($thread_id)) {
            // click from lvl 2 on a discussion
            $thread = thread_details_update(false);
            $_SESSION['thread_display'] = 'details';
        } else {
            // click from lvl 2 on a bookmark
            $threads = threads_select_by_asset($album, $asset);
            $_SESSION['thread_display'] = 'list';
        }
    }
    if (array_key_exists('click', $input) && $input['click']) { // called from a local link
        // lvl, action, album, asset, record type (cam|slide|camslide), permissions (view official | add personal), origin
        trace_append(array('3', $ezplayer_mode, $album, $asset, $asset_meta['record_type'],
            ($has_bookmark) ? 'view_and_add' : 'view_only', 'from_ezplayer'));
        include_once template_getpath('div_assets_center.php');
    } else {// called from the UV or a shared link
        if (!array_key_exists('no_trace', $input) || !$input['no_trace']) {
            trace_append(array('3', $ezplayer_mode, $album, $asset, $asset_meta['record_type'],
                ($has_bookmark) ? 'view_and_add' : 'view_only', 'from_external'));
        }
        
        include_once template_getpath('main.php');
    }
}
