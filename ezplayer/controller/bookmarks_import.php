<?php

/**
 * Imports all selected bookmarks to the selected album
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function index($param = array())
{
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
    trace_append(array($input['source'] == 'assets' ? '2' : '3', 'bookmarks_import', $album,
        $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-', $target, count($selection), count($imported_bookmarks)));
    
    // determines the page to display
    if ($input['source'] == 'assets') {
        // the token is needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
    }
    bookmarks_list_update();
}
