<?php

/**
 * Effectively deletes an asset from the repository, and displays a nice message to the user
 */
function index($param = array()) {
    global $input;
    global $repository_path;

    //
    // Sanity checks
    //
    ezmam_repository_path($repository_path);

    if (!isset($input['album']) || !isset($input['asset'])) {
        echo "Usage: web_index.php?action=delete_asset&album=ALBUM&asset=ASSET";
        die;
    }

    if (!acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'delete_asset: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    if (!ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(template_get_message('Non-existant_asset', get_lang()));
        log_append('warning', 'delete_asset: tried to access asset ' . $input['asset'] . ' of album ' . $input['album'] . ' which does not exist');
        die;
    }

    // firstly, remove the at job for scheduled move
    private_asset_schedule_remove($input['album'], $input['asset']);

    // We remove the bookmarks list from the table of contents (EZcast Player)
    toc_asset_bookmarks_delete_all($input['album'], $input['asset']);
    //
    // Now we simply use lib_ezmam to delete the asset from the repository
    //
    ezmam_asset_delete($input['album'], $input['asset']);

    //
    // And that's all, we just have to print a confirmation message next!
    //
    
    //require_once template_getpath('popup_asset_successfully_deleted.php');
    //view_album();
}