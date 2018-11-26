<?php

function index($param = array())
{
    global $dir_date_format;
    global $submit_upload_dir;
    global $intros;
    global $credits;
    global $repository_path;
    global $default_add_title;
    global $titlings;
    global $default_downloadable;
    global $enable_audio_submit;
    global $valid_extensions_video;
    global $valid_mimeType_video;
    global $valid_extensions_audio;
    global $valid_mimeType_audio;
    

    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';

    ezmam_repository_path($repository_path);

    $album_meta = ezmam_album_metadata_get($album . $visibility);

    // for preselection in the form
    $album_intro = $album_meta['intro'];
    $album_credits = (isset($album_meta['credits']) ? $album_meta['credits'] : '');
    if (isset($album_meta['add_title'])) {
        $add_title = $album_meta['add_title'];
    } else {
        $add_title = $default_add_title;
    }

    // for checkbox in the form
    $downloadable = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable);

    require_once template_getpath('popup_submit_media.php');
    die;
}
