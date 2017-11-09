<?php


function index($param = array())
{
    global $intros;
    global $credits;
    global $titlings;
    global $downloadable;
    global $anon_access;
    global $repository_path;
    global $default_add_title;
    global $default_downloadable;
    global $default_anon_access;

    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';
    $dbCourse=db_course_read($album);

    ezmam_repository_path($repository_path);

    $album_meta = ezmam_album_metadata_get($album . $visibility);

    // for preselection in the form
    $album_intro = $album_meta['intro'];
    if (isset($album_meta['credits'])) {
        $album_credits = $album_meta['credits'];
    }
    if (isset($album_meta['add_title'])) {
        $add_title = $album_meta['add_title'];
    } else {
        $add_title = $default_add_title;
    }

    // for the checkbox in the form
    $downloadable = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable);
    $anon_access = (isset($album_meta['anon_access']) ? $album_meta['anon_access'] : $default_anon_access);
    $recorder_access = (isset($dbCourse['in_recorders']) ? $dbCourse['in_recorders'] : 0);
    
    require_once template_getpath('popup_edit_album.php');

    die;
}
