<?php

/**
 * ezcast EZmanager main program (MVC Controller)
 * @package ezcast.ezmanager.main
 */
require_once 'config.inc';
session_name($appname);
session_start();
require_once 'lib_acl.php';
require_once __DIR__.'/../commons/lib_error.php';
require_once 'lib_ezmam.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_various.php';
require_once '../commons/lib_template.php';
require_once 'lib_various.php';
require_once 'lib_upload.php';
require_once 'lib_toc.php';
$input = array_merge($_GET, $_POST);
require_once '../commons/lib_sql_management.php';

if (isset($input['lang'])) {
    set_lang($input['lang']);
}

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

//
// Login/logout
//
// If we're not logged in, we try to log in or display the login form
if (!user_logged_in()) {
    if (isset($input['action']) && $input['action'] == 'view_help') {
        requireController('view_help.php');
        index();
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
    //if not connected and the user click on a link to partage album management -> put arg in session variable to add it when he is connected
    elseif (isset($input['action']) && $input['action'] == 'add_moderator' && isset($input['album']) && isset($input['tokenmanager'])) {
        $_SESSION['add_moderator'] = 'true';
        $_SESSION['add_moderator_album'] = $input['album'];
        $_SESSION['add_moderator_token'] = $input['tokenmanager'];
        view_login_form();
    }

    // This is a tricky case:
    // If we do not have a session, but we have an action, that means we lost the
    // session somehow and are trying to load part of a page through AJAX call.
    // We do not want the login page to be displayed randomly inside a div,
    // so we refresh the whole page to get a full-page login form.
    elseif (isset($input['action']) && $input['action'] != 'login' && $input['action'] != 'logout') {
        refresh_page();
    }
    // Step 1: Displaying the login form
    // (happens if no "action" is provided)
    else {
        //  view_login_form();
        if (isset($_GET["sso"])) {
            if (in_array("sso", $auth_methods)) {
                user_login(trim('login'), trim('passwd'));
            }
        } else {
            view_login_form();
        }
    }
}

// At this point of the code, the user is supposed to be logged in.
// We check whether they specified an action to perform. If not, it means they landed
// here through a page reload, so we check the session variables to restore the page as it was.
elseif (((isset($_SESSION['podman_logged']) && (!isset($input['action']) || empty($input['action']))) &&
        (!isset($_SESSION['add_moderator']) || $_SESSION['add_moderator'] != 'true')) ||
        ((isset($_SESSION['podman_logged']) && (!isset($input['action']) || empty($input['action']))) &&
                isset($_SESSION['add_moderator']) && $_SESSION['add_moderator'] != 'true')
        ) {
    redraw_page();
}

// At this point of the code, the user is logged in and explicitly specified an action.
// We perform the action specified.
else {
    if (isset($_SESSION['add_moderator']) && $_SESSION['add_moderator'] == 'true') {
        $input['action'] = 'add_moderator';
        $input['album'] = $_SESSION['add_moderator_album'];
        $input['tokenmanager'] = $_SESSION['add_moderator_token'];
        $_SESSION['add_moderator'] = 'false';
    }


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
        // The user clicked on an album, we display its content to them

        case 'view_album':
            requireController('view_album.php');
            break;

        // The user clicked on an asset, we display its details to them
        case 'view_asset_details':
            requireController('asset_view.php');
            break;

        // Display the help page
        case 'view_help':
            requireController('view_help.php');
            break;

        // Display the update page
        case 'view_update':
            requireController('view_update.php');
            break;

        // The user selected an album to create. We now create the two albums (-pub and -priv) they want
        case 'create_album':
        case 'create_courseAndAlbum':
            $service = true;
            requireController('album_create.php');
            break;

        // The user chose to delete an album.
        case 'delete_album':
            $service = true;
            requireController('album_delete.php');
            break;

        // reset rss token
        case 'reset_rss':
            $service = true;
            requireController('reset_rss.php');
            break;

        case 'view_stats':
            requireController('view_stats.php');
            break;

        case 'view_ezplayer_link':
            requireController('view_ezplayer_link.php');
            break;

        case 'view_ezmanager_link':
            requireController('view_ezmanager_link.php');
            break;

        //The users wants to upload an asset into the current album, show lets show him the upload form
        case 'submit_media_progress_bar':
            $service = true;
            requireController('submit_media_progress_bar.php');
            break;

        case 'view_submit_media':
            requireController('view_submit_media.php');
            break;

        case 'view_edit_album':
            requireController('view_edit_album.php');
            break;

        case 'view_list_moderator':
            requireController('moderator_management.php');
            break;

        // users has filled in the edit album form and has confirmed
        case 'edit_album':
            $service = true;
            requireController('album_edit.php');
            break;

        //user has filled in the upload form, we need to handle the data.
        case 'submit_media':
            requireController('submit_media.php');
            break;

        // Called by APC plugin to get info on current upload
        case 'get_upload_progress':
            $service = true;
            requireController('get_upload_progress.php');
            break;

        case 'upload_init':
            $service = true;
            requireController('upload_init.php');
            break;

        case 'upload_chunk':
            $service = true;
            requireController('upload_chunk.php');
            break;

        case 'upload_finished':
            $service = true;
            requireController('upload_finished.php');
            break;

        case 'upload_error':
            $service = true;
            requireController('upload_error.php');
            break;

        case 'edit_asset':
            requireController('asset_edit.php');
            break;

        case 'asset_downloadable_set':
            $service = true;
            requireController('asset_downloadable_set.php');
            break;
        case 'postedit_asset':
            $service = true;
            requireController('asset_postedit.php');
            break;
        case 'delete_asset':
            $service = true;
            requireController('asset_delete.php');
            break;


        case 'move_asset':
            $service = true;
            requireController('asset_move.php');
            break;

        case 'copy_asset':
            $service = true;
            requireController('asset_copy.php');
            break;

        //move asset from album -priv to -pub
        case 'publish_asset':
            $service = true;
            $paramController[] = 'publish';
            requireController('asset_publish_unpublish.php');
            break;

        //move asset from album -pub to -priv
        case 'unpublish_asset':
            $service = true;
            $paramController[] = 'unpublish';
            requireController('asset_publish_unpublish.php');
            break;

        //schedule publication / archiving of asset from album -pub to -priv
        case 'schedule_asset':
            $service = true;
            requireController('asset_schedule.php');
            break;

        //cancel the scheduling
        case 'cancel_schedule_asset':
            $service = true;
            requireController('asset_schedule_cancel.php');
            break;

        // Returning the content to display in a popup
        case 'show_popup':
            requireController('view_popup.php');
            break;

        // In case we want to log out
        case 'logout':
            requireController('user_logout.php');
            break;

        // The only case when we could possibly arrive here with a session created
        // and a "login" action is when the user refreshed the page. In that case,
        // we redraw the page with the last information saved in the session variables.
        case 'login':
            redraw_page();
            break;

        case 'add_moderator':
            requireController('album_add_moderator.php');
            // redraw_page();
            break;

        case 'regen_title':
            $service = true;
            requireController('asset_title_regen.php');
            break;

        case 'delete_user_course':
            requireController('moderator_delete.php');
            break;

        case 'album_stats_reset':
            requireController('album_stats_reset.php');
            break;

        //debugging should be removed in prod
        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            albums_view();
    }

    // Call the function to view the page
    index($paramController);
}

