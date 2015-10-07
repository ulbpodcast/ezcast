<?php

/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	   Arnaud Wijns <awijns@ulb.ac.be>
 *         Antoine Dewilde
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
 */

/**
 * ezcast EZmanager main program (MVC Controller)
 * @package ezcast.ezmanager.main
 */
require_once 'config.inc';
session_name($appname);
session_start();
require_once 'lib_acl.php';
require_once 'lib_error.php';
require_once 'lib_ezmam.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_template.php';
require_once 'lib_various.php';
require_once 'lib_upload.php';
require_once 'lib_toc.php';
$input = array_merge($_GET, $_POST);

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

//
// Login/logout
//
// If we're not logged in, we try to log in or display the login form
if (!user_logged_in()) {
    if (isset($input['action']) && $input['action'] == 'view_help') {
        view_help();
        die;
    }
    // Step 2: Logging in a user who already submitted the form
    if (isset($input['action']) && $input['action'] == 'login') {
        if (!isset($input['login']) || !isset($input['passwd'])) {
            error_print_message(template_get_message('empty_username_password', get_lang()));
            die;
        }

        user_login($input['login'], $input['passwd']);
    }
    // This is a tricky case:
    // If we do not have a session, but we have an action, that means we lost the
    // session somehow and are trying to load part of a page through AJAX call.
    // We do not want the login page to be displayed randomly inside a div,
    // so we refresh the whole page to get a full-page login form.
    else if (isset($input['action']) && $input['action'] != 'login' && $input['action'] != 'logout') {
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
else if (isset($_SESSION['podman_logged']) && (!isset($input['action']) || empty($input['action']))) {
    redraw_page();
}

// At this point of the code, the user is logged in and explicitly specified an action.
// We perform the action specified.
else {
    $action = $input['action'];
    $redraw = false;

    //
    // Actions
    //
    // Controller goes here
    switch ($action) {
        // The user clicked on an album, we display its content to them

        case 'view_album':
            view_album();

            break;

        // The user clicked on an asset, we display its details to them
        case 'view_asset_details':
            asset_view();
            break;

        // Display the help page
        case 'view_help':
            view_help();
            break;

        // Display the update page
        case 'view_update':
            view_update();
            break;

        // The user selected an album to create. We now create the two albums (-pub and -priv) they want
        case 'create_album':
            album_create();
            break;

        // The user chose to delete an album.
        case 'delete_album':
            album_delete();
            break;

        // reset rss token
        case 'reset_rss':
            reset_rss();
            break;

        //The users wants to upload an asset into the current album, show lets show him the upload form
        case 'submit_media_progress_bar':
            submit_media_progress_bar();
            break;

        case 'view_submit_media':
            view_submit_media();
            break;

        case 'view_edit_album':
            view_edit_album();
            break;

        // users has filled in the edit album form and has confirmed
        case 'edit_album':
            album_edit();
            break;

        //user has filled in the upload form, we need to handle the data.
        case 'submit_media':
            submit_media();
            break;

        // Called by APC plugin to get info on current upload
        case 'get_upload_progress':
            get_upload_progress();
            break;

        case 'upload_init':
            upload_init();
            break;

        case 'upload_chunk':
            upload_chunk();
            break;

        case 'upload_finished':
            upload_finished();
            break;

        case 'upload_error':
            upload_error();
            break;

        case 'edit_asset':
            asset_edit();
            break;

        case 'asset_downloadable_set':
            asset_downloadable_set();
            break;

        case 'delete_asset':
            asset_delete();
            break;

        case 'move_asset':
            asset_move();
            break;

        //move asset from album -priv to -pub
        case 'publish_asset':
            asset_publish_unpublish('publish');
            break;

        //move asset from album -pub to -priv
        case 'unpublish_asset':
            asset_publish_unpublish('unpublish');
            break;

        //schedule publication / archiving of asset from album -pub to -priv
        case 'schedule_asset':
            asset_schedule();
            break;

        //cancel the scheduling
        case 'cancel_schedule_asset':
            asset_schedule_cancel();
            break;

        // Returning the content to display in a popup
        case 'show_popup':
            view_popup();
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

        //debugging should be removed in prod
        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            albums_view();
    }
}

//
// Helper functions
//

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in() {
    return isset($_SESSION['podman_logged']);
}

//
// Display functions
//

/**
 * Displays the login form
 */
function view_login_form() {
    global $ezmanager_url;
    global $error, $input;

    //check if we receive a no_flash parameter (to disable flash progressbar on upload)
    if (isset($input['no_flash']))
        $_SESSION['has_flash'] = false;
    $url = $ezmanager_url;
    // template include goes here
    include_once template_getpath('login.php');
    //include_once "tmpl/fr/login.php";
}

/**
 * Displays the main frame, without anything on the right side
 */
function albums_view() {
    global $url;
    // Used in redraw mode only
    global $album_name;
    global $album_name_full;
    global $description;
    global $public_album;
    global $assets;
    global $hd_rss_url;
    global $sd_rss_url;
    global $hd_rss_url_web;
    global $sd_rss_url_web;
    global $player_full_url;
    global $head_code; // Optional code we want to append in the HTML header
    // List of all the albums a user has created
    $created_albums = acl_authorized_albums_list_created(); // Used to display the albums list
    $allowed_albums = acl_authorized_albums_list();
    $not_created_albums_with_descriptions = acl_authorized_albums_list_not_created(true); // Used to display the popup_new_album

    $_SESSION['podman_mode'] = 'view_main';

    include_once template_getpath('main.php');
    //include_once "tmpl/fr/main.php";
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
    global $hd_rss_url;
    global $sd_rss_url;
    global $hd_rss_url_web;
    global $sd_rss_url_web;
    global $player_full_url;
    global $ezmanager_url;
    global $distribute_url;
    global $ezplayer_url;
    ezmam_repository_path($repository_path);

    $action = $_SESSION['podman_mode'];
    $redraw = true;
    if (isset($_SESSION['podman_album'])) {
        $current_album = $_SESSION['podman_album'];
        $current_album_is_public = album_is_public($_SESSION['podman_album']);

        $album_name = suffix_remove($_SESSION['podman_album']);
        ;
        $album_name_full = $_SESSION['podman_album'];
        $metadata = ezmam_album_metadata_get($_SESSION['podman_album']);
        $description = $metadata['description'];
        $public_album = $current_album_is_public;
        $assets = ezmam_asset_list_metadata($_SESSION['podman_album']);
        $hd_rss_url = $distribute_url . '?action=rss&amp;album=' . $current_album . '&amp;quality=high&amp;token=' . ezmam_album_token_get($album_name_full);
        $sd_rss_url = $distribute_url . '?action=rss&amp;album=' . $current_album . '&amp;quality=low&amp;token=' . ezmam_album_token_get($album_name_full);
        $hd_rss_url_web = $distribute_url . '?action=rss&album=' . $current_album . '&quality=high&token=' . ezmam_album_token_get($album_name_full);
        $sd_rss_url_web = $distribute_url . '?action=rss&album=' . $current_album . '&quality=low&token=' . ezmam_album_token_get($album_name_full);
        $player_full_url = $ezplayer_url . "?action=view_album_assets&album=" . $current_album . "&token=" . ezmam_album_token_get($album_name_full);
    }

    // Whatever happens, the first thing to do is display the whole page.
    albums_view();
}

/**
 * Reloads the whole page
 */
function refresh_page() {
    global $ezmanager_url;
    // reload the page
    echo '<script>window.location.reload();</script>';
    die;
}

/**
 * Displays the album passed in GET or POST "album" parameter, if it exists and is managable by the user.
 */
function view_album() {
    // Initialization: we retrieve various variable we'll need later on
    global $input;
    global $repository_path;
    global $ezmanager_url; // Website URL, defined in config.inc
    global $distribute_url;
    global $ezplayer_url;

    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['podman_album'];
    ezmam_repository_path($repository_path);

    //
    // 0) Sanity checks
    //
    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', "view_album: tried to access album " . $album . ' without permission');
        die;
    }

    //
    // 1) We retrieve the metadata relating to the album
    //
    $metadata = ezmam_album_metadata_get($album);

    //
    // 2) We set the variables used in the template with the correct values
    //
    $album_name_full = $album; // complete album name, used for div identification
    $album_name = suffix_remove($album); // "user-friendly" album name, used for display
    $description = $metadata['description'];
    $public_album = album_is_public($album); // Whether the album is public; used to display the correct options
    $hd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=high&amp;token=' . ezmam_album_token_get($album);
    $sd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=low&amp;token=' . ezmam_album_token_get($album);
    $hd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=high&token=' . ezmam_album_token_get($album);
    $sd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=low&token=' . ezmam_album_token_get($album);
    $player_full_url = $ezplayer_url . "?action=view_album_assets&album=" . $album . "&token=" . ezmam_album_token_get($album);
    $assets = ezmam_asset_list_metadata($album_name_full);

    //
    // 3) We save the current album view in a session var
    //
    $_SESSION['podman_mode'] = 'view_album';
    $_SESSION['podman_album'] = $album;

    //
    // 4) Then we display the album
    //
    include template_getpath('div_album_header.php');
    include template_getpath('div_asset_list.php');
}

/**
 * This function shows the asset details div for the asset passed by POST, GET or SESSION
 * @global type $input
 * @global type $repository_path 
 */
function asset_view() {
    global $input;
    global $repository_path;
    global $ezmanager_url;


    // Setting up various variables we'll need later
    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['album'];

    if (isset($input['asset']))
        $asset = $input['asset'];
    else
        $asset = $_SESSION['asset'];

    ezmam_repository_path($repository_path);

    //
    // 0) Sanity checks
    //
    if (!isset($album) || !ezmam_album_exists($album)) {
        error_print_message(template_get_message('Non-existant_album', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        error_print_message(template_get_message('Non-existant_asset', get_lang()));
        log_append('warning', 'view_asset_details: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $album . ' without permission');
        die;
    }

    //
    // 1) We retrieve the metadata for the asset and its media
    //
    $asset_metadata = ezmam_asset_metadata_get($album, $asset);
    $media_metadata = ezmam_media_list_metadata_assoc($album, $asset);

    //
    // 2) Now we can set up the variables used in the template
    //
    $asset_name = $asset; // "Technical" asset name
    $author = $asset_metadata['author']; // Asset author
    $title = $asset_metadata['title']; // "user-friendly" asset name (title)
    $description = $asset_metadata['description']; // Asset description
    $date = get_user_friendly_date($asset_metadata['record_date'], ' ', true, get_lang(), true); // Recording date, i.e. asset creation date
    $origin = $asset_metadata['origin'];
    $status = $asset_metadata['status'];
    $public_album = album_is_public($album); // Whether the album the asset is public or not
    $has_cam = (strpos($asset_metadata['record_type'], 'cam') !== false); // Whether or not the asset has a "live-action" video
    $has_slides = (strpos($asset_metadata['record_type'], 'slide') !== false); // Whether or not the asset has slides
    $created_albums_list_with_descriptions = acl_authorized_albums_list_created(true); // List of all the created albums (used for asset move)
    $asset_token = ezmam_asset_token_get($album, $asset); // Asset token, used for embedded media player (preview)
    $asset_scheduled = $asset_metadata['scheduled'];
    $asset_sched_date = $asset_metadata['schedule_date'];
    $asset_sched_id = $asset_metadata['schedule_id'];
    // Filling in the data about the media
    // all you want to know about high res camera video
    if (isset($media_metadata['high_cam'])) {
        $filesize_cam['HD'] = $media_metadata['high_cam']['file_size'];
        $dimensions_cam['HD'] = $media_metadata['high_cam']['width'] . ' x ' . $media_metadata['high_cam']['height'];
        $format_cam = $media_metadata['high_cam']['codec'];
    }

    // Everything about the low-res version of the camera video
    if (isset($media_metadata['low_cam'])) {
        $filesize_cam['SD'] = $media_metadata['low_cam']['file_size'];
        $dimensions_cam['SD'] = $media_metadata['low_cam']['width'] . ' x ' . $media_metadata['low_cam']['height'];
    }

    // Everything about the high-res slides video
    if (isset($media_metadata['high_slide'])) {
        $filesize_slides['HD'] = $media_metadata['high_slide']['file_size'];
        $dimensions_slides['HD'] = $media_metadata['high_slide']['width'] . ' x ' . $media_metadata['high_slide']['height'];
        $format_slides = $media_metadata['high_slides']['codec'];
    }

    // Everything about the low-res slides
    if (isset($media_metadata['low_slide'])) {
        $filesize_slides['SD'] = $media_metadata['low_slide']['file_size'];
        $dimensions_slides['SD'] = $media_metadata['low_slide']['width'] . ' x ' . $media_metadata['low_slide']['height'];
    }

    // To get the duration, we use the high cam media if it exists, or the high_slide
    // media otherwise. We assume at least one of these exists.
    $duration = $media_metadata['high_cam']['duration'];
    if (!isset($duration) || empty($duration))
        $duration = $media_metadata['high_slide']['duration'];
    $duration = get_user_friendly_duration($duration);

    // Finally, we set up the URLs and view counts to the different media
    if ($has_cam) {
        $url_cam['HD'] = ezmam_media_geturl($album, $asset, 'high_cam');
        $url_cam['SD'] = ezmam_media_geturl($album, $asset, 'low_cam');
        // specific code that contains information about the media
        $code_cam['HD'] = get_code_to_media($album, $asset, 'high_cam');
        $code_cam['SD'] = get_code_to_media($album, $asset, 'low_cam');
        $view_count_cam = ezmam_media_viewcount_get($album, $asset, 'high_cam') + ezmam_media_viewcount_get($album, $asset, 'low_cam');
    }
    if ($has_slides) {
        $url_slides['HD'] = ezmam_media_geturl($album, $asset, 'high_slide');
        $url_slides['SD'] = ezmam_media_geturl($album, $asset, 'low_slide');
        $code_slide['HD'] = get_code_to_media($album, $asset, 'high_slide');
        $code_slide['SD'] = get_code_to_media($album, $asset, 'low_slide');
        $view_count_slides = ezmam_media_viewcount_get($album, $asset, 'high_slide') + ezmam_media_viewcount_get($album, $asset, 'low_slide');
    }

    $file_name = '';
    if ($origin == 'SUBMIT') {
        if ($asset_metadata['submitted_cam'] != '' && $asset_metadata['submitted_slide'] != '')
            $file_name = $asset_metadata['submitted_cam'] . ' & ' . $asset_metadata['submitted_slide'];
        else if ($asset_metadata['submitted_cam'] != '')
            $file_name = $asset_metadata['submitted_cam'];
        else
            $file_name = $asset_metadata['submitted_slide'];
    }

    //
    // 3) We save the current state in session vars
    //
    $_SESSION['podman_mode'] = 'view_asset_details';
    $_SESSION['podman_album'] = $input['album'];
    $_SESSION['podman_asset'] = $input['asset'];

    //
    // 4) Then display the asset and its content
    //
    require_once template_getpath('div_asset_details.php');
}

/**
 * Displays the help page
 */
function view_help() {
    require_once template_getpath('help.php');
}

/**
 * Displays the update page
 */
function view_update() {
    require_once template_getpath('update.php');
}

//
// "Business logic" functions
//

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_login($login, $passwd) {
    global $input;
    global $template_folder;
    global $error;
    global $ezmanager_url;

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
    }

    $res = checkauth(strtolower($login), $passwd);
    if (!$res) {
        $error = checkauth_last_error();
        view_login_form();
        die;
    }

    // 1) Initializing session vars
    $_SESSION['podman_logged'] = "LEtimin"; // "boolean" stating that we're logged
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
    if (count(acl_authorized_albums_list()) == 0) {
        error_print_message(template_get_message('not_registered', get_lang()), false);
        log_append('warning', $res['login'] . ' tried to access ezmanager but doesn\'t have permission to manage any album.');
        session_destroy();
        view_login_form();
        die;
    }

    // 4) Resetting the template path to the one of the language chosen
    template_repository_path($template_folder . get_lang());

    // 5) Logging the login operation
    log_append("login");

    // 6) Displaying the page

    header("Location: " . $ezmanager_url);
    albums_view();
}

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function user_logout() {
    global $ezmanager_url;
    // 1) Deleting the ACLs from the session var
    acl_exit();

    // 2) Unsetting session vars
    unset($_SESSION['podman_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['podman_logged']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezmanager_url;

    unset($_SESSION['lang']);
}

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 * @global type $input
 * @global type $ezmanager_url
 * @global type $repository_path
 * @global type $dir_date_format
 * @global type $default_intro 
 */
function album_create() {
    global $input;
    global $ezmanager_url;
    global $repository_path;
    global $dir_date_format;
    global $default_intro;
    global $default_add_title;
    global $default_downloadable;

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'create_album: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    //
    // First of all, we have to set up the metada for the albums we're going to create
    //
    $not_created_albums = acl_authorized_albums_list_not_created(true);
    $description = $not_created_albums[$input['album']];
    $anac = get_anac(date('Y'), date('m'));
    $metadata = array(
        'name' => $input['album'],
        'description' => $description,
        'date' => date($dir_date_format),
        'anac' => $anac,
        'intro' => $default_intro,
        'add_title' => $default_add_title,
        'downloadable' => $default_downloadable
    );

    //
    // All we have to do now is call ezmam twice to create both the private and public album
    // (remember that $input['album'] only contains the album's base name, /not/ the suffix
    //
    ezmam_repository_path($repository_path);
    $res = ezmam_album_new($input['album'] . '-priv', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    $res = ezmam_album_new($input['album'] . '-pub', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Don't forget to update the session variables!
    //
    acl_update_permissions_list();

    //
    // Finally, we show a confirmation popup to the user
    //
    $public_album_url = $distribute_url . '?action=rss&amp;album=' . $input['album'] . '-pub' . '&amp;quality=high&amp;token=' . ezmam_album_token_get($input['album'] . '-pub');
    require_once template_getpath('popup_album_successfully_created.php');
}

function album_delete() {
    global $input;
    global $repository_path;

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    //
    // The only important thing to do right now is delete both the public and private albums,
    // by calling ezmam
    //
    ezmam_repository_path($repository_path);

    // Deletes the table of contents (EZcast Player)
    toc_album_bookmarks_delete_all($input['album'] . '-priv');

    $res = ezmam_album_delete($input['album'] . '-priv');
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    toc_album_bookmarks_delete_all($input['album'] . '-pub');

    $res = ezmam_album_delete($input['album'] . '-pub');
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Don't forget to update the albums list
    //
    acl_update_permissions_list();
    unset($_SESSION['podman_album']);

    //
    // Finally, we display a nice confirmation message to the user
    //
    
    require_once template_getpath('popup_album_successfully_deleted.php');
}

/**
 * Effectively deletes an asset from the repository, and displays a nice message to the user
 */
function asset_delete() {
    global $input;
    global $repository_path;

    //
    // Sanity checks
    //
    ezmam_repository_path($repository_path);

    if (!isset($input['album']) || !isset($input['asset'])) {
        echo "Usage: web_index.php?action=delete_asset&album=ALBUM&asset=ASSET";
        die;
    }

    if (!acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'delete_asset: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    if (!ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(template_get_message('Non-existant_asset', get_lang()));
        log_append('warning', 'delete_asset: tried to access asset ' . $input['asset'] . ' of album ' . $input['album'] . ' which does not exist');
        die;
    }

    // firstly, remove the at job for scheduled move
    private_asset_schedule_remove($input['album'], $input['asset']);

    // We remove the bookmarks list from the table of contents (EZcast Player)
    toc_asset_bookmarks_delete_all($input['album'], $input['asset']);
    //
    // Now we simply use lib_ezmam to delete the asset from the repository
    //
    ezmam_asset_delete($input['album'], $input['asset']);

    //
    // And that's all, we just have to print a confirmation message next!
    //
    
    //require_once template_getpath('popup_asset_successfully_deleted.php');
    //view_album();
}

/**
 * It's the function for recept the file and move in the queues in submit_upload ( progress_bar )
 * @global type $submit_upload_dir
 * @global type $dir_date_format
 * @global type $head_code
 * @global type $accepted_media_types 
 */
function submit_media_progress_bar() {
    global $submit_upload_dir;
    global $dir_date_format;
    global $head_code;
    global $accepted_media_types;
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;


    $input = array_merge($_GET, $_POST);

    if ($_FILES['media']['error'] > 0) {
        error_print_message(template_get_message('upload_error', get_lang()));
        log_append('error', 'submit_media: an error occurred during file upload (code ' . $_FILES['media']['error']);
        die;
    }
    list($type, $subtype) = explode('/', $_FILES['media']['type']);


    if (!in_array($input['type'], $accepted_media_types)) {

        error_print_message(template_get_message('Invalid_type', get_lang()));
        log_append('warning', 'submit_media: ' . $input['type'] . ' is not a valid media type');
        die;
    }




    // 2) Creating the folder in the queue, and the metadata for the Filedata
    $tmp_name = date($dir_date_format) . '_' . $input['album'];

    //$moderation = ($input['moderation']) ? 'true' : 'false';
    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $input['title'],
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_filename' => $_FILES['media']['name'],
        'submitted_mimetype' => $_FILES['media']['type'],
        'moderation' => $input['moderation'],
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => date($dir_date_format),
        'super_highres' => $input['keepQuality']
    );

    $res = media_submit_create_metadata($tmp_name, $metadata);

    // 3) Uploading the media file inside the folder

    $path = $submit_upload_dir . '/' . $tmp_name . '/' . $input['type'] . '.mov';

    $res = move_uploaded_file($_FILES['media']['tmp_name'], $path);

    // 4) Calling cli_mam_insert.php so that it adds the file into ezmam
    $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . dirname($path) . '  >>' . dirname($path) . '/mam_insert.log 2>&1"|at now';

    exec($cmd, $output, $ret);

    if ($ret != 0) {
        error_print_message('Error while trying to use cli_mam_insert: error code ' . $ret);
        die;
    }

    redraw_page();
}

// used by web worker from js/uploadfile.js to submit metadata over the file
function upload_init() {
    global $input;
    global $submit_upload_dir;
    global $dir_date_format;
    global $accepted_media_types;
    global $upload_slice_size;

    $array = array();
    // 1) Sanity checks
    $title = trim($input['title']);
    if (!isset($title) || empty($title)) {
        log_append('warning', 'upload_init: no title');
        $array["error"] = template_get_message('Missing_title', get_lang());
        echo json_encode($array);
        die;
    }

    if ($input['type'] != 'camslide' && !in_array($input['type'], $accepted_media_types)) {
        log_append('warning', 'upload_init: ' . $input['type'] . ' is not a valid media type');
        $array["error"] = template_get_message('Invalid_type', get_lang());
        echo json_encode($array);
        die;
    }

    // 2) Creating the folder in the queue, and the metadata for the media
    $record_date = date($dir_date_format);
    $tmp_name = $record_date . '_' . $input['album'];

    if ($input['moderation'] == 'false')
        $moderation = "false";
    else
        $moderation = "true";

    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $input['title'],
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_cam' => $input['cam_filename'],
        'submitted_slide' => $input['slide_filename'],
        'moderation' => $moderation,
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => $record_date,
        'super_highres' => $input['keepQuality'],
        'intro' => $input['intro'],
        'add_title' => $input['add_title'],
        'downloadable' => $input['downloadable'],
        'ratio' => $input['ratio']
    );

    $res = media_submit_create_metadata($tmp_name, $metadata);
    if (!$res) {
        log_append('warning', 'upload_init: ' . ezmam_last_error());
        $array["error"] = ezmam_last_error();
        echo json_encode($array);
        die;
    }

    // 3) saves informations for coming upload

    $path = $submit_upload_dir . '/' . $tmp_name . '/';

    $_SESSION[$tmp_name] = array(
        'path' => $path,
        'type' => $input['type'],
        'album' => $input['album'],
        'record_date' => $record_date,
        'cam' => array('index' => 0, 'finished' => false, 'concat' => -1),
        'slide' => array('index' => 0, 'finished' => false, 'concat' => -1));

    $array['values'] = array("id" => $tmp_name, "chunk_size" => $upload_slice_size);
    echo json_encode($array);
}

// used by web worker from js/uploadfile.js to upload chunk of file
// The upload is serial which means that each slice of file is the sequel of 
// the previous one. We can then append each slice to the previous one to
// create the movie file.
function upload_chunk() {
    global $accepted_media_types;
    global $upload_slice_size;

    $array = array();

    $index = $_SERVER['HTTP_X_INDEX'];
    $id = $_SERVER['HTTP_X_ID'];
    $type = $_SERVER['HTTP_X_TYPE'];

    if (!isset($id) || empty($id) || !isset($_SESSION[$id])) {
        log_append('warning', 'upload_chunk: ' . ' current upload id is not set or not valid');
        $array["error"] = template_get_message('Invalid_id', get_lang());
        echo json_encode($array);
        die;
    }

    $path = $_SESSION[$id]['path'];

// path must be in proper format
    if (!isset($path)) {
        log_append('warning', 'upload_chunk: ' . ' cannot find file upload path');
        $array["error"] = template_get_message('Invalid_path', get_lang());
        echo json_encode($array);
        die;
    }

// type must be in proper format
    if (!isset($type) || !in_array($type, $accepted_media_types)) {
        log_append('warning', 'upload_chunk: ' . $input['type'] . ' is not a valid media type');
        $array["error"] = template_get_message('Invalid_type', get_lang());
        echo json_encode($array);
        die;
    }

// index must be set, and number
    if (!isset($index) || !preg_match('/^[0-9]+$/', $index)) {
        log_append('warning', 'upload_chunk: ' . $index . ' is not a valid index');
        $array["error"] = template_get_message('Invalid_index', get_lang());
        echo json_encode($array);
        die;
    }

    // index must be as expected (previous + 1)
    $current_index = $_SESSION[$id][$type]['index'];
    if ($index == $current_index) {
        $_SESSION[$id][$type]['index'] += 1;
    } else {
        log_append('warning', 'upload_chunk: expected index [' . $current_index . '] found index [' . $index . ']');
        $array["error"] = template_get_message('bad_sequence', get_lang());
        echo json_encode($array);
        die;
    }

// we store chunks in directory named after filename
    /*    if (!file_exists("$path/" . $type . '/')) {
      mkdir("$path/" . $type . '/');
      }

      $target = "$path/" . $type . '/' . $type . '-' . $index;
     */

    /*
      // alternative way
      $putdata = fopen("php://input", "r");
      $fp = fopen($target, "w");
      while ($data = fread($putdata, 1024))
      fwrite($fp, $data);
      fclose($fp);
      fclose($putdata);
     */

    //  $input = fopen("php://input", "r");
    //  file_put_contents($target, $input);

    $target = "$path/" . $type . '.mov';

    // the incoming stream is put at the end of the file
    $input = fopen("php://input", "r");

    if (filesize($target) <= 2048000000) {
        /*  $fp = fopen($target, "a");
          while ($data = fread($input, $upload_slice_size)) {
          fwrite($fp, $data);
          }
          fclose($fp);
          fclose($index);
         */
        $res = file_put_contents($target, $input, FILE_APPEND | LOCK_EX);
    } else {
        // if the file is bigger than 2Go, PHP 32bit cannot handle it.
        // We then save each chunk in a separate file and concatenate
        // the final file in command line (see upload_finished())
        if (!file_exists("$path/" . $type . '/')) {
            // creates the directory for files to be concat
            mkdir("$path/" . $type . '/');
            // saves index of the first file to be concat
            $_SESSION[$id][$type]['concat'] = $index;
        }
        $target = "$path/" . $type . '/' . $type . '-' . $index;
        $input = fopen("php://input", "r");
        file_put_contents($target, $input);
    }

    if ($res === false) {
        log_append('warning', 'upload_chunk: ' . "error while writting chunk $index");
        $array["error"] = template_get_message('write_error', get_lang());
        echo json_encode($array);
        die;
    }

    // required by js/fileupload.js
    $array['value'] = "OK";
    echo json_encode($array);
}

// used by web worker from js/uploadfile.js to launch the mam_insert
function upload_finished() {
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;
    global $input;
    global $accepted_media_types;

    $array = array();

    $index = $input['index'];
    $id = $input['id'];
    $type = $input['type'];
    $path = $_SESSION[$id]['path'];

    if (!isset($_SESSION[$id])) {
        log_append('warning', 'upload_finished: ' . ' current upload id is not set or not valid');
        $array["error"] = template_get_message('Invalid_id', get_lang());
        echo json_encode($array);
        die;
    }

    if ($index != $_SESSION[$id][$type]['index']) {
        log_append('warning', 'upload_finished: ' . ' missing file chunks [' . $_SESSION[$id][$type]['index'] . '/' . $index . ']');
        $array["error"] = template_get_message('missing_chunks', get_lang());
        echo json_encode($array);
        die;
    }

    if ($_SESSION[$id][$type]['concat'] > -1) {
        // file is bigger than 2Go
        // Everything that exceeds 2Go has been saved 
        // as separated files in path/type/. and
        // must be concatenated now 
        $dest = $path . "/" . $type . ".mov";
        for ($i = $_SESSION[$id][$type]['concat']; $i <= $index; $i++) {
            $src = $path . "/" . $type . "/" . $type . "-" . $i;
            $cmd = "cat $src >> $dest";
            exec($cmd);
            unlink($src);
        }
        rmdir($path . '/' . $type);
    }

    $finished = true;
    if ($_SESSION[$id]['type'] == 'camslide') {
        $_SESSION[$id][$type]['finished'] = true;
        foreach ($accepted_media_types as $type) {
            $finished = $finished && $_SESSION[$id][$type]['finished'];
        }
    }

    if ($finished) {

        // recreates full file
        /* $target = "$path/$type.mov";
          $dst = fopen($target, 'wb');

          for ($i = 0; $i < $index; $i++) {
          $slice = $path . '/' . $type . '/' . $type . '-' . $i;
          $src = fopen($slice, 'rb');
          stream_copy_to_stream($src, $dst);
          fclose($src);
          unlink($slice);
          }

          fclose($dst);
          rmdir($path . '/' . $type);
         */
        // Calling cli_mam_insert.php so that it adds the file into ezmam
        $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . $path . ' >>' . $path . '/mam_insert.log 2>&1"|at now';

        exec($cmd, $output, $ret);

        if ($ret != 0) {

            log_append('warning', 'upload_finished: ' . ' Error while trying to use cli_mam_insert: error code ' . $ret);
            $array["error"] = ' Error while trying to use cli_mam_insert: error code ' . $ret;
            echo json_encode($array);
            die;
            error_print_message(' Error while trying to use cli_mam_insert: error code ' . $ret);
            die;
        }
    } else {
        $array["wait"] = 'Wait until all files are submitted';
        echo json_encode($array);
        die;
    }


    $array['value'] = "OK";
    echo json_encode($array);
}

function upload_error() {
    global $input;

    $res = media_submit_error($input['id']);
    if (!$res) {
        log_append('warning', 'upload_error: ' . ezmam_last_error());
        $array["error"] = ezmam_last_error();
        echo json_encode($array);
        die;
    }
}

/**
 * Processes media submission 
 * Used only by old web browsers (when xhr2 is not supported)
 * @global type $input 
 */
function submit_media() {
    global $input;
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;
    global $submit_upload_dir;
    global $head_code;
    global $dir_date_format;
    global $accepted_media_types;

    // 1) Sanity checks
    $title = trim($input['title']);
    if (!isset($title) || empty($title)) {
        error_print_message('no Title');
        die;
    }


    if ($_FILES['media']['error'] > 0) {
        log_append('error', 'submit_media: an error occurred during file upload (code ' . $_FILES['media']['error'] . ')');
        error_print_message(template_get_message('upload_error', get_lang()));
        die;
    }

    if (!in_array($input['type'], $accepted_media_types)) {
        log_append('warning', 'submit_media: ' . $input['type'] . ' is not a valid media type');
        error_print_message(template_get_message('Invalid_type', get_lang()));
        die;
    }

    // 2) Creating the folder in the queue, and the metadata for the media
    $tmp_name = date($dir_date_format) . '_' . $input['album'];
    if ($input['moderation'] == 'false')
        $moderation = "false";
    else
        $moderation = "true";
    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $input['title'],
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_cam' => $_FILES['media']['name'],
        'submitted_mimetype' => $_FILES['media']['type'],
        'moderation' => $moderation,
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => date($dir_date_format),
        'super_highres' => $input['keepQuality'],
        'intro' => $input['intro'],
        'add_title' => $input['add_title'],
        'downloadable' => $input['downloadable'],
        'ratio' => $input['ratio']
    );
//    assoc_array2metadata_file($metadata, './metadata_tmp.xml');
    $res = media_submit_create_metadata($tmp_name, $metadata);
    echo $res;
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    // 3) Uploading the media file inside the folder

    $path = $submit_upload_dir . '/' . $tmp_name . '/' . $input['type'] . '.mov';

    $res = move_uploaded_file($_FILES['media']['tmp_name'], $path);
    if (!$res) {
        error_print_message('submit_media: unable to move the uploaded file');
        die;
    }

    // 4) Calling cli_mam_insert.php so that it adds the file into ezmam
    $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . dirname($path) . ' >>' . dirname($path) . '/mam_insert.log 2>&1"|at now';

    exec($cmd, $output, $ret);

    if ($ret != 0) {
        error_print_message(' Error while trying to use cli_mam_insert: error code ' . $ret);
        die;
    }
    echo "success";
    return true;
    // 5) Displaying a confirmation alert.
    $head_code = '<script type="text/javascript">$(document).ready(function() {window.alert(\'Fichier envoyé et en cours de traitement.\');});</script>';
    redraw_page();
}

function album_edit() {
    global $input;
    global $repository_path;

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['moderation'])) {
        echo "Usage: index.php?action=edit_album&session=SESSION_ID&intro=INTRO&addTitle=ADD_TITLE";
        die;
    }

    if ($input['moderation'] == true) {
        $album = $input['album'] . '-priv';
    } else {
        $album = $input['album'] . '-pub';
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($album)) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Then we update the metadata
    //
    $album_meta = ezmam_album_metadata_get($album);

    $album_meta['intro'] = $input['intro'];
    $album_meta['add_title'] = $input['add_title'];
    $album_meta['downloadable'] = $input['downloadable'];

    $res = ezmam_album_metadata_set($album, $album_meta);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //  view_main();
    require_once template_getpath('popup_album_successfully_edited.php');
}

/**
 * Edits asset data and re-draws the asset details
 * @global type $input 
 */
function asset_edit() {
    global $input;
    global $repository_path;
    global $title_max_length;

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['title'])) {
        echo "Usage: index.php?action=edit_asset&amp;album=ALBUM&amp;asset=ASSET&amp;title=NEW_TITLE";
        die;
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    if (strlen($input['title']) > $title_max_length) {
        error_print_message(template_get_message('title_too_long', get_lang()));
        die;
    }

    //
    // Then we update the metadata
    //
    $metadata = ezmam_asset_metadata_get($input['album'], $input['asset']);

    $metadata['title'] = $input['title'];
    $metadata['description'] = $input['description'];

    $res = ezmam_asset_metadata_set($input['album'], $input['asset'], $metadata);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // And we display the (new) asset details
    //
    asset_view();
}

function asset_downloadable_set() {
    global $input;
    global $repository_path;

    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['downloadable'])) {
        die;
    }

    ezmam_repository_path($repository_path);

    $metadata = ezmam_asset_metadata_get($input['album'], $input['asset']);

    $metadata['downloadable'] = $input['downloadable'];

    $res = ezmam_asset_metadata_set($input['album'], $input['asset'], $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
}

