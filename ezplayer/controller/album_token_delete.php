<?php

/**
 * Deletes a token from 'div_main_center.php'
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;
    
    if (!isset($input['album'])) {
        return false;
    }
    $album = $input['album'];

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    user_prefs_token_remove($_SESSION['user_login'], $album);
    user_prefs_album_bookmarks_delete_all($_SESSION['user_login'], $album);
    ezplayer_acl_update_permissions_list();
    log_append('delete_album_token', 'album token removed : album -' . $album);
    // lvl, action, album
    trace_append(array('1', 'album_token_delete', $album));

    albums_view(false);
}
