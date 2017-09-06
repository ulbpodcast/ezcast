<?php

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 */
 function index($param = array()) {
    global $input;
    global $repository_path;
    global $dir_date_format;
    global $default_intro;
    global $default_add_title;
    global $default_downloadable;
    global $default_credits;
    global $max_course_code_size;
    global $max_album_label_size;
    
    //all these values are defined in each switch case
    $course_code_public = null;
    $course_id = null;
    $album_type = null;
    $label = null;
   
    //if we create a new album
    switch($input['action']) {
        case 'create_courseAndAlbum': //user create an arbitrary course
            // Sanity checks
            if(!isset($input['label']) || $input['label'] == "" || !isset($input['albumtype']) || $input['albumtype'] == ""  ) {
                error_print_message("no given value");
                die();
            }    
            $album_type = htmlspecialchars($input['albumtype']);
            $label = htmlspecialchars($input['label']);
            if($album_type == "other") {
                 $course_code_public = $label;
            } else {
                if(!isset($input['course_code']) || $input['course_code'] == "") {
                    error_print_message("no given value");
                    die();
                }
                $course_code_public = htmlspecialchars($input['course_code']);
            }
            // --
  
            //enforce size limits
            if(strlen($course_code_public) > $max_course_code_size)
                $course_code_public = substr($course_code_public, 0, $max_course_code_size);
            if(strlen($label) > $max_album_label_size)
                $label = substr($label, 0, $max_album_label_size); 

            //generate real course id 
            $course_code_public = preg_replace("#[^a-zA-Z]#", "", $course_code_public); //start from the public code, keeping only alphabetic characters

        case 'create_album': //user pick from a course existing in db
            // Sanity checks
            if(!isset($input['course_code']) || $input['course_code'] == "") {
                error_print_message("no given value");
                die();
            }
            $course_code = htmlspecialchars($input['course_code']);
            // -
            
            if (!acl_has_album_permissions($course_code)) {
                error_print_message(template_get_message('Unauthorized', get_lang()));
                log_append('warning', 'create_album: tried to access course ' . $course_code . ' without permission');
                die;
            }
            
            $courseinfo = db_course_read($course_code);
            if(isset($courseinfo['course_code_public']) && $courseinfo['course_code_public'] != '' )
                $course_code_public = $courseinfo['course_code_public'];
            else 
                $course_code_public = htmlspecialchars($input['course_code']);
            
            $label = $courseinfo['course_name'];
            $album_type = "course";
            $course_id=$course_code;
                    
            break;
        default:
            echo "wrong action";
            die;
    }
    
    $ok = ezmam_albums_new_course($course_code_public, $label, $label, $album_type);
    
    switch($input['action']) {
        case 'create_courseAndAlbum':
             //finally, create in db
            db_course_create($course_id, $course_code_public, $label, 0);
            db_users_courses_create($course_id, $_SESSION['user_login']);
            break;
    }
    
    if($ok)
        require_once template_getpath('popup_album_successfully_created.php');
    else
        echo "failure";
}