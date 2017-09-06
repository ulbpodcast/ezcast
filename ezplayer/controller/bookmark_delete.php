<?php

/**
 * Removes an asset bookmark from the user's bookmarks list
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
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
        bookmarks_list_update();
    } else {
        bookmarks_list_update();
    }
}
