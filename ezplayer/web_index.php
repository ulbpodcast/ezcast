<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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
require_once __DIR__.'/../commons/lib_error.php';
require_once 'lib_ezmam.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_template.php';
require_once '../commons/lib_various.php';
require_once '../commons/common.inc';
require_once 'lib_various.php';
require_once 'lib_user_prefs.php';
include_once 'lib_toc.php';
require_once './Browser/Autoloader.php';
require_once 'lib_threads_pdo.php';
require_once 'lib_chat_pdo.php';
require_once 'lib_cache.php';
require_once 'lib_acl.php';
require_once '../commons/lib_mobile_detect.php';

$detect = new Mobile_Detect();
$_SESSION['isPhone'] = $detect->isMobile();

$input = array_merge($_GET, $_POST);

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

global $repository_path;
ezmam_repository_path($repository_path);

//
// Login/logout
//
// Saves the URL used to access the website
if (!isset($_SESSION['first_input']) && isset($input['action']) && $input['action'] != 'logout' &&
        $input['action'] != 'login' && $input['action'] != 'client_trace') {
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
    $_SESSION['user_os_version'] = $os->getVersion();
}
//print_r($input);
//print_r($_SESSION);
////die();
//if((!isset($_SESSION['termsOfUses']) || $_SESSION['termsOfUses']!=1) && $input['action']!='acceptTermsOfUses' && $enable_termsOfUse){
//    $_SESSION['redirect']=json_encode($input);
//    view_termsOfUses_form();
//    die();
//}
//if( isset($input['action']) && $input['action']=='acceptTermsOfUses'){
//    requireController('acceptTermsOfUses.php');
////    die();
//}


$logged_in = user_logged_in();
$album_allow_anonymous = isset($input['album']) && ezmam_album_allow_anonymous($input['album']);
//logout anon user if he tries to access an album which was not set as anonym allowed (except if we still
// accept the anon=true option in the url, should be removed in the future)
if ($logged_in && user_anonymous() && !$allow_url_anon
   && isset($input['album']) && !$album_allow_anonymous) {
    logout();
    $logged_in = false;
}

if(isset($input['loging'])){
    requireController('user_logout.php');
    logout();
    $logged_in = false;
    view_login_form();   
}


// If we're not logged in, we try to log in or display the login form
if (!$logged_in) {
    // global $repository_path;
    
    // if the url contains the parameter 'anon' the session is assumsed as anonymous
    if (($allow_url_anon && isset($input['anon']) && $input['anon'] == true) ||
            // Log as anonymous if user tries to access an album and it has been set as accessible as anonymous
            (isset($input['album']) && $album_allow_anonymous)) {
        user_anonymous_session();
        $logged_in = true;
    }
    
    if (isset($input['action'])) {
        switch ($input['action']) {

            
            // Handle login form
            case 'login':
                if (!isset($input['login']) || !isset($input['passwd'])) {
                    error_print_message(template_get_message('empty_username_password', get_lang()));
                    die;
                }
                user_login(trim($input['login']), trim($input['passwd']));
                exit(0);  //page will be loaded in user_login
                break;
            case 'client_trace':
                requireController('client_trace.php');
                index();
                exit(0);
                break;
            default:
                // This is a tricky case:
                // If we do not have a session, but we have an action, that means we lost the
                // session somehow and are trying to load part of a page through AJAX call.
                // We do not want the login page to be displayed randomly inside a div,
                // so we refresh the whole page to get a full-page login form.
                //
                // $input['click'] indicates that the action comes from a link in the application
                if (isset($input['click']) && $input['click']) {
                    refresh_page();
                    exit(0);
                }
                break;
        }
    }
    
    
    if (!$logged_in) {
        //Just display the login form
        if (isset($_GET["sso"])) {
            if (in_array("sso", $auth_methods)) {
                user_login(trim('login'), trim('passwd'));
            }
            /*if (in_array("sso",$auth_methods)){
                user_login(trim('login'), trim('passwd'));*/
        } else {
            view_login_form();
            exit(0);
        }
    }
}
//print_r($_SESSION); die();
else if(isset($_SESSION['termsOfUses']) && $_SESSION['termsOfUses']!=1 && ($input['action']!='acceptTermsOfUses') && $enable_termsOfUse){
    view_termsOfUses_form();
    $_SESSION['redirect']=json_encode($input);
    die();
}
// From this point, user is logged in.

