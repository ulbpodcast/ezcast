<?php

function index($param = array()) {
    global $input;

    if (isset($input['create']) && $input['create']) {
		$course_code_public=$input['course_code'];
		$input['course_code']=preg_replace("#[^a-zA-Z]#", "", $input['course_code']);
		if(strlen($input['course_code'])>=50) $input['course_code']=substr($input['course_code'], 0, 43) ;
		$course_code=str_replace(" ", '_', $input['course_code']).rand(100000,999999);
        $course_name = $input['course_name'];
		if(!isset($course_code_public) || $course_code_public=="") $course_code_public=$course_name;
        if(isset($input['in_recorders'])) $in_recorders = '1';
		else $in_recorders='0';

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