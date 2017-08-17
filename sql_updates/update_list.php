<?php

/*
 * All sql updates must be added to this array
 * Format :
 * $update_list = [
 *    <source_version> => array(<new_version>, array(<sql query 1>, <sql_query 2>, ...)),
 *    (...)
 * ]
 */
$update_list = [
    "1.0.0" => array("1.0.1", array('ALTER TABLE !PREFIX!_courses` MODIFY COLUMN `course_code_public` varchar(50) NOT NULL;')),
];