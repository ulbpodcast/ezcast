<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * EZCAST EZplayer main program (MVC Controller)
 *
 */

/**
 * ezcast podcast manager main program (MVC Controller)
 * @package ezcast.ezplayer.main
 */
require_once 'config.inc';
session_name($appname);
session_start();
require_once 'lib_error.php';
require_once 'lib_ezmam.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_template.php';
require_once '../commons/lib_various.php';
require_once 'lib_various.php';
require_once 'lib_user_prefs.php';
include_once 'lib_toc.php';
require_once './Browser/Autoloader.php';
require_once 'lib_threads_pdo.php';
require_once 'lib_cache.php';
require_once 'lib_acl.php';

$input = array_merge($_GET, $_POST);

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

//
// Login/logout
//
// Saves the URL used to access the website
if (!isset($_SESSION['first_input']) && isset($input['action']) && $input['action'] != 'logout' && $input['action'] != 'login') {
    $_SESSION['first_input'] = array_merge($_GET, $_POST);
}
// Saves user's web browser information
if (!isset($_SESSION['browser_name']) || !isset($_SESSION['browser_version']) || !isset($_SESSION['user_os'])) {

    Autoloader::register();
    $browser = new Browser;
    $os = new Os;
    $_SESSION['browser_name'] = $browser->getName();
    $_SESSION['browser_version'] = $browser->getVersion();
    $user_agent = $browser->getUserAgent();
    $_SESSION['browser_full'] = $user_agent->getUserAgentString();
    $_SESSION['user_os'] = $os->getName();
    
}
// If we're not logged in, we try to log in or display the login form
if (!user_logged_in()) {
    // if the url contains the parameter 'anon' the session is assumed as anonymous

    if (isset($input['anon']) && $input['anon'] == true) {
        user_anonymous_session();
    }
    // Step 2: Logging in a user who already submitted the form
    // The user can continue without any authentication. Then, it'll be an anonymous session.
    else if (isset($input['action']) && $input['action'] == 'login') {
        // The user continues without any authentication
        if (isset($_POST['anonymous_session'])) {
            user_anonymous_session();

            // The user want to authenticate
        } else {
            if (!isset($input['login']) || !isset($input['passwd'])) {
                error_print_message(template_get_message('empty_username_password', get_lang()));
                die;
            }
            user_login(trim($input['login']), trim($input['passwd']));
        }
    }

    // This is a tricky case:
    // If we do not have a session, but we have an action, that means we lost the
    // session somehow and are trying to load part of a page through AJAX call.
    // We do not want the login page to be displayed randomly inside a div,
    // so we refresh the whole page to get a full-page login form.
    //
    // $input['click'] indicates that the action comes from a link in the application
    else if (isset($input['action']) && $input['click']) {
        refresh_page();
    }
    // Step 1: Displaying the login form
    // (happens if no "action" is provided)
    else {
        view_login_form();
    }
}

// At this point of the code, the user is supposed to be logged in.
// We check whether they specified an action to perform. If not, it means they landed
// here through a page reload, so we check the session variables to restore the page as it was.
else if (isset($_SESSION['ezplayer_logged']) && (!isset($input['action']) || empty($input['action']))) {
    redraw_page();
}

// At this point of the code, the user is logged in and explicitly specified an action.
// We perform the action specified.
else {
    load_page();
}

//
// Helper functions
//

/**
 * Used to load the requested page from POST or GET
 * @global type $input
 */
function load_page() {
    global $input;
    $action = $input['action'];
    $redraw = false;

    //
    // Actions
    //
    // Controller goes here
    switch ($action) {
        // The user clicked on an album, we display its content to them
        // Display the help page
        case 'view_help':
            view_help();
            break;
        
        case 'view_settings':
            view_settings();
            break;
        
        // In case we want to log out
        case 'logout':
            user_logout();
            break;

        // The only case when we could possibly arrive here with a session created
        // and a "login" action is when the user refreshed the page. In that case,
        // we redraw the page with the last information saved in the session variables.
        case 'login':
            redraw_page();
            break;

        case 'anonymous_login':
            anonymous_login();
            break;

        case 'view_album_assets':
            view_album_assets();
            break;

        case 'view_asset_details':
            view_asset_details();
            break;

        case 'view_asset_bookmark':
            view_asset_bookmark();
            break;

        case 'search_bookmark':
            bookmarks_search();
            break;

        case 'sort_asset_bookmark':
            bookmarks_sort();
            break;

        case 'add_asset_bookmark':
            bookmark_add();
            break;

        case 'add_asset_thread':
            thread_add();
            break;

        case 'add_thread_comment':
            comment_add();
            break;

        case 'add_thread_comment_answer':
            comment_add_reply();
            break;

        case 'update_thread_comment':
            comment_edit();
            break;

        case 'update_asset_thread':
            thread_edit();
            break;

        case 'thread_details_view':
            thread_details_update();
            break;

        case 'delete_asset_thread':
            thread_delete();
            break;

        case 'delete_thread_comment':
            comment_delete();
            break;

        case 'edit_settings':
            preferences_update();
            break;

        case 'edit_asset_meta':
            asset_edit_meta();
            break;

        case 'vote':
            vote_add();
            break;

        case 'approve':
            comment_edit_aproval();
            break;

        case 'threads_list_view':
            threads_list_update();
            break;

        case 'copy_bookmark':
            bookmark_copy();
            break;

        case 'remove_asset_bookmark':
            bookmark_delete();
            break;

        case 'remove_asset_bookmarks':
            bookmarks_delete_all();
            break;

        case 'view_import':
            view_import();
            break;

        case 'upload_bookmarks':
            bookmarks_upload();
            break;

        case 'import_bookmarks':
            bookmarks_import();
            break;

        case 'export_bookmarks':
            bookmarks_export();
            break;

        case 'export_album_bookmarks':
            bookmarks_export_all();
            break;

        case 'export_asset_bookmarks':
            bookmarks_export_all(true);
            break;

        case 'delete_bookmarks':
            bookmarks_delete();
            break;

        case 'move_album_token':
            album_token_move();
            break;

        case 'delete_album_token':
            album_token_delete();
            break;

        case 'client_trace':
            client_trace();
            break;
        
        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            view_main();
    }
}

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in() {
    return (isset($_SESSION['ezplayer_logged']) || isset($_SESSION['ezplayer_anonymous']));
}

//
// Display functions
//

/**
 * Displays the login form
 */
function view_login_form() {
    global $ezplayer_url;
    global $error, $input;

    //check if we receive a no_flash parameter (to disable flash progressbar on upload)
    if (isset($input['no_flash']))
        $_SESSION['has_flash'] = false;
    $url = $ezplayer_url;
    // template include goes here
    include_once template_getpath('login.php');
    //include_once "tmpl/fr/login.php";
}

/**
 * $refresh_page is used to determine if we need to refresh the whole page 
 * or just a part of the page
 * Displays the main frame
 */
