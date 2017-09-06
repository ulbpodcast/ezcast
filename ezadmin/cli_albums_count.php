<?php

/*
 * This script is called every night to count the number of albums in ezmanager and update the DB accordingly
 */

require_once 'config.inc';
require_once '../commons/lib_database.php';

$dircontent = scandir($repository_path);

foreach ($dircontent as $album) {
    if ($album == '.' || $album == '..') {
        continue;
    }

    if ($course_code_str = strstr($album, '-pub', true)) {
        $course_code = $course_code_str;
        $updated_courses[] = $course_code;
    } elseif (($course_code_str = strstr($album, '-priv', true)) && !in_array($course_code_str, $updated_courses)) {
        $course_code = $course_code_str;
        $updated_courses[] = $course_code;
    }
}

db_prepare();
db_courses_update_hasalbums($dircontent);
db_close();