//
// Helper functions
//

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in()
{
    return isset($_SESSION['podman_logged']);
}

//
// Display functions
//

/**
 * Displays the login form
 */
function view_login_form()
{
    global $lang;
    global $ezmanager_url;
    global $error, $input;
    global $auth_methods;

    //check if we receive a no_flash parameter (to disable flash progressbar on upload)
    if (isset($input['no_flash'])) {
        $_SESSION['has_flash'] = false;
    }
    $url = $ezmanager_url;
    // template include goes here
    $sso_enabled = in_array("sso", $auth_methods);
    include_once template_getpath('login.php');
}

/**
 * Displays the main frame, without anything on the right side
 */
function albums_view()
{
    global $url;
    // Used in redraw mode only
    global $enable_moderator;
    global $album_name;
    global $album_id;
    global $course_code_public;
    global $album_name_full;
    global $title;
    global $public_album;
    global $assets;
    global $hd_rss_url;
    global $sd_rss_url;
    global $hd_rss_url_web;
    global $sd_rss_url_web;
    global $player_full_url;
    global $manager_full_url;
    global $head_code; // Optional code we want to append in the HTML header
    // List of all the albums a user has created
    $created_albums = acl_authorized_albums_list_created(); // Used to display the albums list
    $allowed_albums = acl_authorized_albums_list();

    $_SESSION['podman_mode'] = 'view_main';

    global $album;
    include_once template_getpath('main.php');
    //include_once "tmpl/fr/main.php";
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
    global $album_id;
    global $course_code_public;
    global $album_name_full;
    global $title;
    global $public_album;
    global $assets;
    global $hd_rss_url;
    global $sd_rss_url;
    global $hd_rss_url_web;
    global $sd_rss_url_web;
    global $player_full_url;
    global $manager_full_url;
    global $distribute_url;
    global $ezplayer_url;
    global $ezmanager_url;
    ezmam_repository_path($repository_path);

    $action = $_SESSION['podman_mode'];
    $redraw = true;
    if (isset($_SESSION['podman_album'])) {
        $current_album = $_SESSION['podman_album'];
        $current_album_is_public = album_is_public($_SESSION['podman_album']);

        $album_name = suffix_remove($_SESSION['podman_album']);
        $album_name_full = $_SESSION['podman_album'];
        $metadata = ezmam_album_metadata_get($_SESSION['podman_album']);
        $title = choose_title_from_metadata($metadata);

        if (isset($metadata['id'])) {
            $album_id = $metadata['id'];
        } else {
            $album_id = $metadata['name'];
        }

        if (isset($metadata['course_code_public']) && $metadata['course_code_public']!="") {
            $course_code_public = $metadata['course_code_public'];
        }
        $public_album = $current_album_is_public;
        $assets = ezmam_asset_list_metadata($_SESSION['podman_album']);
        $hd_rss_url = $distribute_url . '?action=rss&amp;album=' . $current_album . '&amp;quality=high&amp;token=' .
                ezmam_album_token_get($album_name_full);
        $sd_rss_url = $distribute_url . '?action=rss&amp;album=' . $current_album . '&amp;quality=low&amp;token=' .
                ezmam_album_token_get($album_name_full);
        $hd_rss_url_web = $distribute_url . '?action=rss&album=' . $current_album . '&quality=high&token=' .
                ezmam_album_token_get($album_name_full);
        $sd_rss_url_web = $distribute_url . '?action=rss&album=' . $current_album . '&quality=low&token=' .
                ezmam_album_token_get($album_name_full);
        $player_full_url = $ezplayer_url . "?action=view_album_assets&album=" . $current_album . "&token=" .
                ezmam_album_token_get($album_name_full);
        ezmam_album_token_manager_set($current_album);
        $manager_full_url = $ezmanager_url . "?action=add_moderator&album=" . $current_album . "&tokenmanager=" .
        ezmam_album_token_manager_get($album_name_full);
    }

    // Whatever happens, the first thing to do is display the whole page.
    albums_view();
}

