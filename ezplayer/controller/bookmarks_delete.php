<?php

/**
 * Deletes a selection of bookmarks
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function index($param = array())
{
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


    if ($target == 'official') {
        if (acl_has_album_moderation($album)) {
            $bookmarks = toc_asset_bookmarks_selection_get($album, $asset, $selection);
            toc_album_bookmarks_delete($bookmarks);
        }
    } else {
        $bookmarks = user_prefs_asset_bookmarks_selection_get($_SESSION['user_login'], $album, $asset, $selection);
        user_prefs_album_bookmarks_delete($_SESSION['user_login'], $bookmarks);
    }

    log_append('delete_bookmarks: ' . count($selection) . ' bookmarks deleted from the album ' . $album);
    // lvl, action, album, asset, target (from official|personal), number of deleted bookmarks
    trace_append(array($_SESSION['asset'] == '' ? '2' : '3', 'bookmarks_delete', $album,
        $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-', $target == '' ? 'custom' : $target, count($selection)));
    if ($input['source'] == 'assets') {
        // album token needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
        bookmarks_list_update();
    } else {
        bookmarks_list_update();
    }
}
