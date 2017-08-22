<?php


/**
 * Displays the stast informations
 */
function index($param = array()) {
    global $input;
    global $repository_path;
    global $distribute_url;
    global $ezmanager_url; // Website URL, defined in config.inc
    global $ezplayer_url;
    global $enable_moderator;
    global $enable_anon_access_control;
    
    if (isset($input['album']))
        $album = $input['album'];
    else
        $album = $_SESSION['podman_album'];
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
    
    if(isset($metadata['id'])) {
        $album_id = $metadata['id'];
    } else {
        $album_id = $metadata['name'];
    }
    if(isset($metadata['course_code_public']) && $metadata['course_code_public'] != "") {
        $course_code_public = $metadata['course_code_public'];
    }
    
    $album_name_full = $album; // complete album name, used for div identification
    $album_name = suffix_remove($album); // "user-friendly" album name, used for display
    $title = choose_title_from_metadata($metadata);
    $public_album = album_is_public($album); // Whether the album is public; used to display the correct options
    $manager_full_url = $ezmanager_url . "?action=add_moderator&album=" . $album . "&tokenmanager=" . ezmam_album_token_manager_get($album);
    
    $current_tab = 'ezmanager';
    include template_getpath('div_album_header.php');
    include template_getpath('div_ezmanager_link.php');
}