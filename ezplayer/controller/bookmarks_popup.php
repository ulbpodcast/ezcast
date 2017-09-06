<?php

/**
 * Returns a bookmarks list to display in a popup (export_bookmarks / delete_bookmarks)
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;

    $album = trim($input['album']);
    $asset = trim($input['asset']);
    $tab = $input['tab'];
    $source = $input['source'];

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if (isset($asset) && $asset != '') {
        $asset_meta = ezmam_asset_metadata_get($album, $asset);
        if ($tab == 'custom') {
            $bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
        } else {
            $bookmarks = toc_asset_bookmark_list_get($album, $asset);
        }
    } else {
        if ($tab == 'custom') {
            $bookmarks = user_prefs_album_bookmarks_list_get($_SESSION['user_login'], $album);
        } else {
            $bookmarks = toc_album_bookmarks_list_get($album);
        }
    }

    switch ($input['display']) {
        case 'delete':
            include_once template_getpath('popup_bookmarks_delete.php');
            break;
        case 'export':
            include_once template_getpath('popup_bookmarks_export.php');
            break;
    }
}