// We check whether they specified an action to perform. If not, it means they landed
// here through a page reload, so we check the session variables to restore the page as it was.
 if (isset($_SESSION['ezplayer_logged']) && (!isset($input['action']) || empty($input['action']))) {
     // Check if player first connexion
     global $first_connexion;
     $first_connexion = !isset($_COOKIE['has_connected_once']);
     // Cookie life: one year
     setcookie('has_connected_once', true, time() + (365 * 24 * 60 * 60));
    
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
function load_page()
{
    global $input;
    $action = $input['action'];
    $redraw = false;
    
    /**
     * Until pages and services are divided, mark some action as services
     * A service = action not returning a page.
     * A lot of these services actually return presentation too (in the form on popups), presentation should be moved to calling page
     */
    global $service; //true if we're currently running a service.
    $service = false;
    
    //
    // Actions
    //
    // Controller goes here
    
    $paramController = array();
    switch ($action) {
        
                    
        case 'acceptTermsOfUses':
            requireController('acceptTermsOfUses.php');
        break;

        
        // ============== L O G I N  /  L O G O U T =============== //
        // The only case when we could possibly arrive here with a session created
        // and a "login" action is when the user refreshed the page. In that case,
        // we redraw the page with the last information saved in the session variables.
        case 'login':
            redraw_page();
            break;

        // User logs in as anonymous
        case 'anonymous_login':
            requireController('anonymous_login.php');
            break;

        // In case we want to log out
        case 'logout':
            requireController('user_logout.php');
            break;

        // ============== N A V I G A T I O N ============= //
        // displays the list of assets for a given album
        case 'view_album_assets':
            requireController('album_assets_view.php');
            break;

        // displays a specific asset
        case 'view_asset_details':
            requireController('asset_view.php');
            break;

        // displays a specific timecode of a given asset
        case 'view_asset_bookmark':
            $paramController[] = true;
            requireController('asset_view.php');
            break;

        // displays a specific timecode of a given asset
        case 'view_asset_streaming':
            requireController('asset_streaming_view.php');
            break;

        // displays a list of threads for a given asset
        case 'view_threads_list':
            requireController('thread_operation.php');
            break;

        // displays the details of a given thread (best comment / comments / ...)
        case 'view_thread_details':
            thread_details_update();
            return;

        // displays the help page
        case 'view_help':
            requireController('view_help.php');
            break;

        // ============== S T R E A M I N G =============== //
        case 'streaming_config_update':
            requireController('asset_streaming_player_update.php');
            break;
        
        case 'streaming_chat_update':
            requireController('asset_streaming_chat_update.php');
            break;
        
        case 'streaming_chat_get_last':
            requireController('asset_streaming_chat_get_last.php');
            break;
        
        case 'chat_message_add':
            requireController('chat_message_add.php');
            break;

        // ============== S E A R C H =============== //
        // search the given word(s) in threads and/or bookmarks
        case 'threads_bookmarks_search':
            requireController('threads_bookmarks_search.php');
            break;

        // ============== U S E R ' S   P R E F S / A D M I N   M O D E =============== //
        // Admin user enables/disables the admin mode
        case 'admin_mode_update':
            requireController('admin_mode_update.php');
            break;

        // updates the user's settings
        case 'settings_update':
            requireController('settings_update.php');
            break;

        case 'contact_send':
            requireController('contact_send.php');
            break;

        // ============== A L B U M S =============== //
        // moves an album on the home page
        case 'album_token_move':
            requireController('album_token_move.php');
            break;

        // deletes an album from the home page
        case 'album_token_delete':
            requireController('album_token_delete.php');
            break;

        // ============== A S S E T S =============== //
        // increments the view count for a specific range of a video
        case 'asset_range_count_update':
            requireController('asset_range_count_update.php');
            break;

        // ============== B O O K M A R K S =============== //
        // creates a new bookmark
        case 'bookmark_add':
            requireController('bookmark_add.php');
            break;

        // copies a personal bookmark to the official bookmarks
        case 'bookmark_copy':
            requireController('bookmark_copy.php');
            break;

        // deletes the given bookmark
        case 'bookmark_delete':
            requireController('bookmark_delete.php');
            break;

        // deletes a selection of bookmarks
        case 'bookmarks_delete':
            requireController('bookmarks_delete.php');
            break;

        // deletes all bookmarks of the given asset
        case 'bookmarks_delete_all':
            requireController('bookmarks_delete_all.php');
            break;

        // exports a selection of bookmarks
        case 'bookmarks_export':
            $service = true;
            requireController('bookmarks_export.php');
            break;

        // exports all bookmarks of the given album
        case 'bookmarks_album_export':
            $service = true;
            requireController('bookmarks_export_all.php');
            break;

        // exports all bookmarks of the given asset
        case 'bookmarks_asset_export':
            $service = true;
            $paramController[] = true;
            requireController('bookmarks_export_all.php');
            break;

        // prepares the upload form for imported bookmarks
        case 'bookmarks_upload_prepare':
            requireController('bookmarks_upload_prepare.php');
            break;

        // uploads the xml file containing the bookmarks
        case 'bookmarks_upload':
            requireController('bookmarks_upload.php');
            break;

        // imports a selection of bookmarks
        case 'bookmarks_import':
            requireController('bookmarks_import.php');
            break;

        // orders the bookmarks (chron | reverse chron)
        case 'bookmarks_sort':
            requireController('bookmarks_sort.php');
            break;

        // ============== T H R E A D S ================= //
        // creates a new threas
        case 'thread_add':
            $paramController[] = 'add';
            requireController('thread_operation.php');
            break;

        // edits the given thread
        case 'thread_edit':
            requireController('thread_edit.php');
            break;

        // deletes a given thread
        case 'thread_delete':
            $paramController[] = 'delete';
            requireController('thread_operation.php');
            break;

        // adds a new comment to a given thread
        case 'thread_comment_add':
            requireController('thread_comment_add.php');
            break;

        // edits the given thread comment
        case 'thread_comment_edit':
            requireController('thread_comment_edit.php');
            break;

        // deletes the given thread comment
        case 'thread_comment_delete':
            requireController('thread_comment_delete.php');
            break;

        // adds a vote on the given comment
        case 'thread_comment_vote':
            requireController('comment_vote_add.php');
            break;

        // adds/removes approval on the given comment
        case 'thread_comment_approve':
            requireController('comment_approval_edit.php');
            break;

        // adds a new reply to a given comment
        case 'comment_reply_add':
            requireController('comment_reply_add.php');
            break;

        // ============== P O P - U P ================= //
        // renders a modal window related to the album
        case 'album_popup':
            requireController('album_popup.php');
            break;

        // renders a modal window related to the asset
        case 'asset_popup':
            requireController('asset_popup.php');
            break;

        // renders a modal window related to a specific bookmark
        case 'bookmark_popup':
            requireController('bookmark_popup.php');
            break;

        // renders a modal window related to a list of bookmarks
        case 'bookmarks_popup':
            requireController('bookmarks_popup.php');
            break;

        // renders a modal window for the thread visibility choice
        case 'thread_visibility':
            requireController('thread_visibility.php');
            break;

        // renders a modal window related to a thread
        case 'thread_popup':
            requireController('thread_popup.php');
            break;

        // renders a modal window related to a thread comment
        case 'thread_comment_popup':
            requireController('thread_comment_popup.php');
            break;

        // renders a modal window related to live stream
        case 'live_stream_popup':
            requireController('live_stream_popup.php');
            break;

        // ============= V A R I O U S ================ //
        // saves the action in a trace file
        case 'client_trace':
            requireController('client_trace.php');
            break;

        // No action selected: we choose to display the homepage again
        default:
            albums_view();
            return;
    }
    
    
    // Call the function to view the page
    index($paramController);
    
    db_close();
}

// =================== L O G I N  /  L O G O U T ===================== //
/**
 * logs the user in as anonymous
 * @param string $login
 * @param string $passwd
 */
function user_anonymous_session()
{
    global $input;
    global $template_folder;
    global $error;


    // 1) Initializing session vars
    $_SESSION['ezplayer_anonymous'] = "user_logged_anonymous"; // "boolean" stating that we're logged
    $_SESSION['user_login'] = "anon";
    $_SESSION['user_full_name'] = "Anonyme";
    //check flash plugin or GET parameter no_flash
    if (!isset($_SESSION['has_flash'])) {//no noflash param when login
        //check flash plugin
        if (isset($input['has_flash']) && $input['has_flash'] == 'N') {
            $_SESSION['has_flash'] = false;
        } else {
            $_SESSION['has_flash'] = true;
        }
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
    trace_append(array("1", "login_as_anonymous", $_SESSION['browser_name'], $_SESSION['browser_version'],
        $_SESSION['user_os'], $_SESSION['browser_full']));

    // 5) Displaying the page
    //    view_main();
    $input = $_SESSION['first_input'];
    load_page();
}

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_login($login, $passwd)
{
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
        $_SESSION['user_runas'] = true;
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
//    print_r($res);die();


    // 1) Initializing session vars
    $_SESSION['ezplayer_logged'] = "user_logged"; // "boolean" stating that we're logged
    $_SESSION['user_login'] = $res['login'];
    $_SESSION['user_real_login'] = $res['real_login'];
    $_SESSION['user_full_name'] = $res['full_name'];
    $_SESSION['user_email'] = $res['email'];
    $_SESSION['termsOfUses'] = $res['termsOfUses'];

    $_SESSION['admin_enabled'] = false;
    //check flash plugin or GET parameter no_flash
    if (!isset($_SESSION['has_flash'])) {//no noflash param when login
        //check flash plugin
        if (isset($input['has_flash']) && $input['has_flash'] == 'N') {
            $_SESSION['has_flash'] = false;
        } else {
            $_SESSION['has_flash'] = true;
        }
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
    trace_append(array("1", "login", $_SESSION['browser_name'], $_SESSION['browser_version'], $_SESSION['user_os'],
        $_SESSION['browser_full']));

    // 6) Displaying the page
    //    view_main();
    if (count($_SESSION['first_input']) > 0) {
        $ezplayer_url .= '/index.php?';
    }
    foreach ($_SESSION['first_input'] as $key => $value) {
        $ezplayer_url .= "$key=$value&";
    }
    header("Location: " . $ezplayer_url);
    load_page();
}



// =================== N A V I G A T I O N ======================== //

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in()
{
    return (isset($_SESSION['ezplayer_logged']) || isset($_SESSION['ezplayer_anonymous']));
}

function user_anonymous()
{
    return (isset($_SESSION['ezplayer_anonymous']));
}
function view_termsOfUses_form(){
    include_once template_getpath('div_termsOfUses.php');

}
/**
 * Displays the login form
 */
function view_login_form()
{
    global $ezplayer_url;
    global $error, $input;
    global $template_folder;
    global $auth_methods;
    global $sso_only;

    //check if we receive a no_flash parameter (to disable flash progressbar on upload)
    if (isset($input['no_flash'])) {
        $_SESSION['has_flash'] = false;
    }
    $url = $ezplayer_url;
    
    $lang = isset($input['lang']) ? $input['lang'] : 'fr';
    set_lang($lang);
    
    template_repository_path($template_folder . get_lang());
    // template include goes here
    /* require_once template_getpath('login.php');*/
    $sso_enabled = in_array("sso", $auth_methods);
    $file_enabled = ((in_array("file", $auth_methods) && !$sso_only)  || ($sso_only && isset($_GET["local"])) || (!$sso_enabled && in_array("file", $auth_methods)) );
    include_once template_getpath('login.php');
}

/**
 * $refresh_page is used to determine if we need to refresh the whole page
 * or just a part of the page
 * Displays the home page
 */
function albums_view($refresh_page = true)
{
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
              
            $album_title = ezmam_album_metadata_get($album . '-pub');
            if (isset($album_title['course_code_public'])) {
                $moderated_tokens[$index]['course_code_public'] = $album_title['course_code_public'];
            }
        }
        // add the list of moderated public albums
        user_prefs_tokens_add($_SESSION['user_login'], $moderated_tokens);
        ezplayer_acl_update_permissions_list();
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
function redraw_page()
{
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
    albums_view();
}

/**
 * Reloads the whole page
 */
function refresh_page()
{
    global $ezplayer_url;
    // session var to determine the whole page has to be reloaded
    $_SESSION['reloaded'] = true;
    // reload the page
    echo '<script>window.location.reload();</script>';
    trace_append(array('0', 'refresh_page')); // lvl, action
    die;
}


// ============== B O O K M A R K S =============== //



/**
 * Refreshes the right_div containing the bookmarks
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $has_bookmark
 * @global type $default_personal_bm_order
 * @global type $default_official_bm_order
 * @param type $display
 * @param type $official_bookmarks
 * @param type $personal_bookmarks
 * @return boolean
 */
function bookmarks_list_update($display = true, &$official_bookmarks = array(), &$personal_bookmarks = array())
{
    global $repository_path;
    global $user_files_path;
    global $has_bookmark;
    global $default_personal_bm_order;
    global $default_official_bm_order;

    ezmam_repository_path($repository_path);

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    if (!isset($album) || $album == '') {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }

    $has_bookmark = acl_user_is_logged() && acl_has_album_permissions($album);

    if ($has_bookmark) {
        user_prefs_repository_path($user_files_path);
        // bookmarks to display in 'div_right_assets.php'
        if (isset($asset) && $asset != '' && ezmam_asset_exists($album, $asset)) {
            $personal_bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
        } else {
            $personal_bookmarks = user_prefs_album_bookmarks_list_get($_SESSION['user_login'], $album);
        }
        // sorts the bookmarks following user's prefs
        $order = acl_value_get("personal_bm_order");
        if (isset($order) && $order != '' && $order != $default_personal_bm_order) {
            $personal_bookmarks = array_reverse($personal_bookmarks);
        }
    }
    if (isset($asset) && $asset != '' && ezmam_asset_exists($album, $asset)) {
        $official_bookmarks = toc_asset_bookmark_list_get($album, $asset);
    } else {
        $official_bookmarks = toc_album_bookmarks_list_get($album);
    }
    // sorts the bookmarks following user's prefs
    $order = acl_value_get("official_bm_order");
    if (isset($order) && $order != '' && $order != $default_official_bm_order) {
        $official_bookmarks = array_reverse($official_bookmarks);
    }

    if ($display) {
        if ($_SESSION['ezplayer_mode'] == 'view_album_assets') {
            include_once template_getpath('div_right_assets.php');
        } else {
            include_once template_getpath('div_right_details.php');
        }
    }
    return true;
}

// ============== C H A T ================= //


/**
 * Removes private messages from the list
 * @param type $messages_array
 * @return type
 */
function chat_messages_remove_private($messages_array)
{
    $chat_messages = array();
    $match_user = '@' . $_SESSION['user_login'];
    foreach ($messages_array as $message) {
        if (($message['authorId'] == $_SESSION['user_login']) || $message['message'][0] !== '@' ||
                substr($message['message'], 0, strlen($match_user)) === $match_user) {
            $chat_messages[] = $message;
        }
    }
    return $chat_messages;
}


// ============= V A R I O U S ================ //

/**
 * Adds a label 'edited on' in the message when a thread/comment is edited
 * @return string
 */
function edited_on()
{
    $date = date('d-m-Y H:i:s');
    $msg = PHP_EOL . PHP_EOL . '<i style="font-size: 10px;">' . template_get_message('Last_edit', get_lang()) . ' ' . $date . '</i>';
    return $msg;
}


/**
 * saves an action in a trace file
 * @global type $ezplayer_trace
 * @global type $trace_on
 * @param type $array
 * @return boolean
 */
function trace_append($array)
{
    global $ezplayer_trace_path;
    global $trace_on;

    if (!$trace_on) {
        return false;
    }

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
    if (!isset($_SESSION['user_login']) || empty($_SESSION['user_login']) || $_SESSION['user_login'] === "anon") {
        $data .= 'nologin';
    }
    // General case, where there is a login and (possibly) a real login
    elseif (isset($_SESSION['real_login'])) {
        $data .= $_SESSION['real_login'] . '/' . $_SESSION['user_login'];
    } else {
        $data .= $_SESSION['user_login'];
    }
    $data .= ' | ';

    $idx = 0;
    $max_idx = count($array);
    foreach ($array as $value) {
        $idx++;
        $data .= preg_replace("/\|/", "-", $value);
        if ($idx != $max_idx) {
            $data .= ' | ';
        }
    }
    // 6) And we add a carriage return for readability
    $data .= PHP_EOL;

    if (!is_dir($ezplayer_trace_path)) {
        mkdir($ezplayer_trace_path, 0755, true);
    }

    file_put_contents($ezplayer_trace_path . '/' . date('Y-m-d') . '.trace', $data, FILE_APPEND | LOCK_EX);
}

function thread_details_update($display = true)
{
    global $input;
    
    if (array_key_exists('thread_id', $input) && $input['thread_id'] != null) {
        $id = $input['thread_id'];
        $_SESSION['current_thread'] = $id;
    } else {
        $id = $_SESSION['current_thread'];
    }

    $thread = thread_select_by_id($id);
    $thread['best_comment'] = comment_select_best($id);
    $thread['comments'] = comment_select_by_thread($id);
    

    if ($display) {
        include template_getpath('div_thread_details.php');
        return true;
    } else {
        return $thread;
    }
}

function logout()
{
    // Deleting the ACLs from the session var
    log_append("logout");
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'logout'));
    acl_exit();

    // Unsetting session vars
    unset($_SESSION['ezplayer_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['ezplayer_logged']); // "boolean" stating that we're logged
    unset($_SESSION['ezplayer_anonymous']); // "boolean" stating that we're logged
    session_destroy();
}
