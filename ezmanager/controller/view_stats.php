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

function convertPHPArrayToJSArray($data) {
    return json_encode(
                array_map(
                  function($key, $value) { return array($key, intval($value)); },
                  array_keys($data),
                  array_values($data)
                )
              );
}

function load_stats($album) {
    require_once dirname(__FILE__) . '/../lib_sql_stats.php';
    
    $stats = array();
    $all_album_data = db_stats_album_get_month_comment($album);
    $all_video_data = db_stats_video_get_month_comment($album);
    $stats['descriptive'] = array(
        'bookmark_personal' => 0, 
        'bookmark_official' => 0, 
        'access' => 0);
    $album_infos = db_stats_album_infos_get($album);
    if(count($album_infos) > 0) {
        $stats['descriptive'] = $album_infos[0];
    }
    $stats['descriptive']['threads'] = db_stats_album_threads_get($album);
    
    $stats['graph']['album'] = calcul_graph_album($all_album_data);
    $stats['graph']['video'] = calcul_graph_video($all_video_data);
    
    return $stats;
}

function calcul_graph_album($all_album_data) {
    $data = array(
            'comment' => array(),
            'total_view' => array(),
            'unique_view' => array()
            );
    
    foreach($all_album_data as $album_data) {
        $time = strtotime("02-" . $album_data['month']) . "000";
        $data['comment'][$time] = $album_data['total_comment'];
        $data['total_view'][$time] = $album_data['total_view_total'];
        $data['unique_view'][$time] = $album_data['total_view_unique'];
    }
    
    
    
    $result = array();
    $result['str_comment'] = convertPHPArrayToJSArray($data['comment']);
    $result['str_totalview'] = convertPHPArrayToJSArray($data['total_view']);
    $result['str_uniqueview'] = convertPHPArrayToJSArray($data['unique_view']);
    return $result;
}

function calcul_graph_video($all_video_data) {
    $result = array();
    $result['str_all_asset'] = json_encode(array_column($all_video_data, 'asset'));
    $result['str_total_comment'] = json_encode(array_map('intval', array_column($all_video_data, 'total_comment')));
    $result['str_total_view'] = json_encode(array_map('intval', array_column($all_video_data, 'total_view_total')));
    $result['str_unique_view'] = json_encode(array_map('intval', array_column($all_video_data, 'total_view_unique')));
    return $result;
}