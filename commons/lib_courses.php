<?php

/*
 * EZCAST Commons
 * Copyright (C) 2016 Université libre de Bruxelles
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
require_once 'lib_database.php';

/**
 * return array of the courses associated to a netid
 *
 * @param string $netid
 * @return array key: course code; value: course description
 */
function courses_list($netid = "")
{
    
    // prepared requests
    $statements = array(
        'course_all_get' =>
            'SELECT DISTINCT ' .
            db_gettable('courses') . '.course_code AS mnemonic, ' .
            db_gettable('courses') . '.course_name AS label ' .
            'FROM ' . db_gettable('courses') . ' ' .
            'ORDER BY mnemonic ASC',
        
        'user_courses_get' =>
            'SELECT DISTINCT ' .
            db_gettable('users_courses') . '.ID, ' .
            db_gettable('courses') . '.course_code, ' .
            db_gettable('courses') . '.course_name, ' .
            db_gettable('courses') . '.in_recorders, ' .
            db_gettable('users_courses') . '.origin ' .
            'FROM ' . db_gettable('courses') . ' ' .
            'INNER JOIN ' . db_gettable('users_courses') . ' ON ' . db_gettable('courses') . '.course_code = ' .
                db_gettable('users_courses') . '.course_code ' .
            'WHERE user_ID = :user_ID'
    );
    
    $db = db_prepare($statements);
    
    if (!$db) {
        debuglog("could not connect to sgbd:" . mysql_error());
        die;
    }
    $result = array();

    if ($netid == "") {
        // retrieves all courses in the database
        $course_list = db_courses_all_get();
        $result = array();
        foreach ($course_list as $value) {
            $result[$value['mnemonic']] = $value['mnemonic'] . '|' . $value['label'];
        }
    } else {
        // retrieves all courses for a given netid
        $course_list = db_user_courses_get($netid);
        
        $result = array();
        foreach ($course_list as $value) {
            $result[$value['course_code']] = $value['course_code'] . '|' . $value['course_name'];
        }
    }

    return $result;
}

function debuglog($message)
{
    global $commons_logfile, $_SERVER;

    $fp = fopen($commons_logfile, "a+");
    $time = time();
    $rawdate = getdate($time);
    $date = $rawdate["mday"] . "/" . $rawdate["mon"] . "/" . $rawdate["year"] . "  " . $rawdate["hours"] . ":" .
            $rawdate["minutes"] . ":" . $rawdate["seconds"];
    $line = $date . " ip: " . $_SERVER['REMOTE_ADDR'] . " " . $message . "\n";
    fwrite($fp, $line);
    fclose($fp);
}

/**
 * Retrieve all courses
 * @param String $course_code
 * @param String $course_name
 * @param boolean $in_recorders
 * @param integer $has_albums
 * @param Stirng $origin
 *
 */
function db_courses_all_get()
{
    global $statements; // from lib_database

    $statements['course_all_get']->execute();
    return $statements['course_all_get']->fetchAll();
}

function db_user_courses_get($user_ID)
{
    global $statements; // from lib_database

    $statements['user_courses_get']->bindParam(':user_ID', $user_ID);
    $statements['user_courses_get']->execute();

    return $statements['user_courses_get']->fetchAll();
}
