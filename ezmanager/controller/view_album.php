<?php


/**
 * Displays the album passed in GET or POST "album" parameter, if it exists and is managable by the user.
 */
function index($param = array())
{
    // Initialization: we retrieve various variable we'll need later on
    global $input;
    global $repository_path;
    global $ezmanager_url; // Website URL, defined in config.inc
    global $distribute_url;
    global $ezplayer_url;
    global $enable_moderator;
    global $enable_anon_access_control;
    global $trace_on;
    global $display_trace_stats;
    // global $ezmanager_url;

    if (isset($input['tokenmanager'])) {
        // add course to user in DB
    }
    
    if (isset($input['album'])) {
        $album = $input['album'];
    } else {
        $album = $_SESSION['podman_album'];
    }
    $current_album = $album;
    
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
    if (isset($metadata['id'])) {
        $album_id = $metadata['id'];
    } else {
        $album_id = $metadata['name'];
    }
    if (isset($metadata['course_code_public']) && $metadata['course_code_public'] != "") {
        $course_code_public = $metadata['course_code_public'];
    }
    $album_name = $metadata['name'];
    
    // TO DO : create variable to show displayed_course_code
    // $album_displayed_code = get_album_displayed_code($album);

    
    $title = choose_title_from_metadata($metadata);
    $public_album = album_is_public($album); // Whether the album is public; used to display the correct options
    $hd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=high&amp;token=' . ezmam_album_token_get($album);
    $sd_rss_url = $distribute_url . '?action=rss&amp;album=' . $album . '&amp;quality=low&amp;token=' . ezmam_album_token_get($album);
    $hd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=high&token=' . ezmam_album_token_get($album);
    $sd_rss_url_web = $distribute_url . '?action=rss&album=' . $album . '&quality=low&token=' . ezmam_album_token_get($album);
    $player_full_url = $ezplayer_url . "?action=view_album_assets&album=" . $album . "&token=" . ezmam_album_token_get($album);
    ezmam_album_token_manager_set($album);
    $assets = ezmam_asset_list_metadata($album_name_full);

    //
    // 3) We save the current album view in a session var
    //
    $_SESSION['podman_mode'] = 'view_album';
    $_SESSION['podman_album'] = $album;

    //
    // 4) Then we display the album
    //

    $current_tab = 'list';
    include template_getpath('div_album_header.php');
    include template_getpath('div_asset_list.php');
}
