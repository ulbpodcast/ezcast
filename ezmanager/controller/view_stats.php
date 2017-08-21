<?php


/**
 * Displays the stast informations
 */
function index($param = array()) {
    global $input;
    global $repository_path;
    global $distribute_url;
    global $ezplayer_url;
    global $enable_moderator;
    global $enable_anon_access_control;
    global $trace_on;
    
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
    
    if(isset($metadata['id'])) {
        $album_id = $metadata['id'];
    } else {
        $album_id = $metadata['name'];
    }
    
    $album_name_full = $album; // complete album name, used for div identification
    $album_name = suffix_remove($album); // "user-friendly" album name, used for display
    $title = choose_title_from_metadata($metadata);
    $public_album = album_is_public($album); // Whether the album is public; used to display the correct options
    
    $stats = load_stats($album);
    
    $current_tab = 'stats';
    include template_getpath('div_album_header.php');
    include template_getpath('div_stats_descriptives.php');
}

function load_stats($album) {
    require_once dirname(__FILE__) . '/../lib_sql_stats.php';
    
    $stats = array();
    $stats['album'] = db_stats_album_get_month_comment($album);
    $stats['video'] = db_stats_video_get_month_comment($album);
    $stats['descriptive'] = array(
        'bookmark_personal' => 0, 
        'bookmark_official' => 0, 
        'access' => 0);
    $album_infos = db_stats_album_infos_get($album);
    if(count($album_infos) > 0) {
        $stats['descriptive'] = $album_infos[0];
    }
    $stats['descriptive']['threads'] = db_stats_album_threads_get($album);
    return $stats;
}