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
    $result = array();
    $data = array(
            'comment' => array(),
            'total_view' => array(),
            'unique_view' => array()
            );
    
    if(count($all_album_data) > 0) {
        $result['display'] = TRUE;
        
        foreach($all_album_data as $album_data) {
            // TODO remove: it's only for debug
    //        $strTime = strtotime("02-" . $album_data['month']);
    //        $data['comment'][strtotime("-4 month", $strTime) . "000"] = $album_data['total_comment']+2;
    //        $data['total_view'][strtotime("-4 month", $strTime) . "000"] = $album_data['total_view_total']-200;
    //        $data['unique_view'][strtotime("-4 month", $strTime) . "000"] = $album_data['total_view_unique']-10;
    //        
    //        $data['comment'][strtotime("-3 month", $strTime) . "000"] = $album_data['total_comment'];
    //        $data['total_view'][strtotime("-3 month", $strTime) . "000"] = $album_data['total_view_total']-100;
    //        $data['unique_view'][strtotime("-3 month", $strTime) . "000"] = $album_data['total_view_unique']-50;
    //        
    //        $data['comment'][strtotime("-2 month", $strTime) . "000"] = $album_data['total_comment']+10;
    //        $data['total_view'][strtotime("-2 month", $strTime) . "000"] = $album_data['total_view_total']-30;
    //        $data['unique_view'][strtotime("-2 month", $strTime) . "000"] = $album_data['total_view_unique']-120;
    //        
    //        $data['comment'][strtotime("-1 month", $strTime) . "000"] = $album_data['total_comment']+6;
    //        $data['total_view'][strtotime("-1 month", $strTime) . "000"] = $album_data['total_view_total']-6;
    //        $data['unique_view'][strtotime("-1 month", $strTime) . "000"] = $album_data['total_view_unique']-400;

            $time = strtotime("02-" . $album_data['month']) . "000";
            $data['comment'][$time] = $album_data['total_comment'];
            $data['total_view'][$time] = $album_data['total_view_total'];
            $data['unique_view'][$time] = $album_data['total_view_unique'];
        }

        $result['str_comment'] = convertPHPArrayToJSArray($data['comment']);
        $result['str_totalview'] = convertPHPArrayToJSArray($data['total_view']);
        $result['str_uniqueview'] = convertPHPArrayToJSArray($data['unique_view']);
        
    } else {
        $result['display'] = FALSE;
    }
    
    return $result;
}

function calcul_graph_video($all_video_data) {
    $result = array();
    if(count($all_video_data) > 0) {
        $result['display'] = TRUE;
        $result['str_all_asset'] = json_encode(array_column($all_video_data, 'asset'));
        $comment = array_map('intval', array_column($all_video_data, 'total_comment'));
        $result['str_total_comment'] = json_encode($comment);
        $result['str_total_view'] = json_encode(array_map('intval', array_column($all_video_data, 'total_view_total')));
        $result['str_unique_view'] = json_encode(array_map('intval', array_column($all_video_data, 'total_view_unique')));
    } else {
        $result['display'] = FALSE;
    }
    
    return $result;
}