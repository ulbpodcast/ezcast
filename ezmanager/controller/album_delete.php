<?php


function index($param = array())
{
    global $input;
    global $repository_path;

    //
    // Access checks
    //
    //$input['album'] is actually course code (or id?) here.
    if (!isset($input['album']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    //
    // The only important thing to do right now is delete both the public and private albums,
    // by calling ezmam
    //
    ezmam_repository_path($repository_path);

    // Deletes the table of contents (EZcast Player)
    toc_album_bookmarks_delete_all($input['album'] . '-priv');

    $res = ezmam_album_delete($input['album'] . '-priv');
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    toc_album_bookmarks_delete_all($input['album'] . '-pub');

    $res = ezmam_album_delete($input['album'] . '-pub');
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Don't forget to update the albums list
    //
    acl_update_permissions_list();
    unset($_SESSION['podman_album']);

    //
    // Finally, we display a nice confirmation message to the user
    //
    
    require_once template_getpath('popup_album_successfully_deleted.php');
}