function view_main($refresh_page = true) {
    // Used in redraw mode only
    global $repository_path;
    global $user_files_path;
    global $albums;  // used in 'div_main_center.php'
    global $message_of_the_day;
    global $login_error;
    global $trace_on;

    $_SESSION['show_message'] = false;
    if (!isset($_SESSION['day_message'])) {
        if (file_exists($message_of_the_day)) {
            $_SESSION['day_message'] = file_get_contents($message_of_the_day);
            if ($_SESSION['day_message'] != null || $_SESSION['day_message'] != '') {
                $_SESSION['show_message'] = true;
            }
        }
    }
    $_SESSION['ezplayer_mode'] = 'view_main'; // used in 'main.php' and 'div_search.php'
    $_SESSION['album'] = ''; // no album selected
    $_SESSION['asset'] = ''; // no asset selected
    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);
    if (acl_user_is_logged()) {
        // loads all public albums of the user 
        $moderated_albums = array_keys(acl_moderated_albums_list());
        $moderated_tokens = array();
        foreach ($moderated_albums as $index => $album) {
            $moderated_tokens[$index]['album'] = $album . '-pub';
            $moderated_tokens[$index]['title'] = get_album_title($album . '-pub');
            $moderated_tokens[$index]['token'] = ezmam_album_token_get($album . '-pub');
        }
        // add the list of moderated public albums 
        user_prefs_tokens_add($_SESSION['user_login'], $moderated_tokens);
        acl_update_permissions_list();
    }
    // albums to display on the home page
    $albums = acl_authorized_album_tokens_list();

    if ($refresh_page) {
        log_append('View home page from link');
        trace_append(array('1', 'view_albums')); // lvl, action
        include_once template_getpath('main.php');
    } else {
        log_append('View home page after album token action');
        include_once template_getpath('div_main_center.php');
    }
}

/**
 * This function is called whenever the user chose to refresh the page.
 * It loads the last album viewed, but not the asset details.
 * @global type $repository_path 
 */
function redraw_page() {
    global $repository_path;
    global $action;
    global $redraw;
    global $current_album;
    global $current_album_is_public;
    global $album_name;
    global $album_name_full;
    global $description;
    global $public_album;
    global $assets;
    global $distribute_url;
    ezmam_repository_path($repository_path);

    $action = $_SESSION['ezplayer_mode'];
    $redraw = true;

    // Whatever happens, the first thing to do is display the whole page.
    view_main();
}

/**
 * Reloads the whole page
 */
function refresh_page() {
    global $ezplayer_url;
    // session var to determine the whole page has to be reloaded
    $_SESSION['reloaded'] = true;
    // reload the page
    echo '<script>window.location.reload();</script>';
        trace_append(array('0', 'refresh_page')); // lvl, action
    die;
}

/**
 * Displays the list of all assets from the selected album
 * @refresh_center determines if we need to refresh the whole page / the center 
 * of the page or another part of the page (mainly the right side)
 * @global type $input
 * @global type $repository_path
 * @global type $ezplayer_url
 * @global type $assets_list
 * @global string $panel_display
 */
