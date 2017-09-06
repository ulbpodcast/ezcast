<?php

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
function index($param = array())
{
    index_asset_view($param);
}
 
function index_asset_view($param)
{
    global $input;
    global $repository_path;
    global $user_files_path;
    global $assets_list;
    global $album;
    global $error_path; // used to display an error on the main page
    global $login_error; // used to display error when anonymous user login

    global $cache_limit;

    $refresh_center = count($param == 0) || $param[0];
    
    // if reloaded is set, the whole page has to be refreshed
    if (isset($_SESSION['reloaded'])) {
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
        if ($input['click']) { // refresh a part of the page
            include_once template_getpath('error_album_not_found.php');
        } else { // refresh the whole page
            $error_path = template_getpath('error_album_not_found.php');
            include_once template_getpath('main.php');
        }
        log_append('warning', 'view_album_assets: tried to access non-existant album ' . $input['album']);
        exit;
    }

    // Authorization check
    if (!ezmam_album_token_check($album, $token)) {
        if ($input['click']) {
            include_once template_getpath('error_permission_denied.php');
        } else {
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
    $_SESSION['ezplayer_mode'] = 'view_album_assets'; // used in 'div_assets_center.php' and 'div_thread_details.php'
    $_SESSION['album'] = $album; // used in search
    $_SESSION['asset'] = '';
    $_SESSION['token'] = $token;

    // 3) Add current album to the album list
    
    $album_name = get_album_title($album);
    $album_title = ezmam_album_metadata_get($album);
    $course_code_public = "";
    if (isset($album_title['course_code_public'])) {
        $course_code_public = $album_title['course_code_public'];
    }
    
    $album_token = array(
            'title' => $album_name,
            'album' => $album,
            'course_code_public' => $course_code_public,
            'token' => $token
        );
    // checks if the album already exists in the token list
    // if it exists yet, checks if the title and the token have not changed
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
        ezplayer_acl_update_permissions_list();
    }

    // prepares official and personal bookmarks
    bookmarks_list_update(false, $official_bookmarks, $personal_bookmarks);

    // 4) prepares the threads to be displayed in the trending threads
    if (acl_display_threads()) {
        $threads = threads_select_by_album($album, $cache_limit);
        // removes the deleted threads or threads on deleted assets
        foreach ($threads as &$thread) {
            if (!thread_is_archive($thread['albumName'], $thread['assetName'])) {
                $threads_list[] = $thread;
            }
        }
    }
    
    if (array_key_exists('click', $input) && $input['click']) { // called by a local link
        // lvl, action, album, origin
        trace_append(array('2', 'view_album_assets', $album, 'from_ezplayer'));
        include_once template_getpath('div_assets_center.php');
    } else {// accessed by the UV or shared link
        // lvl, action, album, origin
        if (!array_key_exists('no_trace', $input) || !$input['no_trace']) {
            trace_append(array('2', 'view_album_assets', $album, 'from_external'));
        }
        include_once template_getpath('main.php');
    }
}
