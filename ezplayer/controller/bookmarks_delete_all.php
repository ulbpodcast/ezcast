<?php

/**
 * Deletes all bookmarks of the given asset
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function index($param = array())
{
    global $input;
    global $user_files_path;
    global $repository_path;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $album = $input['album'];
    $asset = $input['asset'];

    $bookmarks = user_prefs_asset_bookmarks_delete($_SESSION['user_login'], $album, $asset);

    // lvl, action, album, asset
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'asset_bookmarks_delete', $album, $asset));
    log_append('remove_asset_bookmarks: all bookmarks deleted from the asset ' . $asset . ' in the album ' . $album);

    // album token needed to display the album assets
    $input['token'] = ezmam_album_token_get($album);
    $input['click'] = true;
    
    requireController('album_assets_view.php');
    index_asset_view(array(true));
}