/**
 * Moves asset from $input['from'] to $input['to']
 * @global type $input
 * @global type $repository_path 
 */
function asset_move() {
    global $input;
    global $repository_path;
    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['asset']) || !isset($input['from']) || !isset($input['to'])) {
        echo 'Usage: web_index.php?action=move_asset&amp;from=SOURCE&amp;to=DESTINATION&amp;asset=ASSET';
        die;
    }

    if (!acl_has_album_permissions($input['from']) || !acl_has_album_permissions($input['to'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'move_asset: you can\'t manage album ' . $input['from'] . ' or ' . $input['to']);
        die;
    }

    if (!ezmam_asset_exists($input['from'], $input['asset'])) {
        error_print_message(template_get_message('Non-existant_album', get_lang()));
        log_append('warning', 'move_asset: asset ' . $input['asset'] . ' of album ' . $input['from'] . ' does not exist');
        die;
    }
    
    private_asset_schedule_remove($input['from'], $input['asset']);

    // saves the bookmarks to copy
    $bookmarks = toc_asset_bookmark_list_get($input['from'], $input['asset']);
    // deletes the bookmarks from the source album
    toc_asset_bookmarks_delete_all($input['from'], $input['asset']);
    //
    // Moving the asset
    // TODO: the moving won't work if there is a different asset with the same name in dest folder. Should be corrected in the future (new asset renamed)
    //
    $res = ezmam_asset_move($input['asset'], $input['from'], $input['to']);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    // adds the previously saved bookmarks to the new album
    $count = count($bookmarks);
    for ($index = 0; $index < $count; $index++) {
        $bookmarks[$index]['album'] = $input['to'];
    }
    toc_album_bookmarks_add($bookmarks);

    include_once template_getpath('popup_asset_successfully_moved.php');
}