function view_album_assets($refresh_center = true) {
    global $input;
    global $repository_path;
    global $ezplayer_url; // used in a popup window
    global $user_files_path;
    global $assets_list;
    global $album;
    global $album_bookmarks;
    global $toc_bookmarks;
    global $error_path; // used to display an error on the main page
    global $login_error; // used to display error when anonymous user login
    global $default_bookmarks_order;
    global $default_toc_order;
    global $cache_limit;


    // if reloaded is set, the whole page has to be refreshed
    if ($_SESSION['reloaded']) {
        unset($input['click']);
        unset($_SESSION['reloaded']);
        $refresh_center = true;
    }

    $error_path = '';

    if (isset($input['album'])) {
        $album = $input['album'];
    } else {
        $album = $_SESSION['album'];
    }

    if (isset($input['token'])) {
        $token = $input['token'];
    } else {
        $token = $_SESSION['token'];
    }

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // 0) Sanity checks

    if (!ezmam_album_exists($album)) {
        if ($input['click']) // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_album_assets: tried to access non-existant album ' . $input['album']);
        exit;
    }

    // Authorization check
    if (!ezmam_album_token_check($album, $token)) {

        if ($input['click'])
            include_once template_getpath('error_permission_denied.php');
        else {
            $error_path = template_getpath('error_permission_denied.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_album_assets: tried to access album ' . $input['album'] . ' with invalid token ' . $input['token']);
        die;
    }


    // 1) Retrieving all assets' metadata

    $assets_list = ezmam_asset_list_metadata($album);
    $count = count($assets_list);

    // add the asset token to the metadata
    for ($index = 0; $index < $count; $index++) {
        $assets_list[$index]['token'] = ezmam_asset_token_get($album, $assets_list[$index]['name']);
    }

    // 2) Save current album    

    log_append('view_album_assets: ' . $album);
    $_SESSION['ezplayer_mode'] = 'view_album_assets'; // used in 'div_assets_center.php'
    $_SESSION['album'] = $album; // used in search
    $_SESSION['asset'] = '';
    $_SESSION['token'] = $token;

    // 3) Add current album to the album list
    //    and load album bookmarks
    //    
    $album_name = get_album_title($album);
    $album_token = array('title' => $album_name, 'album' => $album, 'token' => $token);
    if (!token_array_contains($_SESSION['acl_album_tokens'], $album_token)) {
        if (acl_user_is_logged()) {
            // logged user : consulted albums are stored in file
            user_prefs_token_add($_SESSION['user_login'], $album, $album_name, $token);
            log_append('view_album_assets: album token added - ' . $album);
            trace_append(array('2', 'album_token_add', $album)); // lvl, action, album
        } else {
            // anonymous user : consulted albums are stored in session var
            $_SESSION['acl_album_tokens'][] = $album_token;
        }
        acl_update_permissions_list();
    }

    if (acl_user_is_logged()) {
        // bookmarks to display in 'div_side_assets.php'
        $album_bookmarks = user_prefs_album_bookmarks_list_get($_SESSION['user_login'], $album);
        // sorts the bookmarks following user's prefs
        $order = acl_value_get("bookmarks_order");
        if (isset($order) && $order != '' && $order != $default_bookmarks_order) {
            $album_bookmarks = array_reverse($album_bookmarks);
        }
    }

    // 4) table of contents to display in 'div_side_assets.php'
    $toc_bookmarks = toc_album_bookmarks_list_get($album);
    // sorts the bookmarks following user's prefs
    $order = acl_value_get("toc_order");
    if (isset($order) && $order != '' && $order != $default_toc_order) {
        $toc_bookmarks = array_reverse($toc_bookmarks);
    }

    if ($refresh_center) {

        if (acl_display_threads()) {
            $threads = threads_select_by_album($album, $cache_limit);
            foreach ($threads as &$thread) {
                if (!thread_is_archive($thread['albumName'], $thread['assetName']))
                    $threads_list[] = $thread;
            }
        }

        if ($input['click']){ // called by a local link
            // lvl, action, album, origin
            trace_append(array('2', 'view_album_assets', $album, 'from_ezplayer'));
            include_once template_getpath('div_assets_center.php');
        } else {// accessed by the UV or shared link
            // lvl, action, album, origin
            trace_append(array('2', 'view_album_assets', $album, 'from_external'));
            include_once template_getpath('main.php');
        }
    } else { // refresh only the side panel (after import / export / deletion / ...)
        include_once template_getpath('div_side_assets.php');
    }
}

/**
 * Shows the asset details div for the asset passed by POST, GET or SESSION
 * @global type $input
 * @global type $repository_path 
 */
function view_asset_details($refresh_center = true) {
    global $input;
    global $appname;
    global $repository_path;
    global $ezplayer_url; // used in a popup window
    global $asset_meta;
    global $album;
    global $user_files_path;
    global $is_bookmark;
    global $asset_bookmarks;
    global $toc_bookmarks;
    global $default_bookmarks_order;
    global $default_toc_order;
    global $login_error; // used to display error when anonymous user login
    // determines if the user is logged and has access to the selected album
    $is_bookmark = false;
    // used in 'div_left_details.php' to precise the video has to be loaded
    $_SESSION['load_video'] = true;

    // the session has expired, the whole page has to be refreshed
    if ($_SESSION['reloaded']) {
        unset($input['click']);
        unset($_SESSION['reloaded']);
        $refresh_center = true;
    }

    // Setting up various variables we'll need later
    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['album'];

    if (isset($input['asset']))
        $asset = $input['asset'];
    else
        $asset = $_SESSION['asset'];

    if (isset($input['asset_token']))
        $asset_token = $input['asset_token'];
    else
        $asset_token = $_SESSION['asset_token'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    //
    // 0) Sanity checks
    //
    if (!isset($album) || !ezmam_album_exists($album)) {
        if ($input['click']) // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_details: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        if ($input['click'])
            include_once template_getpath('error_asset_not_found.php');
        else {
            $error_path = template_getpath('error_asset_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_details: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    // the user has access to the album so we don't need a token
    if (acl_user_is_logged() && acl_has_album_permissions($album)) {
        //saves the asset token for the link to display in "share the asset" in left_details.php
        $asset_token = ezmam_asset_token_get($album, $asset);
        $is_bookmark = true;
    } else { // either the user is not logged in or he doesn't have access to the album
        if (!ezmam_asset_token_check($album, $asset, $asset_token)) {

            if ($input['click']) // refresh a part of the page
                include_once template_getpath('error_permission_denied.php');
            else { // refresh the whole page
                $error_path = template_getpath('error_permission_denied.php');
                include_once template_getpath('main.php');
            }
            log_append('warning', 'view_asset_details: tried to access asset ' . $input['asset'] . 'in album ' . $input['album'] . ' with invalid token ' . $input['asset_token']);
            die;
        }
    }

    if (acl_user_is_logged()) {
        if (user_prefs_watched_add($_SESSION['user_login'], $album, $asset)) {
            acl_update_permissions_list();
        }
    }

    // 1) info for the selected asset

    $asset_meta = ezmam_asset_metadata_get($album, $asset);

    if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'cam') {
        $asset_meta['high_cam_src'] = get_link_to_media($album, $asset, 'high_cam');
        $asset_meta['low_cam_src'] = get_link_to_media($album, $asset, 'low_cam');
        $asset_meta['src'] = $asset_meta['low_cam_src'] . '&origin=' . $appname . "#t=" . $input['t'];
    }

    if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'slide') {
        $asset_meta['high_slide_src'] = get_link_to_media($album, $asset, 'high_slide');
        $asset_meta['low_slide_src'] = get_link_to_media($album, $asset, 'low_slide');
        if ($asset_meta['record_type'] == 'slide') {
            $asset_meta['src'] = $asset_meta['low_slide_src'] . '&origin=' . $appname . "#t=" . $input['t'];
        }
    }

    if ($is_bookmark) {
        // loads all bookmarks for the selected asset (displayed in 'div_side_details.php')
        $asset_bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
        // sorts the bookmarks following user's prefs
        $order = acl_value_get("bookmarks_order");
        if (isset($order) && $order != '' && $order != $default_bookmarks_order) {
            $asset_bookmarks = array_reverse($asset_bookmarks);
        }
    }

    // loads the table of contents for the selected asset (displayed in 'div_side_details.php')
    $toc_bookmarks = toc_asset_bookmark_list_get($album, $asset);
    // sorts the bookmarks following user's prefs
    $order = acl_value_get("toc_order");
    if (isset($order) && $order != '' && $order != $default_toc_order) {
        $toc_bookmarks = array_reverse($toc_bookmarks);
    }

    log_append('view_asset_details: album = ' . $album . ", asset = " . $asset);
    $_SESSION['ezplayer_mode'] = 'view_asset_details';
    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['asset_token'] = $asset_token;

    if ($refresh_center) { // the whole page must be displayed
        if (acl_display_threads()) {
            $threads = threads_select_by_asset($album, $asset);
            $_SESSION['thread_display'] = 'list';
        }
        if ($input['click']){ // called from a local link
            // lvl, action, album, asset, record type (cam|slide|camslide), permissions (view official | add personal), origin
            trace_append(array('3', 'view_asset_details', $album, $asset, $asset_meta['record_type'], ($is_bookmark) ? 'view_and_add' :  'view_only','from_ezplayer'));
            include_once template_getpath('div_assets_center.php');
        } else {// called from the UV or a shared link
            trace_append(array('3', 'view_asset_details', $album, $asset, $asset_meta['record_type'], ($is_bookmark) ? 'view_and_add' :  'view_only','from_external'));
            include_once template_getpath('main.php');
        }
    } else { // only the bookmarks on the right must be refreshed 
        // i.e: after bookmark add / edit / import / export / ...
        $_SESSION['load_video'] = false;
        include_once template_getpath('div_side_details.php');
    }
}

/**
 * Displays the asset details and bookmarks.
 * This function is called when a user selects a bookmark from the bookmarks 
 * tab in the assets page or when a user shares a link to a specific timecode 
 * with an other user.
 * @global type $input
 * @global type $repository_path
 * @global type $asset_meta
 * @global string $panel_display
 * @global type $album
 * @global type $user_files_path
 * @global type $is_bookmark
 * @global type $asset_bookmarks
 * @global type $timecode
 * @param type $refresh_center
 */
function view_asset_bookmark($refresh_center = true) {
    global $appname;
    global $input;
    global $asset_meta;
    global $album;
    global $user_files_path;
    global $repository_path;
    global $ezplayer_url; // used in a popup window
    global $is_bookmark;
    global $asset_bookmarks;
    global $toc_bookmarks;
    global $default_bookmarks_order;
    global $default_toc_order;
    global $timecode;
    global $error_asset;
    global $login_error; // used to display error when anonymous user login
    // the video will be loaded by a different way
    $_SESSION['load_video'] = false;
    // determines if the user is logged and has album authorization
    $is_bookmark = false;
    $error_asset = '';

    // session has expired, the whole page has to be refreshed
    if ($_SESSION['reloaded']) {
        unset($input['click']);
        unset($_SESSION['reloaded']);
        $refresh_center = true;
    }

    // Setting up various variables we'll need later
    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['album'];

    if (isset($input['asset']))
        $asset = $input['asset'];
    else
        $asset = $_SESSION['asset'];

    if (isset($input['t']))
        $timecode = $input['t'];
    else
        $timecode = $_SESSION['timecode'];

    if (isset($input['thread_id']))
        $thread_id = $input['thread_id'];
    
    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    //
    // 0) Sanity checks
    //
    if (!isset($album) || !ezmam_album_exists($album)) {

        if ($input['click']) // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_bookmark: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        $error_asset = $asset;
        if ($input['click'])
            include_once template_getpath('error_asset_not_found.php');
        else {
            $error_path = template_getpath('error_asset_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_asset_bookmark: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    // only users who have album authorization can access a specific bookmark
    // anonymous users cannot access a shared bookmark
    if (acl_has_album_permissions($album)) {
        $is_bookmark = true;
    } else {
        if (!ezmam_asset_token_check($album, $asset, $_SESSION['asset_token'])) {
            if ($input['click'])
                include_once template_getpath('error_permission_denied.php');
            else {
                $error_path = template_getpath('error_permission_denied.php');
                include_once template_getpath('main.php');
            }
            log_append('warning', 'view_asset_bookmark: no permission to asset ' . $input['asset'] . 'in album ' . $input['album']);
            die;
        }
    }

    if (acl_user_is_logged()) {
        if (user_prefs_watched_add($_SESSION['user_login'], $album, $asset)) {
            acl_update_permissions_list();
        }
    }

    // 1) info for the selected asset

    $asset_meta = ezmam_asset_metadata_get($album, $asset);

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
            // #t=$timecode stands for W3C temporal Media Fragments URI (working in Firefox and Chrome)
            $asset_meta['src'] = $asset_meta['low_slide_src'] . "#t=" . $timecode;
        }
    }

    // user is logged and has acces to the selected album
    if ($is_bookmark) {
        $asset_bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
        // sorts the bookmarks following user's prefs
        $order = acl_value_get("bookmarks_order");
        if (isset($order) && $order != '' && $order != $default_bookmarks_order) {
            $asset_bookmarks = array_reverse($asset_bookmarks);
        }
    }

    $toc_bookmarks = toc_asset_bookmark_list_get($album, $asset);
    // sorts the bookmarks following user's prefs
    $order = acl_value_get("toc_order");
    if (isset($order) && $order != '' && $order != $default_toc_order) {
        $toc_bookmarks = array_reverse($toc_bookmarks);
    }

    log_append('view_asset_bookmark: album = ' . $album . ", asset = " . $asset);
    $_SESSION['ezplayer_mode'] = 'view_asset_bookmark'; // used in 'div_left_details.php'
    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['timecode'] = $timecode;
    $_SESSION['loaded_type'] = $input['type'];

    if ($refresh_center) {
        if (acl_display_threads()) {
            if (isset($thread_id)){
                // click from lvl 2 on a discussion
                $thread = thread_details_update(false);
                $_SESSION['thread_display'] = 'details';
            } else {
                // click from lvl 2 on a bookmark
                $threads = threads_select_by_asset($album, $asset);
                $_SESSION['thread_display'] = 'list';
            }
        }
        if ($input['click']){ // refresh the center of the page (local link)
            // lvl, action, album, asset, timecode, targeted type (cam|slide), record type (cam|slide|camslide), permissions (view official | add personal), origin
            trace_append(array('3', 'view_asset_timecode', $album, $asset, $timecode, $_SESSION['loaded_type'], $asset_meta['record_type'], ($is_bookmark) ? 'view_and_add' :  'view_only','from_ezplayer'));
            include_once template_getpath('div_assets_center.php');
        } else {// refresh the whole page (shared link)
            trace_append(array('3', 'view_asset_timecode', $album, $asset, $timecode, $_SESSION['loaded_type'], $asset_meta['record_type'], ($is_bookmark) ? 'view_and_add' :  'view_only','from_external'));
            include_once template_getpath('main.php');
        }
        } else { // refresh the right panel (import / export / edition / deletion / ...)
        include_once template_getpath('div_side_details.php');
    }
}

/**
 * Searches a specific pattern in the bookmarks lists.
 * @global type $input
 * @global type $bookmarks
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $words 
 */
function bookmarks_search() {
    global $input;
    global $bookmarks;
    global $search_result_threads;
    global $bookmarks_toc;
    global $repository_path;
    global $user_files_path;
    global $words; // used to highlight the searched words in 'div_search_result.php'

    $search = $input['search']; // the pattern to be searched
    $target = $input['target']; // where to search (all albums / selected albums / current album)
    $albums = $input['albums']; // the selection of albums
    $fields = $input['fields']; // where to search in the bookmark fields (title / descr. / keywords)
    $fields_thread = $input['fields_thread'];
    $level = $input['level'];
    $tab = $input['tab'];

    if (!isset($level) || is_nan($level) || $level < 0 || $level > 3)
        $level = 0;

    log_append('search_bookmarks : ' . PHP_EOL .
            'search - ' . $search . PHP_EOL .
            'target - ' . $target . PHP_EOL .
            'fields - ' . implode(", ", $fields) . PHP_EOL .
            'tab - ' . implode(", ", $tab));

    // defines target 
    if (!isset($target) || $target == '')
        $target = 'global';

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    if ($target == 'current' // we search in the current album / asset
            && (!isset($album) || $album == ''))
        $target = 'global';

    // transforms the search string in well formed string
    /*   $words = str_getcsv($search, ' ', '"');
      $wordsCSV = '';
      foreach ($words as $index => $word) {
      if (trim($word) == '' || $word == '+') {
      unset($words[$index]);
      } else {
      $wordsCSV = $wordsCSV . "," . $word;
      }
      } */

    // split the string, saves the value to search in a array
    $words = str_getcsv($search, ' ', '"');
    $search = array();
    foreach ($words as $index => $word) {
        if ($word == '' || $word == '+') {
            unset($words[$index]);
        } else {
            $search[] = $word;
        }
    }

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $bookmarks_toc = array();

    switch ($target) {
        case 'current': // searches in current location (either global or album or asset)
            $albums = array($album);
            break;
        case 'album': // searches in albums selection 
            if (!acl_has_album_permissions($album)) {
                $bookmarks_toc = toc_bookmarks_search($search, $fields, $level, array($album), $asset);
            }
            $asset = ""; // asset must be empty for searching in albums selection
            break;
        default : // searches in all albums 
            if (!acl_has_album_permissions($album)) {
                $bookmarks_toc = toc_bookmarks_search($search, $fields, $level, array($album), $asset);
            }
            $asset = ""; // asset must be empty for searching in all albums
            $albums = acl_authorized_albums_list();
            break;
    }

    if (in_array('official', $tab)) { // searches in official bookmarks
        $bookmarks_toc = array_merge($bookmarks_toc, toc_bookmarks_search($search, $fields, $level, $albums, $asset));
    }
    if (in_array('custom', $tab)) { // searches in personal bookmarks
        $bookmarks = user_prefs_bookmarks_search($_SESSION['user_login'], $search, $fields, $level, $albums, $asset);
    }
    if (acl_user_is_logged() && acl_display_threads && in_array('threads', $tab)) { // searches in threads
        $search_result_threads = thread_search($search, $fields_thread, $albums, $asset);
    }

    include_once template_getpath('div_search_result.php');
}

/**
 * Displays the file input form
 * @global type $album
 * @global type $asset
 */
function view_import() {
    global $album;
    global $asset;

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    include_once template_getpath('div_upload_bookmarks.php');
}

/**
 * Uploads a temp file which contains bookmarks to import
 * @global type $imported_bookmarks
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $bookmarks_validation_file
 * @global type $album
 * @global type $asset
 */
function bookmarks_upload() {
    global $imported_bookmarks;
    global $repository_path;
    global $user_files_path;
    global $bookmarks_validation_file;
    global $album;
    global $asset;

    $album = $_POST['album']; // the album user wants to import in
    $asset = $_POST['asset']; // the asset user wants to import in
    $target = $_POST['target']; // personal bookmarks or table of contents

    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['target'] = $target;

    // 1) Sanity checks       
    if ($_FILES['XMLbookmarks']['error'] > 0) {
        error_print_message(template_get_message('upload_error', get_lang()));
        log_append('error', 'upload_bookmarks: an error occurred during file upload (code ' . $_FILES['XMLbookmarks']['error']);
        die;
    }

    if ($_FILES['XMLbookmarks']['type'] != 'text/xml') {
        error_print_message(template_get_message('error_mimetype', get_lang()));
        log_append('warning', 'upload_bookmarks: invalid mimetype for file ' . $_FILES['XMLbookmarks']['tmp_name']);
        die;
    }

    if ($_FILES['XMLbookmarks']['size'] > 2147483) {
        error_print_message(template_get_message('error_size', get_lang()));
        log_append('warning', 'upload_bookmarks: file too big ' . $_FILES['XMLbookmarks']['tmp_name']);
        die;
    }

    // 2) Validates the XML file and converts it in associative array 

    if (file_exists($_FILES['XMLbookmarks']['tmp_name'])) {

        // Validates XML structure
        $xml_dom = new DOMDocument();
        // trim heading and trailing white spaces 
        // because blank lines in top and end of XML file lead to
        // validation error
        file_put_contents($_FILES['XMLbookmarks']['tmp_name'], trim(file_get_contents($_FILES['XMLbookmarks']['tmp_name'])));
        $xml_dom->load($_FILES['XMLbookmarks']['tmp_name']);

        if (!$xml_dom->schemaValidate($bookmarks_validation_file)) {
            include_once template_getpath('div_import_bookmarks.php');
            error_print_message(template_get_message('error_structure', get_lang()));
        }

        // Converts XML file in SimpleXMLElement
        $xml = simplexml_load_file($_FILES['XMLbookmarks']['tmp_name']);
        $imported_bookmarks = xml_file2assoc_array($xml, 'bookmark');
    }

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // Keeps only bookmarks from existing assets
    foreach ($imported_bookmarks as $index => $bookmark) {
        if (!ezmam_asset_exists($bookmark['album'], $bookmark['asset'])) {
            unset($imported_bookmarks[$index]);
        }
    }
    log_append('upload_bookmarks: file imported');
    // lvl, action, album, asset, target (in official|personal bookmarks), number of bookmarks uploaded
    trace_append(array($asset != '' ? '3' : '2', 'bookmarks_upload', $album, $asset != '' ? $asset : '-', $target, count($imported_bookmarks)));
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
    //   echo  "<script language='javascript' type='text/javascript'>window.top.window.document.getElementById('popup_import_bookmarks').innerHTML='$lapin';</script>";
    include_once template_getpath('div_import_bookmarks.php');
}

/**
 * Imports all selected bookmarks to the selected album
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function bookmarks_import() {
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $_SESSION['album'];
    $selection = $input['import_selection'];
    $imported_bookmarks = json_decode($input['imported_bookmarks'], true);
    $target = $input['target'];

    $selected_bookmarks = array();

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // keeps only the selected bookmarks 
    foreach ($selection as $index) {
        array_push($selected_bookmarks, $imported_bookmarks[$index]);
    }

    if ($target == 'official') {
        if (acl_has_album_moderation($album)) { // authorization check
            toc_album_bookmarks_add($selected_bookmarks);
        }
    } else {
        user_prefs_album_bookmarks_add($_SESSION['user_login'], $selected_bookmarks);
    }

    log_append('import_bookmarks: bookmarks added to the album ' . $album);
    // lvl, action, album, asset, target (in official|personal), number of selected bookmarks, number of uploaded bookmarks
    trace_append(array($input['source'] == 'assets' ? '2' : '3', 'bookmarks_import', $album, $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-', $target, count($selection), count($imported_bookmarks)));
    // determines the page to display
    if ($input['source'] == 'assets') {
        // the token is needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
        view_album_assets(false);
    } else {
        view_asset_details(false);
    }
}

/**
 * Exports all selected bookmarks to the user
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function bookmarks_export() {
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];
    $selection = $input['export_selection']; // the selection of bookmarks to export
    $target = $input['target'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // name for the file to be saved
    $filename = (get_lang() == 'fr') ? 'signets' : 'bookmarks';
    if ($target == 'official')
        $filename .= (get_lang() == 'fr') ? '_officiels' : '_official';
    $filename .= '_' . suffix_remove($album);
    if (isset($asset) && $asset != '') {
        $filename .= '_' . $asset;
    }
    $filename .= '.xml';

    // download popup
    if ($target == 'official') { // bookmarks from the table of contents
        $bookmarks = toc_asset_bookmarks_selection_get($album, $asset, $selection);
    } else { // personal bookmarks
        $bookmarks = user_prefs_asset_bookmarks_selection_get($_SESSION['user_login'], $album, $asset, $selection);
    }
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: text/xml");
    header("Content-Transfer-Encoding: binary");

    // XML to save in the file
    $xml_txt = assoc_array2xml_string($bookmarks, "bookmarks", "bookmark");

    // Formating XML for pretty display
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    $dom->loadXML($xml_txt);
    $dom->formatOutput = TRUE;
    ob_clean();
    flush();
    echo $dom->saveXml();

    log_append('export_bookmarks: bookmarks exported from the album ' . $album);
    // lvl, action, album, asset, target (from official|personal), number of exported bookmarks 
    trace_append(array($_SESSION['asset'] == '' ? '2' : '3', 'bookmarks_export', $album, $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-', $target == '' ? 'personal' : $target, count($selection)));
}

/**
 * Exports all bookmarks from the given album / asset
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 * @param type $export_asset false if all album's bookmarks must be exported;
 * true if only specified asset's bookmarks must be exported
 */
function bookmarks_export_all($export_asset = false) {
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $input['album'];
    if ($export_asset)
        $asset = $input['asset'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // name for the file to be saved
    $filename = (get_lang() == 'fr') ? 'signets' : 'bookmarks';
    $filename .= '_' . suffix_remove($album);
    if (isset($asset) && $asset != '') {
        $filename .= '_' . $asset;
    }
    $filename .= '.xml';

    // download popup
    if ($export_asset) {
        $bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
    } else {
        $bookmarks = user_prefs_album_bookmarks_list_get($_SESSION['user_login'], $album);
    }
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: text/xml");
    header("Content-Transfer-Encoding: binary");

    // XML to save in the file
    $xml_txt = assoc_array2xml_string($bookmarks, "bookmarks", "bookmark");
    // Formating XML for pretty display
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    $dom->loadXML($xml_txt);
    $dom->formatOutput = TRUE;
    ob_clean();
    flush();
    echo $dom->saveXml();

    log_append('export_asset_bookmarks: bookmarks exported from the album ' . $album);
}

/**
 * Deletes a selection of bookmarks
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function bookmarks_delete() {
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];
    $selection = $input['delete_selection'];
    $target = $input['target'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // get bookmarks to be deleted
    if ($target == 'official') { // from table of contents
        $bookmarks = toc_asset_bookmarks_selection_get($album, $asset, $selection);
    } else { // from personal bookmarks
        $bookmarks = user_prefs_asset_bookmarks_selection_get($_SESSION['user_login'], $album, $asset, $selection);
    }

    if ($target == 'official') {
        if (acl_has_album_moderation($album)) {
            toc_album_bookmarks_delete($bookmarks);
        }
    } else {
        user_prefs_album_bookmarks_delete($_SESSION['user_login'], $bookmarks);
    }

    log_append('delete_bookmarks: ' . count($selection) . ' bookmarks deleted from the album ' . $album);
    // lvl, action, album, asset, target (from official|personal), number of deleted bookmarks 
    trace_append(array($_SESSION['asset'] == '' ? '2' : '3', 'bookmarks_delete', $album, $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-', $target == '' ? 'custom' : $target, count($selection)));
    if ($input['source'] == 'assets') {
        // album token needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
        view_album_assets(false);
    } else {
        view_asset_details(false);
    }
}

/**
 * Adds or edits a bookmark to the user's bookmarks list
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function bookmark_add() {
    global $input;
    global $repository_path;
    global $user_files_path;


    $bookmark_album = $input['album'];
    $bookmark_asset = $input['asset'];
    $bookmark_timecode = $input['timecode'];
    $bookmark_title = $input['title'];
    $bookmark_description = $input['description'];
    $bookmark_keywords = $input['keywords'];
    $bookmark_level = $input['level'];
    $bookmark_source = $input['source'];
    $bookmark_type = $input['type'];

    if (!acl_user_is_logged())
        return false;

    if (is_nan($bookmark_timecode) || is_nan($bookmark_level)) {
        view_asset_details(false);
    }

    if (!isset($bookmark_type) || ($bookmark_type != 'cam' && $bookmark_type != 'slide'))
        $bookmark_type = '';

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($bookmark_source == 'custom') { // personal bookmarks
        user_prefs_asset_bookmark_add($_SESSION['user_login'], $bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level, $bookmark_type);
    } else { // table of contents
        if (acl_user_is_logged() && acl_has_album_moderation($bookmark_album)) {
            toc_asset_bookmark_add($bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level, $bookmark_type);
        }
    }
    log_append('add_asset_bookmark', 'bookmark added : album -' . $bookmark_album . PHP_EOL .
            'asset - ' . $bookmark_asset . PHP_EOL .
            'timecode - ' . $bookmark_timecode);
    // lvl, action, album, asset, timecode, target (personal|official), type (cam|slide), title, descr, keywords, bookmark_lvl
    trace_append(array('3', $input['edit'] ? 'asset_bookmark_edit' : 'asset_bookmark_add', $bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_source, $bookmark_type, $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));

    view_asset_details(false);
}

/**
 * Used to post a thread
 * @global type $input
 * @return boolean
 */
function thread_add() {
    global $input;

    $thread_album = $input['album'];
    $thread_asset = $input['asset'];
    $thread_asset_title = $input['assetTitle'];
    $thread_timecode = intval($input['timecode']);
    $thread_title = htmlspecialchars($input['title']);
    $thread_description = htmlspecialchars($input['description']);
    $thread_visibility = ($input['visibility'] == "on") ? '1' : '0';

    if (!acl_user_is_logged())
        return false;
    if (is_nan($thread_timecode)) {
        $thread_timecode = 0;
    }
    $values = array(
        "title" => $thread_title,
        "message" => $thread_description,
        "timecode" => $thread_timecode,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s'),
        "studentOnly" => $thread_visibility,
        "albumName" => $thread_album,
        "assetName" => $thread_asset,
        "assetTitle" => $thread_asset_title
    );

    thread_insert($values);

    cache_asset_threads_unset($thread_album, $thread_asset);
    cache_album_threads_unset($thread_album);

    return threads_list_update();
}

/**
 * Used to post a new comment
 * @global type $input
 * @return boolean
 */
function comment_add() {
    global $input;
    $album = $input['album'];
    $asset = $input['asset'];
    $comment_message = htmlspecialchars($input['message']);
    $comment_thread = $input['thread_id'];

    if (!acl_user_is_logged())
        return false;

    $values = array(
        "message" => $comment_message,
        "thread" => $comment_thread,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s')
    );
    comment_insert($values);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    $_SESSION['current_thread'] = $comment_thread;

    return thread_details_update();
}

/**
 * Used to reply to a comment
 * @global type $input
 * @return boolean
 */
function comment_add_reply() {
    global $input;

    $album = $input['album'];
    $asset = $input['asset'];
    $comment_message = htmlspecialchars($input['answer_message']);
    $comment_thread = $input['thread_id'];
    $comment_parent = intval($input['answer_parent']);

    if (!acl_user_is_logged())
        return false;

    $values = array(
        "message" => $comment_message,
        "thread" => $comment_thread,
        "parent" => $comment_parent,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s')
    );
    comment_insert($values);

    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    $_SESSION['current_thread'] = $comment_thread;

    return thread_details_update();
}

/**
 * Used to edit a comment
 * @global type $input
 * @return boolean
 */
function comment_edit() {
    global $input;
    $comment_id = $input['comment_id'];
    $comment_message = htmlspecialchars($input['comment_message'] . edited_on());
    $album = $input['album'];
    $asset = $input['asset'];
    $thread = $input['thread_id'];

    comment_update($comment_id, $comment_message, $album, $asset, $thread, $_SESSION['user_full_name']);

    $_SESSION['current_thread'] = $thread;

    return thread_details_update();
}

/**
 * Used to update a thread informations
 * @global type $input
 * @return boolean
 */
function thread_edit() {
    global $input;
    $thread_id = $input['thread_id'];
    $thread_message = htmlspecialchars($input['thread_message'] . edited_on());
    $thread_timecode = intval($input['thread_timecode']);
    $thread_title = htmlspecialchars($input['thread_title']);
    $album = $input['thread_album'];
    $asset = $input['thread_asset'];

    $_SESSION['current_thread '] = $thread_id;

    thread_update($thread_id, $thread_title, $thread_message, $thread_timecode, $album, $_SESSION['user_full_name']);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    return thread_details_update();
}

function edited_on(){
    $date = date('Y-m-d H:i:s');
    $msg = PHP_EOL . PHP_EOL . '<i style="font-size: 10px;">' . template_get_message('Last_edit', get_lang()) . ' ' . $date . '</i>';
    return $msg;
}
/**
 * Reloads a thread details element 
 * @global array $input
 * @return boolean
 */
function thread_details_update($display = true) {
    global $input;

    $id = $input['thread_id'];

    $thread = thread_select_by_id($id);
    $thread['best_comment'] = comment_select_best($id);
    $thread['comments'] = comment_select_by_thread($id);

    if ($display){
        include template_getpath('div_thread_details.php');
        return true;
    } else {
        return $thread;
    }
}

/**
 * Reloads the threads list
 * @global type $input
 */
function threads_list_update($display = true) {
    global $input;

    $album = $input['album'];
    $asset = $input['asset'];

    if (!isset($album) || $album == '' || $album == 'undefined') {
        $album = $_SESSION['album'];
    }

    if (!isset($asset) || $asset == '' || $asset == 'undefined') {
        $asset = $_SESSION['asset'];
    }

    if (acl_display_threads()) {
        $threads = threads_select_by_asset($album, $asset);
    }

    if ($display){
        include_once template_getpath('div_threads_list.php');
        return true;
    } else {
        return $threads;
    }
}

/**
 * Used to remove a thread
 * @global array $input
 * @return boolean
 */
function thread_delete() {
    global $input;

    $id = $input['thread_id'];
    $album = $input['thread_album'];
    $asset = $input['thread_asset'];

    if (!acl_is_admin())
        return false;
    if (!isset($album) || $album == '')
        $album = $_SESSION['album'];
    if (!isset($asset) || $asset == '')
        $asset = $_SESSION['asset'];

    thread_delete_by_id($id, $album, $asset);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    return threads_list_update();
}

/**
 * Used to remove a comment
 * @global type $input
 */
function comment_delete() {
    global $input;
    $id = $input['comment_id'];

    comment_delete_by_id($id);
    cache_asset_threads_unset($_SESSION['album'], $_SESSION['asset']);

    $_SESSION['current_thread'] = $input['thread_id'];

    return thread_details_update();
}

/**
 * Copies a bookmark from the personal bookmarks to the table of contents and reverse
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $tab
 */
function bookmark_copy() {
    global $input;
    global $repository_path;
    global $user_files_path;
    global $tab;

    $bookmark_album = $input['album'];
    $bookmark_asset = $input['asset'];
    $bookmark_timecode = $input['timecode'];
    $bookmark_title = $input['title'];
    $bookmark_description = html_entity_decode($input['description']);
    $bookmark_keywords = $input['keywords'];
    $bookmark_level = $input['level'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($input['tab'] == 'official') { // copies from table of contents to personal bookmarks
        user_prefs_asset_bookmark_add($_SESSION['user_login'], $bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level);

        // lvl, action, album, asset, timecode, target (to official|personal), title, description keywords, bookmark_lvl
        trace_append(array('3', 'asset_bookmark_copy', $bookmark_album, $bookmark_asset, $bookmark_timecode, 'custom', $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));
        log_append('copy_bookmark', 'bookmark copied from official to personal : album -' . $bookmark_album .
                ' asset - ' . $bookmark_asset .
                ' timecode - ' . $bookmark_timecode);
    } else { // copies from personal bookmarks to table of contents 
        if (acl_user_is_logged() && acl_has_album_moderation($bookmark_album)) {
            toc_asset_bookmark_add($bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level);

            trace_append(array('3', 'asset_bookmark_copy', $bookmark_album, $bookmark_asset, $bookmark_timecode, 'official', $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));
            log_append('copy_bookmark', 'bookmark copied from personal to official : album -' . $bookmark_album .
                    ' asset - ' . $bookmark_asset .
                    ' timecode - ' . $bookmark_timecode);
        }
    }

    if ($input['source'] == 'assets') {
        $input['token'] = ezmam_album_token_get($bookmark_album);
        view_album_assets(false);
    } else {
        view_asset_details(false);
    }
}

/**
 * Removes all bookmarks of the given asset
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function bookmarks_delete_all() {
    global $input;
    global $user_files_path;
    global $repository_path;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $album = $input['album'];
    $asset = $input['asset'];

    $bookmarks = user_prefs_asset_bookmarks_delete($_SESSION['user_login'], $album, $asset);

    // lvl, action, album, asset
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'asset_bookmarks_delete', $album, $asset));
    log_append('remove_asset_bookmarks: all bookmarks deleted from the asset ' . $asset . ' in the album ' . $album);

    // album token needed to display the album assets
    $input['token'] = ezmam_album_token_get($album);
    $input['click'] = true;
    view_album_assets(true);
}

/**
 * Removes an asset bookmark from the user's bookmarks list
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function bookmark_delete() {
    global $input;
    global $repository_path;
    global $user_files_path;

    $bookmark_album = $input['album'];
    $bookmark_asset = $input['asset'];
    $bookmark_timecode = $input['timecode'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($input['tab'] == 'custom') { // remove from personal bookmarks
        user_prefs_asset_bookmark_delete($_SESSION['user_login'], $bookmark_album, $bookmark_asset, $bookmark_timecode);
    } else { // removes from table of contents
        if (acl_user_is_logged() && acl_has_album_moderation($bookmark_album)) {
            toc_asset_bookmark_delete($bookmark_album, $bookmark_asset, $bookmark_timecode);
        }
    }
    // lvl, action, album, asset, timecode
    trace_append(array($_SESSION['asset'] == '' ? '2' : '3', 'bookmark_delete', $bookmark_album, $bookmark_asset, $bookmark_timecode));
    log_append('remove_asset_bookmark', 'bookmark removed : album -' . $bookmark_album .
            ' asset - ' . $bookmark_asset .
            ' timecode - ' . $bookmark_timecode);

    if ($input['source'] == 'assets') {
        $input['token'] = ezmam_album_token_get($bookmark_album);
        view_album_assets(false);
    } else {
        view_asset_details(false);
    }
}

/**
 * Defines user's preferences on how bookmarks should be ordered in the web interface
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function bookmarks_sort() {
    global $input;
    global $repository_path;
    global $user_files_path;

    $album = $_SESSION["album"];
    $panel = $input['panel'];
    $new_order = $input["order"];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if (acl_value_get("${panel}_order") != $new_order) {
        if (acl_user_is_logged()) {
            user_prefs_settings_edit($_SESSION['user_login'], "${panel}_order", $new_order);
            acl_update_settings();
        } else {
            $_SESSION["acl_user_settings"]["${panel}_order"] = $new_order;
        }
    }
    // lvl, action, album, panel (official|personal), new_order (chron|reverse_chron)
    trace_append(array($input['source'] == 'assets' ? '2': '3', 'bookmarks_sort', $album, $panel, $new_order));
    // determines the page to display
    if ($input['source'] == 'assets') {
        // the token is needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
        view_album_assets(false);
    } else {
        view_asset_details(false);
    }
}

/**
 * Deletes a token from 'div_main_center.php'
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function album_token_delete() {
    global $input;
    global $repository_path;
    global $user_files_path;


    $album = $input['album'];

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    user_prefs_token_remove($_SESSION['user_login'], $album);
    user_prefs_album_bookmarks_delete_all($_SESSION['user_login'], $album);
    acl_update_permissions_list();
    log_append('delete_album_token', 'album token removed : album -' . $album);
    // lvl, action, album
    trace_append(array('1', 'album_token_delete', $album));

    view_main(false);
}

/**
 * Move an album token up and down
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function album_token_move() {
    global $input;
    global $repository_path;
    global $user_files_path;


    $album = $input['album'];
    $index = (int) $input['index'];
    $upDown = $input['up_down'];

    $new_index = ($upDown == 'up') ? $index - 1 : $index + 1;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    user_prefs_token_swap($_SESSION['user_login'], $index, $new_index);
    acl_update_permissions_list();
    log_append('moved_album_token', 'album token moved from ' . $index . ' to ' . $new_index);
    // lvl, action, album, index_src, index_dest
    trace_append(array('1', 'album_token_move', $album, $index, $new_index));

    view_main(false);
}

/**
 * Displays the help page
 */
function view_help() {
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'view_help'));
    require_once template_getpath('help.php');
    //include_once "tmpl/fr/help.php";
}

//
// "Business logic" functions
//

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_anonymous_session() {
    global $input;
    global $template_folder;
    global $error;


    // 1) Initializing session vars
    //  $_SESSION['ezplayer_anonymous'] = "anonymous_session"; // "boolean" stating that we're logged
    $_SESSION['ezplayer_anonymous'] = "user_logged_anonymous"; // "boolean" stating that we're logged
    //check flash plugin or GET parameter no_flash
    if (!isset($_SESSION['has_flash'])) {//no noflash param when login
        //check flash plugin
        if ($input['has_flash'] == 'N')
            $_SESSION['has_flash'] = false;
        else
            $_SESSION['has_flash'] = true;
    }

    // 2) Setting correct language
    $lang = isset($input['lang']) ? $input['lang'] : 'fr';
    set_lang($lang);


    // 3) Resetting the template path to the one of the language chosen
    template_repository_path($template_folder . get_lang());

    // 4) Logging the entering operation
    log_append("Anonymous_session");
    log_append("user's browser : " . $_SESSION['browser_full']);
    // lvl, action, browser_name, browser_version, user_os, browser_full_info
    trace_append(array("1", "login_as_anonymous", $_SESSION['browser_name'], $_SESSION['browser_version'], $_SESSION['user_os'], $_SESSION['browser_full'], session_id()));

    // 5) Displaying the page
//    view_main();
    $input = $_SESSION['first_input'];
    load_page();
}

/**
 * Used to sign in without a login
 * @global array $input
 * @global type $template_folder
 * @global type $login_error
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $ezplayer_url
 */
function anonymous_login() {
    global $input;
    global $template_folder;
    global $login_error;
    global $repository_path;
    global $user_files_path;
    global $ezplayer_url;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $login_error = '';
    $login = $input['login'];
    $passwd = $input['passwd'];
    unset($input['login']);
    unset($input['passwd']);
    $input['action'] = $_SESSION['ezplayer_mode'];
    $album_tokens = $_SESSION['acl_album_tokens'];
    unset($input['click']);

    // 0) Sanity checks
    if (!isset($login) || !isset($passwd) || empty($login) || empty($passwd)) {
        $login_error = template_get_message('empty_username_password', get_lang());
        load_page();
        die;
    }

    $res = checkauth($login, $passwd);
    if (!$res) {
        $login_error = checkauth_last_error();
        load_page();
        die;
    }
    // 1) Initializing session vars
    $_SESSION['ezplayer_logged'] = "user_logged"; // "boolean" stating that we're logged
    unset($_SESSION['ezplayer_anonymous']); // "boolean" stating that we're logged
    $_SESSION['user_login'] = $res['login'];
    $_SESSION['user_real_login'] = $res['real_login'];
    $_SESSION['user_full_name'] = $res['full_name'];
    $_SESSION['user_email'] = $res['email'];


    if (isset($album_tokens)) {
        user_prefs_tokens_add($_SESSION['user_login'], $album_tokens);
    }

    // 2) Initializing the ACLs
    acl_init($login);
    // 3) Logging the login operation
    log_append("anonymous user logged in");
    // lvl, action, browser_name, browser_version, user_os, browser_full_info
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, "login_from_anonymous", $_SESSION['browser_name'], $_SESSION['browser_version'], $_SESSION['user_os'], $_SESSION['browser_full'], session_id()));

    if (count($input) > 0)
        $ezplayer_url .= '/index.php?';
    foreach ($input as $key => $value) {
        $ezplayer_url .= "$key=$value&";
    }
    // 4) Displaying the previous page
    header("Location: " . $ezplayer_url);
    load_page();
}

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_login($login, $passwd) {
    global $input;
    global $template_folder;
    global $error;
    global $ezplayer_url;

    // 0) Sanity checks
    if (empty($login) || empty($passwd)) {
        $error = template_get_message('empty_username_password', get_lang());
        view_login_form();
        die;
    }

    $login_parts = explode("/", $login);

    // checks if runas 
    if (count($login_parts) == 2) {
        if (!file_exists('admin.inc')) {
            $error = "Not admin. runas login failed";
            view_login_form();
            die;
        }
        include 'admin.inc'; //file containing an assoc array of admin users
        if (!isset($admin[$login_parts[0]])) {
            $error = "Not admin. runas login failed";
            view_login_form();
            die;
        }
        $_SESSION['user_is_admin'] = true;
    } else {
        if (file_exists('admin.inc')) {
            include 'admin.inc'; //file containing an assoc array of admin users
            if (isset($admin[$login])) {
                $_SESSION['user_is_admin'] = true;
            }
        }
    }

    $res = checkauth(strtolower($login), $passwd);
    if (!$res) {
        $error = checkauth_last_error();
        view_login_form();
        die;
    }


    // 1) Initializing session vars
    $_SESSION['ezplayer_logged'] = "user_logged"; // "boolean" stating that we're logged
    $_SESSION['user_login'] = $res['login'];
    $_SESSION['user_real_login'] = $res['real_login'];
    $_SESSION['user_full_name'] = $res['full_name'];
    $_SESSION['user_email'] = $res['email'];
    //check flash plugin or GET parameter no_flash
    if (!isset($_SESSION['has_flash'])) {//no noflash param when login
        //check flash plugin
        if ($input['has_flash'] == 'N')
            $_SESSION['has_flash'] = false;
        else
            $_SESSION['has_flash'] = true;
    }
    // 2) Initializing the ACLs
    acl_init($login);

    // 3) Setting correct language
    set_lang($input['lang']);


    // 4) Resetting the template path to the one of the language chosen
    template_repository_path($template_folder . get_lang());

    // 5) Logging the login operation
    log_append("login");
    log_append("user's browser : " . $_SESSION['browser_full']);
    // lvl, action, browser_name, browser_version, user_os, browser_full_info
    trace_append(array("1", "login", $_SESSION['browser_name'], $_SESSION['browser_version'], $_SESSION['user_os'], $_SESSION['browser_full'], session_id()));

    // 6) Displaying the page
//    view_main();
    if (count($_SESSION['first_input']) > 0)
        $ezplayer_url .= '/index.php?';
    foreach ($_SESSION['first_input'] as $key => $value) {
        $ezplayer_url .= "$key=$value&";
    }
    header("Location: " . $ezplayer_url);
    load_page();
}

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function user_logout() {
    global $ezplayer_url;
    // 1) Deleting the ACLs from the session var
    log_append("logout");
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'logout'));
    acl_exit();

    // 2) Unsetting session vars
    unset($_SESSION['ezplayer_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['ezplayer_logged']); // "boolean" stating that we're logged
    unset($_SESSION['ezplayer_anonymous']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezplayer_url;

    unset($_SESSION['lang']);
}

/**
 * Updates user preferences
 * @global type $input
 * @return boolean
 */
function preferences_update() {
    global $input;
    $display_new_video_notification = $input['display_new_video_notification'];
    $display_threads = $input['display_threads'];
    $display_thread_notification = $input['display_thread_notification'];

    user_prefs_settings_update($_SESSION['user_login'], "display_new_video_notification", $display_new_video_notification);
    user_prefs_settings_update($_SESSION['user_login'], "display_threads", $display_threads);
    user_prefs_settings_update($_SESSION['user_login'], "display_thread_notification", $display_thread_notification);

    acl_update_settings();
    include_once template_getpath('div_main_center.php');
    return true;
}


/**
 * Upvote or downvote a comment
 * @global type $input
 */
function vote_add() {
    global $input;
    $login = $input['login'];
    $comment = intval($input['comment']);
    $vote_type = $input['vote_type'];

    $values = array(
        "login" => $login,
        "comment" => $comment,
        "voteType" => $vote_type
    );

    vote_insert($values);
    return thread_details_update();
}

/**
 * Updates a comments's aproval
 * @global type $input
 */
function comment_edit_aproval() {
    global $input;
    $commentId = intval($input['approved_comment']);

    comment_update_approval($commentId);
    return thread_details_update();
}


/**
 * Called by client to save a use trace
 */
function client_trace(){
    global $input;
    
    trace_append($input['info']);    
}

function trace_append($array){
    global $ezplayer_trace;
    global $trace_on;

    if (!$trace_on) return false;

    // 1) Date/time at which the event occurred
    $data = date('Y-m-d-H:i:s');
    $data .= ' | ';
    
    $data .= session_id();
    $data .= ' | ';
    
    // 2) IP address of the user that provoked the event
    $data .= (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'noip';
    $data .= ' | ';
    
    // 3) Username and realname of the user that provoked the event
    // There can be no login if the operation was performed by a CLI tool for instance.
    // In that case, we display "nologin" instead.
    if(!isset($_SESSION['user_login']) || empty($_SESSION['user_login'])) {
        $data .= 'nologin';
    }
    // General case, where there is a login and (possibly) a real login
    else if(isset($_SESSION['real_login'])) {
        $data .= $_SESSION['real_login'].'/'.$_SESSION['user_login'];
    }
    else {
        $data .= $_SESSION['user_login'];
    }
    $data .= ' | ';
    
    $idx = 0;
    $max_idx = count($array);
    foreach ($array as $value){
        $idx++;
        $data .= $value;
        if ($idx != $max_idx) $data .= ' | ';
    }
    // 6) And we add a carriage return for readability
    $data .= PHP_EOL;
    
    // Then we save the new entry
    if (file_exists($ezplayer_trace) && filesize($ezplayer_trace) >= 2000000000){
        rename($ezplayer_trace, $ezplayer_trace . date("Ymd_His"));
    }
    file_put_contents($ezplayer_trace, $data, FILE_APPEND | LOCK_EX);
}
?>
