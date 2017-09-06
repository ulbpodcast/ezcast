<?php

function index($param = array())
{
    global $input;
    global $repository_path;

    //
    // Access checks
    //
    if (!isset($input['album']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $input['album'] . ' without permission');
        die;
    }
    update_title($input['album'], $input['asset']);
}
