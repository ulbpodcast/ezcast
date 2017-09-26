<?php

require_once(__DIR__ . '/../../commons/lib_ezmam.php');

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 */
 function index($param = array())
 {
     global $input;
     global $max_course_code_size;
     global $max_album_label_size;
     global $course_id_validation_regex;
    
     //all these values are defined in each switch case
     $course_code_public = null;
     $album_type = null;
     $label = null;
   
     //if we create a new album
     switch ($input['action']) {
        case 'create_courseAndAlbum': //user create an arbitrary course
            // Sanity checks
            if (!isset($input['label']) || $input['label'] == "" || !isset($input['albumtype']) || $input['albumtype'] == "") {
                error_print_message("no given value");
                die();
            }
            $album_type = htmlspecialchars($input['albumtype']);
            $label = htmlspecialchars($input['label']);
            if ($album_type == "other") {
                $course_code_public = $label;
            } else {
                if (!isset($input['course_code']) || $input['course_code'] == "") {
                    error_print_message("no given value");
                    die();
                }
                $course_code_public = htmlspecialchars($input['course_code']);
            }
  
            //sanitize course code
            $course_code_public = preg_replace($course_id_validation_regex, "", $course_code_public); //start from the public code, keeping only alphabetic characters
            //
            //enforce size limits
            if (strlen($course_code_public) > $max_course_code_size) {
                $course_code_public = substr($course_code_public, 0, $max_course_code_size);
            }
            if (strlen($label) > $max_album_label_size) {
                $label = substr($label, 0, $max_album_label_size);
            }
            
            $course_id = ezmam_course_get_new_id($course_code_public);
                
            break;
        case 'create_album': //user pick from a course already existing in db
            // Sanity checks
            if (!isset($input['course_code']) || $input['course_code'] == "") {
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
            if (isset($courseinfo['course_code_public']) && $courseinfo['course_code_public'] != '') {
                $course_code_public = $courseinfo['course_code_public'];
            } else {
                $course_code_public = htmlspecialchars($input['course_code']);
            }
            
            $label = $courseinfo['course_name'];
            $album_type = "course";
       
            //course already exists in db, so do not regenerate a new id
            $course_id = $course_code;
            
            break;
        default:
            echo "wrong action";
            die;
    }
    
     $ok = ezmam_course_create_repository($course_id, $course_code_public, $label, $label, $album_type);
    
     $create_in_db = $input['action'] == 'create_courseAndAlbum';
     if ($create_in_db) {
         //finally, create course in db and link user to it
         $ok = ezmam_course_create_db($course_id, $course_code_public, $label, 1, $_SESSION['user_login']) && $ok;
     }
    
     //update course list
     acl_update_permissions_list();
    
     if ($ok) {
         require_once template_getpath('popup_album_successfully_created.php');
     } else {
         echo "failure";
     }
 }
