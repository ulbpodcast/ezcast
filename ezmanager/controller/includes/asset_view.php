<?php

//display asset details
function asset_view()
{
    global $input;
    global $repository_path;
    global $ezmanager_url;
    global $trace_on;
    global $display_trace_stats;
    

    // Setting up various variables we'll need later
    if (isset($input['album'])) {
        $album = $input['album'];
    } else {
        $album = $_SESSION['album'];
    }

    if (isset($input['asset'])) {
        $asset = $input['asset'];
    } else {
        $asset = $_SESSION['asset'];
    }

    ezmam_repository_path($repository_path);

    //
    // 0) Sanity checks
    //
    if (!isset($album) || !ezmam_album_exists($album)) {
        error_print_message(template_get_message('Non-existant_album', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $album . ' which does not exist');
        die;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        error_print_message(template_get_message('Non-existant_asset', get_lang()));
        log_append('warning', 'view_asset_details: tried to access asset ' . $asset . ' of album ' . $album . ' which does not exist');
        die;
    }

    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'view_asset_details: tried to access album ' . $album . ' without permission');
        die;
    }

    //
    // 1) We retrieve the metadata for the asset and its media
    //
    $asset_metadata = ezmam_asset_metadata_get($album, $asset);
    $media_metadata = ezmam_media_list_metadata_assoc($album, $asset);

    //
    // 2) Now we can set up the variables used in the template
    //
    $asset_name = $asset; // "Technical" asset name
    $author = $asset_metadata['author']; // Asset author
    $title = $asset_metadata['title']; // "user-friendly" asset name (title)
    $description = $asset_metadata['description']; // Asset description
    $date = get_user_friendly_date($asset_metadata['record_date'], ' ', true, get_lang(), true); // Recording date, i.e. asset creation date
    $origin = (isset($asset_metadata) ? $asset_metadata['origin'] : '');
    $status = (isset($asset_metadata['status']) ? $asset_metadata['status'] : '');
    $public_album = album_is_public($album); // Whether the album the asset is public or not
    $has_cam = (strpos($asset_metadata['record_type'], 'cam') !== false); // Whether or not the asset has a "live-action" video
    $has_slides = (strpos($asset_metadata['record_type'], 'slide') !== false); // Whether or not the asset has slides
    $created_albums_list_with_descriptions = acl_authorized_albums_list_created(true); // List of all the created albums (used for asset move)
    $asset_token = ezmam_asset_token_get($album, $asset); // Asset token, used for embedded media player (preview)
    $asset_scheduled = isset($asset_metadata['scheduled']) ? $asset_metadata['scheduled'] : false;
    $asset_sched_date = isset($asset_metadata['schedule_date']) ? $asset_metadata['schedule_date'] : false;
    $asset_sched_id = isset($asset_metadata['schedule_id']) ? $asset_metadata['schedule_id'] : false;
    // Filling in the data about the media
    // all you want to know about high res camera video
    if (isset($media_metadata['high_cam'])) {
        $filesize_cam['HD'] = (isset($media_metadata['high_cam']['file_size']) ?
                                        $media_metadata['high_cam']['file_size'] : '');
        $dimensions_cam['HD'] = $media_metadata['high_cam']['width'] . ' x ' . $media_metadata['high_cam']['height'];
        //not used // $format_cam = $media_metadata['high_cam']['videocodec'];
    }

    // Everything about the low-res version of the camera video
    if (isset($media_metadata['low_cam'])) {
        $filesize_cam['SD'] = (isset($media_metadata['low_cam']['file_size']) ?
                                        $media_metadata['low_cam']['file_size'] : '');
        $dimensions_cam['SD'] = $media_metadata['low_cam']['width'] . ' x ' . $media_metadata['low_cam']['height'];
    }

    // Everything about the high-res slides video
    if (isset($media_metadata['high_slide'])) {
        $filesize_slides['HD'] = (isset($media_metadata['high_slide']['file_size']) ?
                                        $media_metadata['high_slide']['file_size'] : '');
        $dimensions_slides['HD'] = $media_metadata['high_slide']['width'] . ' x ' . $media_metadata['high_slide']['height'];
        //not used// $format_slides = $media_metadata['high_slides']['videocodec'];
    }

    // Everything about the low-res slides
    if (isset($media_metadata['low_slide'])) {
        $filesize_slides['SD'] = (isset($media_metadata['low_slide']['file_size']) ?
                                        $media_metadata['low_slide']['file_size'] : '');
        $dimensions_slides['SD'] = $media_metadata['low_slide']['width'] . ' x ' . $media_metadata['low_slide']['height'];
    }

    // To get the duration, we use the high cam media if it exists, or the high_slide
    // media otherwise. We assume at least one of these exists.
    if (isset($media_metadata['high_cam'])) {
        $duration = $media_metadata['high_cam']['duration'];
    } elseif (isset($media_metadata['high_slide'])) {
        $duration = $media_metadata['high_slide']['duration'];
    }
    
    if (!isset($duration) || empty($duration)) {
        $duration = "Error getting duration";
    } else {
        $duration = get_user_friendly_duration($duration);
    }
    
    $record_type = $asset_metadata['record_type'];

    // Finally, we set up the URLs and view counts to the different media
    if ($has_cam) {
        $url_cam['HD'] = ezmam_media_geturl($album, $asset, 'high_cam');
        $url_cam['SD'] = ezmam_media_geturl($album, $asset, 'low_cam');
        // specific code that contains information about the media
        $code_cam['HD'] = get_code_to_media($album, $asset, 'high_cam');
        $code_cam['SD'] = get_code_to_media($album, $asset, 'low_cam');
        $view_count_cam = ezmam_media_viewcount_get($album, $asset, 'high_cam') + ezmam_media_viewcount_get($album, $asset, 'low_cam');
    }
    if ($has_slides) {
        $url_slides['HD'] = ezmam_media_geturl($album, $asset, 'high_slide');
        $url_slides['SD'] = ezmam_media_geturl($album, $asset, 'low_slide');
        $code_slide['HD'] = get_code_to_media($album, $asset, 'high_slide');
        $code_slide['SD'] = get_code_to_media($album, $asset, 'low_slide');
        $view_count_slides = ezmam_media_viewcount_get($album, $asset, 'high_slide') + ezmam_media_viewcount_get($album, $asset, 'low_slide');
    }

    $file_name = '';
    if ($origin == 'SUBMIT') {
        if ($asset_metadata['submitted_cam'] != '' && $asset_metadata['submitted_slide'] != '') {
            $file_name = $asset_metadata['submitted_cam'] . ' & ' . $asset_metadata['submitted_slide'];
        } elseif ($asset_metadata['submitted_cam'] != '') {
            $file_name = $asset_metadata['submitted_cam'];
        } else {
            $file_name = $asset_metadata['submitted_slide'];
        }
    }

    //
    // 3) We save the current state in session vars
    //
    $_SESSION['podman_mode'] = 'view_asset_details';
    $_SESSION['podman_album'] = $input['album'];
    $_SESSION['podman_asset'] = $input['asset'];

    global $enable_copy_asset;
    //
    // 4) Then display the asset and its content
    //
    require_once template_getpath('div_asset_details.php');
}
