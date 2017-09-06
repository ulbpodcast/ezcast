<?php

/**
 * Cancel the scheduling of an asset
 */
function index($param = array())
{
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
    private_asset_schedule_remove($input['album'], $input['asset']);
    redraw_page();
    //require_once template_getpath('popup_asset_successfully_deleted.php');
    //view_album();
}
