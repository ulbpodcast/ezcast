<?php

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 * @global type $input
 * @global type $ezmanager_url
 * @global type $repository_path
 * @global type $dir_date_format
 * @global type $default_intro 
 * @global type $default_credits
 
 */

 
 function index($param = array()) {
    global $input;
    global $repository_path;
    global $dir_date_format;
    global $default_intro;
    global $default_add_title;
    global $default_downloadable;
    global $default_credits;
    //
    // Sanity checks
    //
	// include "../commons/lib_sql_management.php";

	if($input['action']=='create_courseAndAlbum'){		
	     $course_code_public=$input['course_code'];	
		if(strlen($input['album'])>=50) $input['album']=substr($input['album'], 0, 43) ;
		$course=true;
		while($course){
			$idAlbum = preg_replace("#[^a-zA-Z]#", "", $input['album']);
			$idAlbum=str_replace(" ", '_',$idAlbum).rand(100000,999999);
			$course = db_course_read($idAlbum);				
		}
		$albumName=$input['album'];
		$input['album']=$idAlbum;
		if(!isset($course_code_public) || $course_code_public=="") $course_code_public=$albumName;																							
		db_course_create($input['album'],$course_code_public);
		db_users_courses_create($input['album'], $_SESSION['user_login']);
    }
	else{
		$albumName=$input['album'];
		$idAlbum=$input['album'];
		// preg_replace("#[^a-zA-Z]#", "", $input['album']);
	}
    if (!isset($input['album']) || (!acl_has_album_permissions($input['album']) && $input['action']!='create_courseAndAlbum' )) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'create_album: tried to access album ' . $input['album'] . ' without permission');
        die;
    }
    //
    // First of all, we have to set up the metada for the albums we're going to create
    //
    $not_created_albums = acl_authorized_albums_list_not_created(true);
    $description = $not_created_albums[$input['album']];
	if($description =='' && isset($albumName) )$description=$albumName;
	if($albumName==$idAlbum)$albumName=$description;
	if(!isset( $input['albumtype'])) $input['albumtype']='not_defined';
    $anac = get_anac(date('Y'), date('m'));
	
	// if(!isset( $input['isofficial'])) $input['isofficial']='true';
 	$courseinfo = db_course_read($input['album']);

	
    $metadata = array(
        'id' => $idAlbum,
        'course_code_public' => $courseinfo['course_code_public'],							 
        'name' => $albumName,
        'description' => $description,
        'date' => date($dir_date_format),
        'anac' => $anac,
        'intro' => $default_intro,
        'credits' => $default_credits,
        'add_title' => $default_add_title,
        'downloadable' => $default_downloadable,
        'type' => $input['albumtype'],
        'official' => 'false'
    );

    //
    // All we have to do now is call ezmam twice to create both the private and public album
    // (remember that $input['album'] only contains the album's base name, /not/ the suffix
    //
    ezmam_repository_path($repository_path);
    $res = ezmam_album_new($input['album'] . '-priv', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    $res = ezmam_album_new($input['album'] . '-pub', $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Don't forget to update the session variables!
    //
    acl_update_permissions_list();

    require_once template_getpath('popup_album_successfully_created.php');
}