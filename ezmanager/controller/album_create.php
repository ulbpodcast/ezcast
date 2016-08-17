<?php

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 * @global type $input
 * @global type $ezmanager_url
 * @global type $repository_path
 * @global type $dir_date_format
 * @global type $default_intro 
 * @global type $default_credits
 */
function index($param = array()) {
    global $input;
    global $repository_path;
    global $dir_date_format;
    global $default_intro;
    global $default_add_title;
    global $default_downloadable;
    global $default_credits;
    //
    // Sanity checks
    //
    if (!isset($input['album']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'create_album: tried to access album ' . $input['album'] . ' without permission');
        die;
    }

    //
    // First of all, we have to set up the metada for the albums we're going to create
    //
    $not_created_albums = acl_authorized_albums_list_not_created(true);
    $description = $not_created_albums[$input['album']];
    $anac = get_anac(date('Y'), date('m'));
    $metadata = array(
        'name' => $input['album'],
        'description' => $description,
        'date' => date($dir_date_format),
        'anac' => $anac,
        'intro' => $default_intro,
        'credits' => $default_credits,
        'add_title' => $default_add_title,
        'downloadable' => $default_downloadable
    );

    //
    // All we have to do now is call ezmam twice to create both the private and public album
    // (remember that $input['album'] only contains the album's base name, /not/ the suffix
    //
    ezmam_repository_path($repository_path);
    $res = ezmam_album_new($input['album'] . '-priv', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    $res = ezmam_album_new($input['album'] . '-pub', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Don't forget to update the session variables!
    //
    acl_update_permissions_list();

    require_once template_getpath('popup_album_successfully_created.php');
}