<?php

/*
* EZCAST Commons 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
 * Library for consultation of courses in EZadmin's database
 */

require_once 'config.inc';
require_once 'lib_mysql.php';

/**
 * return array of the courses associated to a netid
 *
 * @param string $netid
 * @return array key: course code; value: course description
 */
function courses_list($netid) {
    global $db_host;
    global $db_name;
    global $db_passwd;
    global $db_login;
    global $db_prefix;

    $db = db_open($db_host, $db_name, $db_login, $db_passwd);
    if (!$db) {
        debuglog("could not connect to sgbd:" . mysql_error());
        die;
    }

    $result = array();


    if ($netid == "") {
        // retrieves all courses in the database
        $sql = "SELECT DISTINCT course_code AS mnemonic, course_name AS label FROM " . $db_prefix . "courses ORDER BY course_code ASC";
        $course_list = db_select($db, $sql);
        $result = array();
        foreach ($course_list as $value)
            $result[$value['mnemonic']] = utf8_decode($value['mnemonic'] . '|' . $value['label']);
    } else {
        // retrieves all courses for a given netid
        $sql = "SELECT DISTINCT " . $db_prefix . "courses.course_code AS mnemonic, " . $db_prefix . "courses.course_name AS label FROM " . $db_prefix . "courses, " . $db_prefix . "users_courses " .
                "WHERE " . $db_prefix . "courses.course_code = " . $db_prefix . "users_courses.course_code " .
                "AND " . $db_prefix . "users_courses.user_ID = '" . $netid . "' ORDER BY " . $db_prefix . "courses.course_code";
        $course_list = db_select($db, $sql);

        $result = array();
        foreach ($course_list as $value)
            $result[$value['mnemonic']] = utf8_decode($value['mnemonic'] . '|' . $value['label']);
    }
    db_close($db);
    return $result;
}


function debuglog($message) {
    global $commons_logfile, $_SERVER;

    $fp = fopen($commons_logfile, "a+");
    $time = time();
    $rawdate = getdate($time);
    $date = $rawdate["mday"] . "/" . $rawdate["mon"] . "/" . $rawdate["year"] . "  " . $rawdate["hours"] . ":" . $rawdate["minutes"] . ":" . $rawdate["seconds"];
    $line = $date . " ip: " . $_SERVER['REMOTE_ADDR'] . " " . $message . "\n";
    fwrite($fp, $line);
    fclose($fp);
}

?>
