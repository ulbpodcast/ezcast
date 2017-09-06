<?php

function index($param = array())
{
    global $input;

    if (!db_course_delete($input['course_code'])) {
        redirectToController('view_course_details');
        return;
    }

    db_unlink_course($input['course_code']);

    db_log(db_gettable('courses'), 'Deleted internal course ' . $input['course_code'], $_SESSION['user_login']);
    notify_changes();
    redirectToController('view_courses');
}
