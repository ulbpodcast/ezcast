<?php

function index($param = array()) {
    global $intros;
    global $credits;
    global $titlings;
    global $downloadable;
    global $anon_access;
    global $repository_path;
    global $default_add_title;
    global $default_downloadable;
    global $default_anon_access;
    global $basedir;

	
    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';
    ezmam_repository_path($repository_path);
    $album_meta = ezmam_album_metadata_get($album . $visibility);
    $tbusercourse= users_courses_get_users($album);
    require_once template_getpath('popup_moderator_list.php');
    die;
}