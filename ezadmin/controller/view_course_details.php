<?php

function index($param = array()) {
    global $input;

    if (empty($input['course_code']))
        die;

    if (isset($input['post'])) {
        $course_code = $input['course_code'];
        $course_name = $input['course_name'];
        $shortname = $input['shortname'];
        $in_recorders = $input['in_recorders'] ? 1 : 0;

        if (empty($course_name)) {
            $error = template_get_message('missing_course_name', get_lang());
        } else {
            db_course_update($course_code, $course_name, $shortname, $in_recorders);
            db_log(db_gettable('course'), 'Edited course ' . $input['course_code'], $_SESSION['user_login']);
            notify_changes();
        }
    }

    $courseinfo = db_course_read($input['course_code']);
    $users = db_course_get_users($input['course_code']);

    // Manipulate info
    $course_code = $courseinfo['course_code'];
    $course_name = $courseinfo['course_name'];
    $shortname = $courseinfo['shortname'];
    $origin = $courseinfo['origin'];
    $has_albums = ($courseinfo['has_albums'] != '0');
    $in_classroom = ($courseinfo['in_recorders'] == '1');
    //$users = array();
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_course_details.php');
    include template_getpath('div_main_footer.php');
}