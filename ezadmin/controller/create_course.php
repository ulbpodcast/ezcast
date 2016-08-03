<?php

function index($param = array()) {
    global $input;

    if (isset($input['create']) && $input['create']) {
        $course_code = $input['course_code'];
        $course_name = $input['course_name'];
        $shortname = $input['shortname'];

        $valid = false;
        if (empty($course_code)) {
            $error = template_get_message('missing_course_code', get_lang());
        } else if (empty($course_name)) {
            $error = template_get_message('missing_course_name', get_lang());
        } else {
            $valid = db_course_create($course_code, $course_name, $shortname);
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
