<?php

/**
 * Moves an album token up and down (home page)
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;

    if (!isset($input['album']) || !isset($input['index']) ||
            !isset($input['up_down'])) {
        return false;
    }
    
    $album = $input['album'];
    $index = (int) $input['index'];
    $upDown = $input['up_down'];

    $new_index = ($upDown == 'up') ? $index - 1 : $index + 1;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    user_prefs_token_swap($_SESSION['user_login'], $index, $new_index);
    ezplayer_acl_update_permissions_list();
    log_append('moved_album_token', 'album token moved from ' . $index . ' to ' . $new_index);
    // lvl, action, album, index_src, index_dest
    trace_append(array('1', 'album_token_move', $album, $index, $new_index));

    albums_view(false);
}
