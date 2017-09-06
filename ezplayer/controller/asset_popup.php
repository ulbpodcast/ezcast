<?php

/**
 * Returns the content to put in a share popup
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $ezplayer_url;

    $album = $input['album'];
    $asset = $input['asset'];
    $current_time = $input['time'];
    $type = $input['type'];
    $display = $input['display'];

    ezmam_repository_path($repository_path);

    $asset_meta = ezmam_asset_metadata_get($album, $asset);

    switch ($display) {
        case 'share_time':
            $share_time = $ezplayer_url . '/index.php?action=view_asset_bookmark'
                    . '&album=' . $album
                    . '&asset=' . $asset
                    . '&t=' . $current_time
                    . '&type=' . $type;
            include_once template_getpath('popup_asset_timecode_share.php');
            break;
        case 'share_link':
            if ($type == 'cam') {
                $asset_meta['high_src'] = get_link_to_media($album, $asset, 'high_cam') . '&origin=link';
                $asset_meta['low_src'] = get_link_to_media($album, $asset, 'low_cam') . '&origin=link';
            } else {
                $asset_meta['high_src'] = get_link_to_media($album, $asset, 'high_slide') . '&origin=link';
                $asset_meta['low_src'] = get_link_to_media($album, $asset, 'low_slide') . '&origin=link';
            }
            include_once template_getpath('popup_asset_download.php');
            break;
    }
}
