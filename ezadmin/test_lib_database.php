<?php
/**
 * @package ezcast.ezadmin.test
 */

require_once '../commons/lib_database.php';

$course_name = "PODC-I-000";
$user_name = "andewild";

echo 'Preparing DB ...';
var_dump(db_prepare());

echo 'Testing courses_search ...'.PHP_EOL;
db_courses_search_compact('%', '%', true, true, true, true);
echo "All users of $course_name ...".PHP_EOL;
var_dump(db_courses_search_compact($course_name, '%', true, true, false, true));
echo "All courses by $user_name ...".PHP_EOL;
var_dump(db_courses_search_compact('%', $user_name, true, true, false, true));
echo "All users of $course_name, step 2 ...".PHP_EOL;
var_dump(db_course_get_users($course_name));
?>
