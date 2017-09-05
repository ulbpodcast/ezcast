<?php

function index($param = array()) {
    global $input;
    global $max_course_code_size;
    global $max_album_label_size;

    if (isset($input['create']) && $input['create']) {
        $course_code_public = htmlspecialchars($input['course_code']);      
        $course_name = $input['course_name'];        

        //enforce size limits
            if (strlen($course_code_public) > $max_course_code_size)
                $course_code_public = substr($course_code_public, 0, $max_course_code_size);
            
            if (strlen($course_name) > $max_album_label_size)
                $course_name = substr($course_name, 0, $max_album_label_size);       
      
       //generate real course id 
            $id_course_input = preg_replace("#[^a-zA-Z]#", "", $course_code_public); //start from the public code, keeping only alphabetic characters
            $incremental_id = 0;
            $course_code = $id_course_input;
            $course = db_course_read($course_code);
            while($course) { //if we found an already existing course, loop until we found a free id
                $course_code = $id_course_input . $incremental_id++;
                $course = db_course_read($course_code);				
            }

        if (isset($input['in_recorders'])) 
            $in_recorders = '1';
        
        else 
            $in_recorders = '0';

        $valid = false;
        if (empty($course_code)) {
            $error = template_get_message('missing_course_code', get_lang());
        } else if (empty($course_name)) {
            $error = template_get_message('missing_course_name', get_lang());
        } else {
            $valid = db_course_create($course_code,$course_code_public, $course_name, $in_recorders);
        }

        if ($valid) {
            $input['course_code'] = $course_code;    
            db_log(db_gettable('courses'), 'Created course ' . $input['course_code'], $_SESSION['user_login']);
            redirectToController('view_course_details');
            return;
        }
    }

    notify_changes();

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_course.php');
    include template_getpath('div_main_footer.php');
}