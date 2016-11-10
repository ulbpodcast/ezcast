<?php

/**
 * Reloads the streaming player
 * @param type $display
 */
function index($param = array()) {
    asset_streaming_player($param);
}
    
function asset_streaming_player($param = array()) {
    global $input;
    global $asset_meta;
    global $m3u8_live_stream;
    global $streaming_video_player;
    global $repository_path;
    global $is_android;
    
    $display = (count($param) == 0 || $param[0]);

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    ezmam_repository_path($repository_path);

    // gets metadata for the selected asset
    $asset_meta = ezmam_asset_metadata_get($album, $asset);
    $asset_token = ezmam_asset_token_get($album, $asset);

    if ($asset_meta['record_type'] == 'camslide') {
        $type = (isset($input['type']) && $input['type'] != '') ? $input['type'] : 'cam';
        $m3u8_live_stream = 'videos/' . suffix_remove($album) . '/' . $asset_meta['stream_name'] . '_' . $asset_token . '/' . $type . '/live.m3u8';
        $m3u8_slide = 'videos/' . suffix_remove($album) . '/' . $asset_meta['stream_name'] . '_' . $asset_token . '/slide/live.m3u8';
    } else {
        $m3u8_live_stream = 'videos/' . suffix_remove($album) . '/' . $asset_meta['stream_name'] . '_' . $asset_token . '/' . $asset_meta['record_type'] . '/live.m3u8';
    }

    $_SESSION['current_type'] = ($asset_meta['record_type'] == 'camslide') ? $type : $asset_meta['record_type'];

    if ($display) { // the whole page must be displayed        
        if ($is_android) {
            include_once template_getpath("div_streaming_player_android.php");
        } else {
            include_once template_getpath("div_streaming_player_$streaming_video_player.php");
        }
        return true;
    } else {
        return $asset_meta;
    }
}