/**
 * Reloads the whole page
 */
function refresh_page()
{
    global $ezmanager_url;
    // reload the page
    echo '<script>window.location.reload();</script>';
    die;
}


//
// "Business logic" functions
//

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
        if ($input['has_flash'] == 'N') {
            $_SESSION['has_flash'] = false;
        } else {
            $_SESSION['has_flash'] = true;
        }
    }
    // 2) Initializing the ACLs
    acl_init($login);

    // 3) Setting correct language
    set_lang($input['lang']);
    if (count(acl_authorized_albums_list()) == 0 && (!isset($res['ismanager']) || $res['ismanager']!='true')) {
        // if (count(acl_authorized_albums_list()) == 0) {
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



function private_asset_schedule_remove($album, $asset)
{
    global $repository_path;
    ezmam_repository_path($repository_path);

    $asset_meta = ezmam_asset_metadata_get($album, $asset);
    if (array_key_exists("scheduled", $asset_meta) && $asset_meta["scheduled"]) {
        $cmd = "at -r " . $asset_meta["schedule_id"];
        system($cmd);

        $asset_meta["scheduled"] = false;
        unset($asset_meta["schedule_id"]);
        unset($asset_meta["schedule_date"]);
    }

    ezmam_asset_metadata_set($album, $asset, $asset_meta);
}
