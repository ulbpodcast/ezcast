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
   global $input;
	   
	require_once $basedir.'/commons/lib_sql_management.php';

	
	$album=$input['album'];
	$iduser=$input['iduser'];
	
	db_users_courses_delete_row($album,$iduser);
	
	// $album = suffix_remove($_SESSION['podman_album']);
	$tbusercourse= users_courses_get_users($album);
	
	// header('Location: index.php');
    require_once template_getpath('popup_list_moderator.php');	

    die;
}