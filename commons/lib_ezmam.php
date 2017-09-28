<?php

require_once(__DIR__ . '/config.inc');

function ezmam_course_get_new_id($course_code)
{
    $course_id = $course_code;
    //find first free course_id if needed
    $incremental_id = 0;
    $course = db_course_read($course_id);
    while ($course) { //if we found an already existing course, loop until we found a free id
        $course_id = $course_code . $incremental_id++;
        $course = db_course_read($course_id);
    }
    return $course_id;
}