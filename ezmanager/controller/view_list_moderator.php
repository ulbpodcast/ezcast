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
	require_once $basedir.'/ezadmin/lib_sql_management.php';
	
		file_put_contents('/home/arwillame/test2/addmoderator.txt','enter:    '. PHP_EOL . PHP_EOL );
    $album = suffix_remove($_SESSION['podman_album']);
    $moderation = album_is_private($_SESSION['podman_album']);
    $visibility = ($moderation) ? '-priv' : '-pub';

    ezmam_repository_path($repository_path);

    $album_meta = ezmam_album_metadata_get($album . $visibility);

		// file_put_contents('/home/arwillame/test2/addmoderator.txt','enter:    '. PHP_EOL . PHP_EOL );
    // $album = suffix_remove($_SESSION['podman_album']);
	$tbusercourse= users_courses_get_users($album);
	

		file_put_contents('/home/arwillame/test2/addmoderator.txt','Tb:    '.json_encode($tbusercourse). PHP_EOL . PHP_EOL );
		// file_put_contents('/home/arwillame/test2/addmoderator.txt','TOKEN IN FILE:    '.ezmam_album_token_manager_get($input['album']). PHP_EOL . PHP_EOL , FILE_APPEND);

	
    require_once template_getpath('popup_list_moderator.php');

    die;
}