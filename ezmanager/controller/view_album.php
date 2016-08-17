<?php


/**
 * Displays the album passed in GET or POST "album" parameter, if it exists and is managable by the user.
 */
function index($param = array()) {
    // Initialization: we retrieve various variable we'll need later on
    global $input;
    global $repository_path;
    global $ezmanager_url; // Website URL, defined in config.inc
    global $distribute_url;
    global $ezplayer_url;

    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['podman_album'];
    ezmam_repository_path($repository_path);
    //
    // 0) Permissions checks
    //
    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', "view_album: tried to access album " . $album . ' without permission');
        die;
    }

    //
    // 1) We retrieve the metadata relating to the album
    //
    $metadata = ezmam_album_metadata_get($album);

    //
    // 2) We set the variables used in the template with the correct values
    //
    $album_name_full = $album; // complete album name, used for div identification
    $album_name = suffix_remove($album); // "user-friendly" album name, used for display
    $description = $metadata['description'];
    $public_album = album_is_public($album); // Whether the album is public; used to display the correct options
    $hd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=high&amp;token=' . ezmam_album_token_get($album);
    $sd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=low&amp;token=' . ezmam_album_token_get($album);
    $hd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=high&token=' . ezmam_album_token_get($album);
    $sd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=low&token=' . ezmam_album_token_get($album);
    $player_full_url = $ezplayer_url . "?action=view_album_assets&album=" . $album . "&token=" . ezmam_album_token_get($album);
    $assets = ezmam_asset_list_metadata($album_name_full);

    //
    // 3) We save the current album view in a session var
    //
    $_SESSION['podman_mode'] = 'view_album';
    $_SESSION['podman_album'] = $album;

    //
    // 4) Then we display the album
    //

    include template_getpath('div_album_header.php');
    include template_getpath('div_asset_list.php');
}