/**
 * Resets the RSS feed for both high and low res, and displays a confirmation message to the user
 */
function reset_rss() {
    global $input;
    global $repository_path;

    ezmam_repository_path($repository_path);

    if (!isset($input['album'])) {
        echo "Usage: web_index.php?action=reset_rss&album=ALBUM";
        die;
    }

    if (!ezmam_album_exists($input['album'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    if (!ezmam_album_exists($input['album'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // We just have to reset the tokens ...
    //
    ezmam_album_token_reset($input['album']);

    //
    // ... And display a confirmation message!
    //
    require_once template_getpath('popup_rss_successfully_reset.php');
}

/**
 * Publishes or unpublishes an asset. The parameter $action tells us if we want the asset to be moved to the public ('publish') album or private ('unpublish') album.
 * @global type $input
 * @global type $repository_path
 * @param string $action publish|unpublish
 */
function asset_publish_unpublish($action = 'publish') {
    global $input;
    global $repository_path;
    ezmam_repository_path($repository_path);

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset'])) {
        echo "Usage: web_index.php?action=publish_asset&amp;album=ALBUM&amp;asset=ASSET";
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    private_asset_schedule_remove($input['album'], $input['asset']);
    //
    // (Un)publishing the asset, and displaying a confirmation message.
    //
    if ($action == 'publish') {
        $res = ezmam_asset_publish($input['album'], $input['asset']);
        if (!$res) {
            error_print_message(ezmam_last_error());
            die;
        }
        // moves asset bookmarks from private to public 
        toc_album_bookmarks_swap($input['album'], $input['asset']);

        require_once template_getpath('popup_asset_successfully_published.php');
        //include_once "tmpl/fr/popup_asset_successfully_published.php";
    } else if ($action == 'unpublish') {
        $res = ezmam_asset_unpublish($input['album'], $input['asset']);
        if (!$res) {
            error_print_message(ezmam_last_error());
            die;
        }
        // moves asset bookmarks from public to private
        toc_album_bookmarks_swap($input['album'], $input['asset']);

        require_once template_getpath('popup_asset_successfully_unpublished.php');
    } else {
        error_print_message('Publish_unpublish: no operation provided');
        die;
    }

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
}

/**
 * Schedules the publication / archiving of an asset
 * @global type $input
 * @global type $repository_path
 * @global type $php_cli_cmd
 * @global type $asset_publish_pgm
 * @global type $action
 */
function asset_schedule() {
    global $input;
    global $repository_path;
    global $php_cli_cmd;
    global $asset_publish_pgm;
    global $action;

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['date'])) {
        echo "Usage: index.php?action=schedule_asset&album=ALBUM&asset=ASSET&date=DATE";
        die;
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($input['album'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    if (!ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }


    $action = (album_is_public($input['album'])) ? "unpublish" : "publish";
    $date = date("H:i M d, Y", strtotime($input["date"]));

    $cmd = "echo '" . $php_cli_cmd . " " . $asset_publish_pgm . " " . $input["album"] . " " . $input["asset"] . " " . $action . "' | at " . $date . "  2>&1 | awk '/job/ {print $2}'";
    $at_id = shell_exec($cmd);
    //
    // Then we update the metadata
    //
    $asset_meta = ezmam_asset_metadata_get($input["album"], $input["asset"]);

    $asset_meta['scheduled'] = true;
    $asset_meta['schedule_id'] = $at_id;
    $asset_meta['schedule_date'] = $input['date'];

    $res = ezmam_asset_metadata_set($input["album"], $input["asset"], $asset_meta);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //  view_main();
    require_once template_getpath('popup_asset_successfully_scheduled.php');
}

/**
 * Cancel the scheduling of an asset
 */
function asset_schedule_cancel() {
    global $input;
    global $repository_path;

    //
    // Sanity checks
    //
    ezmam_repository_path($repository_path);

    if (!isset($input['album']) || !isset($input['asset'])) {
        echo "Usage: web_index.php?action=delete_asset&album=ALBUM&asset=ASSET";
        die;
    }

    if (!acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'delete_asset: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    if (!ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(template_get_message('Non-existant_asset', get_lang()));
        log_append('warning', 'delete_asset: tried to access asset ' . $input['asset'] . ' of album ' . $input['album'] . ' which does not exist');
        die;
    }
    private_asset_schedule_remove($input['album'], $input['asset']);
    redraw_page();
    //require_once template_getpath('popup_asset_successfully_deleted.php');
    //view_album();
}

function private_asset_schedule_remove($album, $asset) {
    global $repository_path;
    ezmam_repository_path($repository_path);

    $asset_meta = ezmam_asset_metadata_get($album, $asset);
    if ($asset_meta["scheduled"]) {
        $cmd = "at -r " . $asset_meta["schedule_id"];
        system($cmd);

        $asset_meta["scheduled"] = false;
        unset($asset_meta["schedule_id"]);
        unset($asset_meta["schedule_date"]);
    }

    ezmam_asset_metadata_set($album, $asset, $asset_meta);
}

function view_submit_media() {
    global $dir_date_format;
    global $submit_upload_dir;
    global $intros;
    global $repository_path;
    global $default_add_title;
    global $titlings;
    global $default_downloadable;

    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';

    ezmam_repository_path($repository_path);

    $album_meta = ezmam_album_metadata_get($album . $visibility);

    // for preselection in the form
    $album_intro = $album_meta['intro'];
    if (isset($album_meta['add_title'])) {
        $add_title = $album_meta['add_title'];
    } else {
        $add_title = $default_add_title;
    }

    // for checkbox in the form
    $downloadable = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable);

    require_once template_getpath('popup_submit_media.php');
    die;
}

function view_edit_album() {
    global $intros;
    global $titlings;
    global $downloadable;
    global $repository_path;
    global $default_add_title;
    global $default_downloadable;

    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';

    ezmam_repository_path($repository_path);

    $album_meta = ezmam_album_metadata_get($album . $visibility);

    // for preselection in the form
    $album_intro = $album_meta['intro'];
    if (isset($album_meta['add_title'])) {
        $add_title = $album_meta['add_title'];
    } else {
        $add_title = $default_add_title;
    }

    // for the checkbox in the form
    $downloadable = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable);

    require_once template_getpath('popup_edit_album.php');

    die;
}

//
// Popup getters
//

/**
 * General popup dispatcher. Depending on the value of $input['popup'], it will show the right popup.
 * @global type $input 
 */
function view_popup() {
    global $input;

    if (!isset($input['popup'])) {
        echo 'Usage: web_index.php?action=show_popup&amp;popup=POPUP';
        die;
    }

    switch ($input['popup']) {
        case 'media_url':
            popup_media_url();
            break;

        case 'embed_code':
            popup_embed_code();
            break;

        case 'ezplayer_link':
            popup_ezplayer_link();
            break;

        case 'ulb_code':
            popup_ulb_code();
            break;

        default:
            error_print_message('view_popup: content of popup ' . $input['popup'] . ' not found');
            die;
    }
}

/**
 * Displays the popup with a link to the media
 */
function popup_media_url() {
    global $input;
    global $repository_path;
    global $ezmanager_url;

    ezmam_repository_path($repository_path);
    $media_url = get_link_to_media($input['album'], $input['asset'], $input['media']) . "&origin=link";
    $media_url_web = get_link_to_media($input['album'], $input['asset'], $input['media'], false) . "&origin=link";


    require_once template_getpath('popup_media_url.php');

    $url = $ezmanager_url;
    require_once template_getpath('popup_media_url.php');
}

/**
 * DIsplays the popup with the embed code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_embed_code() {
    global $input;
    global $repository_path;
    global $ezmanager_url;
    global $distribute_url;

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=embed_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    // Retrieving the info needed for the embed code and target link
    $metadata = ezmam_media_metadata_get($input['album'], $input['asset'], $input['media']);
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    if (!$token)
        $token = ezmam_album_token_get($input['album']);

    $media_infos = explode('_', $input['media']);
    $type = $media_infos[1];
    $quality = $media_infos[0];
    //compute iframe size according to media size
    $iframe_height = $metadata['height'] + 40;
    $iframe_width = $metadata['width'] + 30;
    // Embed code
    $link_target = $distribute_url . '?action=embed&amp;album=' . $input['album'] . '&amp;asset=' . $input['asset'] . '&amp;type=' . $type . '&amp;quality=' . $quality . '&amp;token=' . $token;
    $embed_code_web = '<iframe width="' . $iframe_width . '" height="' . $iframe_height . '" style="padding: 0;" frameborder="0" scrolling="no" src="' . $distribute_url . '?action=embed_link&album=' . $input['album'] . '&asset=' . $input['asset'] . '&type=' . $type . '&quality=' . $quality . '&token=' . $token . '&width=' . $metadata['width'] . '&height=' . $metadata['height'] . '&lang=' . get_lang() . '"><a href="' . $link_target . '">' . template_get_message('view_video', get_lang()) . '</a></iframe>';
    $embed_code = htmlentities($embed_code_web, ENT_COMPAT, 'UTF-8');

    // Displaying the popup
    require_once template_getpath('popup_embed_code.php');
}

/**
 * Displays the popup with the EZplayer link to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_ezplayer_link() {
    global $input;
    global $ezplayer_url;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];

    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset'])) {
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    $asset_meta = ezmam_asset_metadata_get($input['album'], $input['asset']);
    $action = (strtolower($asset_meta['origin']) === "streaming") ? 'view_asset_streaming' : 'view_asset_details';
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    $ezplayer_link = $ezplayer_url . '/index.php?'
            . 'action=' . $action
            . '&album=' . $album
            . '&asset=' . $asset
            . '&asset_token=' . $token;

    // Displaying the popup
    require_once template_getpath('popup_ezplayer_link.php');
}

/**
 * Displays the popup with the ulb code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_ulb_code() {
    global $input;
    global $repository_path;

    $asset_name = $input['asset'];

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=ulb_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    $ulb_code = get_code_to_media($input['album'], $input['asset'], $input['media']);

    // Displaying the popup
    require_once template_getpath('popup_ulb_code.php');
}

?>
