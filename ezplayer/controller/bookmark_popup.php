<?php

/**
 * Return a specific bookmark to display in a popup (delete_bookmark / copy_bookmark)
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
    $tab = $input['tab'];
    $source = $input['source'];

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($tab == 'custom') {
        $bookmark = user_prefs_asset_bookmark_get($_SESSION['user_login'], $bookmark_album, $bookmark_asset, $bookmark_timecode);
    } else { // removes from table of contents
        $bookmark = toc_asset_bookmark_get($bookmark_album, $bookmark_asset, $bookmark_timecode);
    }
    
    switch ($input['display']) {
        case 'remove':
            include_once template_getpath('popup_bookmark_delete.php');
            break;
        case 'copy':
            include_once template_getpath('popup_bookmark_copy.php');
            break;
    }
}
