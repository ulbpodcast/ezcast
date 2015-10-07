<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
*
* This software is free software; you can redistribute it and/or
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
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * @package ezcast.commons.lib.sql
 */

require_once '../commons/lib_database.php';

if(file_exists('config.inc'))
    require_once 'config.inc';

db_prepare(statements_get());


function statements_get(){
    return array(
        'update_courses_hasalbums' =>
            'UPDATE '.  db_gettable('courses'). ' ' .
            'SET has_albums = 1 '.
            'WHERE course_code = :course_code',
        
        'course_list' =>
            'SELECT ' . 
                db_gettable('courses') . '.course_code, ' .
                db_gettable('courses') . '.course_name, ' .
                db_gettable('courses') . '.in_recorders, ' .
                db_gettable('courses') . '.has_albums, ' .
                db_gettable('courses') . '.origin, ' .
                db_gettable('courses') . '.date_created ' .
            'FROM ' . db_gettable('courses') . ' ' .
            'WHERE ' .
                db_gettable('courses') . '.course_code LIKE :course_code AND ' . 
                db_gettable('courses') . '.course_name LIKE :course_name AND ' .
                db_gettable('courses') . '.in_recorders = :in_recorders AND ' .
                db_gettable('courses') . '.has_albums = :has_albums AND ' .
                db_gettable('courses') . '.origin = :origin',
        
        'course_create' =>
            'INSERT INTO ' . db_gettable('courses') . '(course_code, course_name, shortname, in_recorders, has_albums, date_created, origin) ' .
            'VALUES (:course_code, :course_name, :shortname, 0, 0, NOW(), \'internal\')',
        
        'course_read' =>
            'SELECT ' . 
                db_gettable('courses') . '.course_code, ' .
                db_gettable('courses') . '.course_name, ' .
                db_gettable('courses') . '.shortname, ' .
                db_gettable('courses') . '.in_recorders, ' .
                db_gettable('courses') . '.has_albums, ' .
                db_gettable('courses') . '.origin, ' .
                db_gettable('courses') . '.date_created ' .
            'FROM ' . db_gettable('courses') . ' ' .
            'WHERE course_code = :course_code',
        
        'course_get_users' =>
            'SELECT ' .
                db_gettable('users_courses').'.ID, '.
                db_gettable('users').'.user_ID, '.
                db_gettable('users').'.surname, '.
                db_gettable('users').'.forename, '.
                db_gettable('users_courses').'.origin '.
            'FROM '.  db_gettable('users').' ' .
            'INNER JOIN '.  db_gettable('users_courses').' ON '.  db_gettable('users').'.user_ID = '.  db_gettable('users_courses').'.user_ID '.
            'WHERE course_code = :course_code',
        
        'course_update' =>
            'UPDATE ' . db_gettable('courses') . ' ' .
            'SET course_name = :course_name, shortname = :shortname, in_recorders = :in_recorders ' .        
            'WHERE course_code = :course_code',
        
        'course_delete' =>
            'DELETE FROM ' . db_gettable('courses') . ' ' .
            'WHERE course_code = :course_code AND origin = \'internal\'',
        
        'user_read' =>
            'SELECT ' . 
                db_gettable('users') . '.user_ID, ' .
                db_gettable('users') . '.surname, ' .
                db_gettable('users') . '.forename, ' .
                db_gettable('users') . '.permissions, ' .
                db_gettable('users') . '.origin ' .
                //db_gettable('users') . '.date_created ' .
            'FROM ' . db_gettable('users') . ' ' .
            'WHERE user_ID = :user_ID',
                
        'user_courses_get' =>
            'SELECT DISTINCT ' .
                db_gettable('users_courses').'.ID, '.
                db_gettable('courses').'.course_code, '.
                db_gettable('courses').'.shortname, '.
                db_gettable('courses').'.course_name, '.
                db_gettable('courses').'.in_recorders, '.
                db_gettable('users_courses').'.origin '.
            'FROM '.  db_gettable('courses').' ' .
            'INNER JOIN '.  db_gettable('users_courses').
            ' ON '.  db_gettable('courses').'.course_code = '.  db_gettable('users_courses').'.course_code '.
            'WHERE user_ID = :user_ID',
        
        'users_get_admins' =>
            'SELECT user_ID '.
            'FROM ' .  db_gettable('users').' ' .
            'WHERE permissions > 0',
        
        'get_users_in_recorder' =>
            'SELECT DISTINCT '.
                db_gettable('users').'.user_ID, '.
                db_gettable('users').'.recorder_passwd, '.
                db_gettable('users').'.forename, '.
                db_gettable('users').'.surname, '.
                db_gettable('courses').'.course_code, '.
                db_gettable('courses').'.shortname, '.
                db_gettable('courses').'.course_name '.
            'FROM '.db_gettable('users').' '.
            'INNER JOIN '.db_gettable('users_courses').' '.
                'ON '.db_gettable('users').'.user_ID = '.db_gettable('users_courses').'.user_ID '.
            'INNER JOIN '.db_gettable('courses').' '.
                'ON '.db_gettable('users_courses').'.course_code = '.db_gettable('courses').'.course_code '.
            'WHERE '.db_gettable('courses').'.in_recorders != 0 '.
                'AND '.db_gettable('users').'.recorder_passwd IS NOT NULL '.
                'AND '.db_gettable('users').'.recorder_passwd != \'\'',
        
        'get_internal_users' =>
            'SELECT ' . 
                db_gettable('users') . '.user_ID, ' .
                db_gettable('users') . '.surname, ' .
                db_gettable('users') . '.forename, ' .
                db_gettable('users') . '.recorder_passwd ' .
            'FROM ' . db_gettable('users') . ' ' .
            'WHERE origin = \'internal\' ' .
            'AND recorder_passwd IS NOT NULL '.
            'AND recorder_passwd != \'\' ',
        
        'classrooms_list_enabled' =>
            'SELECT room_ID, name, IP ' .
            'FROM ' . db_gettable('classrooms').' '.
            'WHERE enabled = 1',
        
        'classrooms_list' =>
            'SELECT room_ID, name, IP, IP_remote ' .
            'FROM ' . db_gettable('classrooms'),
        
        'classroom_update_enabled' =>
            'UPDATE ' . db_gettable('classrooms') . ' ' .
            'SET enabled = :enabled ' .
            'WHERE room_ID = :room_ID',
        
        'users_courses_create' =>
            'INSERT INTO ' . db_gettable('users_courses') . '(course_code, user_ID, origin) ' .
            'VALUES (:course_code, :user_ID, \'internal\')',
        
        'users_courses_delete' =>
            'DELETE FROM ' . db_gettable('users_courses') . ' ' .
            'WHERE ID = :user_course_ID AND origin=\'internal\'',
        
        'users_courses_get' =>
            'SELECT * ' .
            'FROM ' . db_gettable('users_courses') . ' ' .
            'WHERE course_code=:course_code AND user_ID=:user_ID',
        
        'found_rows' => 
            'SELECT  FOUND_ROWS();',
        
        'user_create' =>
            'INSERT INTO ' . db_gettable('users') . '(user_ID, surname, forename, recorder_passwd, permissions, origin) ' .
            'VALUES (:user_ID, :surname, :forename, :recorder_passwd, :permissions, \'internal\')',
        
        'user_delete' => 
            'DELETE FROM ' . db_gettable('users') . ' ' .
            'WHERE user_ID = :user_ID AND origin=\'internal\'',
        
        'user_update' =>
            'UPDATE ' . db_gettable('users') . ' ' .
            'SET surname = :surname, forename = :forename, recorder_passwd = :recorder_passwd, permissions = :permissions' . ' ' .
            'WHERE user_ID = :user_ID',
        
        'user_update_short' =>
            'UPDATE ' . db_gettable('users') . ' ' .
            'SET surname = :surname, forename = :forename,  permissions = :permissions' . ' ' .
            'WHERE user_ID = :user_ID',
        
        'log_action' =>
            'INSERT INTO ' . db_gettable('logs') . ' (`time`, `table`, message, author) ' .
            'VALUES (NOW(), :table, :message, :author)',
        
        'classroom_create' =>
            'INSERT INTO ' . db_gettable('classrooms') . '(room_ID, name, ip, ip_remote, enabled) ' .
            'VALUES (:room_ID, :name, :ip, :ip_remote, :enabled)',
        
        'unlink_course' =>
            'DELETE FROM ' . db_gettable('users_courses') . ' ' .
            'WHERE course_code = :course_code AND origin=\'internal\'',
        
        'unlink_user' =>
            'DELETE FROM ' . db_gettable('users_courses') . ' ' .
            'WHERE user_ID = :user_ID AND origin=\'internal\'',
        
        'classroom_update' => 
            'UPDATE ' . db_gettable('classrooms') . ' ' .
            'SET room_ID = :room_ID, name = :name,  ip = :ip,  ip_remote = :ip_remote' . ' ' .
            'WHERE room_ID = :ID',
        
        'classroom_delete' =>
            'DELETE FROM ' . db_gettable('classrooms') . ' ' .
            'WHERE room_ID = :room_ID'       
    );
}

//---------------------------
// PAGE-SPECIFIC FUNCTIONS
//---------------------------

/**
 * Returns the courses corresponding the the following criteria
 * @param String $course_code
 * @param String $user_ID
 * @param boolean $include_external
 * @param boolean $include_internal
 * @param integer $has_albums -1 = everything; 0 = only those with no albums; 1 = only those with album
 * @param integer $in_classrooms  -1 = everything; 0 = only those not in recorder; 1 = only those in recorder
 * @param integer $with_teacher  -1 = everything; 0 = only those without teachers; 1 = only those with teachers
 * @param String $order the order condition
 * @param String $limit the limit condition
 */
function db_courses_search($course_code, $user_ID, $include_external, $include_internal, $has_albums, $in_classrooms, $with_teacher, $order, $limit) {   
    global $db_object;
    
    $origin = $include_external && $include_internal ? '%' : ($include_external ? 'external' : 'internal');
    
    $join = 'LEFT';
    if($with_teacher == 1) $join = 'INNER';
    
    $query = 
        'SELECT DISTINCT SQL_CALC_FOUND_ROWS ' .  
            db_gettable('courses') . '.course_code, ' .
            db_gettable('users') . '.user_ID AS user_ID, ' .
            db_gettable('users_courses') . '.origin, ' .
            db_gettable('courses') . '.in_recorders, ' .
            db_gettable('courses') . '.has_albums, ' .
            db_gettable('courses') . '.shortname, ' .
            db_gettable('courses') . '.course_name, ' .
            db_gettable('users') . '.forename, ' .
            db_gettable('users') . '.surname ' .
            'FROM ' . db_gettable('users_courses') . ' ' .
            'RIGHT JOIN ' . db_gettable('courses') . ' ' .
                'ON ' . db_gettable('courses') . '.course_code = ' . db_gettable('users_courses') .'.course_code ' .
            $join . ' JOIN ' . db_gettable('users') . ' ' .
                'ON ' . db_gettable('users').'.user_ID = ' . db_gettable('users_courses').'.user_ID ' .
            'WHERE ' .
                db_gettable('courses') . '.course_code LIKE "' . addslashes($course_code) . '"' .
                ($origin != "%" ? ' AND ' . db_gettable('users_courses') . '.origin LIKE "' . addslashes($origin) . '"' : '') .
                ($user_ID != "%" ? ' AND ' . db_gettable('users') . '.user_ID LIKE "' . $user_ID . '"': '') .
                ($has_albums != -1 ? ' AND ' . db_gettable('courses') . '.has_albums = "' . $has_albums . '"': '') .
                ($in_classrooms != -1 ? ' AND ' . db_gettable('courses') . '.in_recorders = "' . $in_classrooms . '"': '') .
                ($with_teacher == 0 ? ' AND '.  db_gettable('users') . '.user_ID IS NULL': '') .
            ($order ? ' ORDER BY ' . $order : '') .
            ($limit ? ' LIMIT ' . $limit : '');
      
    $res = $db_object->query($query);
        
    return $res;
}

/**
 * Updates the "has_albums" field in the DB. Scans the content of the repo and updates the fields that have changed.
 * @param type $repo_content 
 */
function db_courses_update_hasalbums($repo_content) {
    global $db_object;
    global $statements;
    
    $course_code = '';
    $db_object->beginTransaction();
    $statements['update_courses_hasalbums']->bindParam(':course_code', $course_code);
    
    $updated_courses = array(); // To prevent double update
    foreach($repo_content as $album) {
        if($album == '.' || $album == '..')
            continue;
        
        if($course_code_str = strstr($album, '-ppub', true)) {
            $course_code = $course_code_str;
            $statements['update_courses_hasalbums']->execute();
            $updated_courses[] = $course_code;
        }
        else if(($course_code_str = strstr($album, '-priv', true)) && !in_array($course_code_str, $updated_courses)) {
            $course_code = $course_code_str;
            $statements['update_courses_hasalbums']->execute();
            $updated_courses[] = $course_code;
        }
    }
    
    $db_object->commit();
}

/**
 * Retrieve all courses
 * @param String $course_code
 * @param String $course_name
 * @param String $shortname
 * @param boolean $in_recorders
 * @param integer $has_albums
 * @param Stirng $origin
 * 
 */
function db_courses_list($course_code, $course_name, $shortname, $in_recorders, $has_albums, $origin) {
    global $statements;
    
    $statements['course_list']->bindParam(':course_code', $course_code);
    $statements['course_list']->bindParam(':course_name', $course_name);
    $statements['course_list']->bindParam(':shortname', $shortname);
    $statements['course_list']->bindParam(':in_recorders', $in_recorders);
    $statements['course_list']->bindParam(':has_albums', $has_albums);
    $statements['course_list']->bindParam(':origin', $origin);
    
    $statements['course_list']->execute();
    return $statements['course_list']->fetchAll();  
}

/**
 * Create a new course
 * @param String $course_code
 * @param String $course_name
 * @param String $shortname
 * @param boolean $in_recorders
 * @param integer $has_albums
 * @param Stirng $origin
 */
function db_course_create($course_code, $course_name, $shortname) {
    global $statements;
    
    $statements['course_create']->bindParam(':course_code', $course_code);
    $statements['course_create']->bindParam(':course_name', $course_name);
    $statements['course_create']->bindParam(':shortname', $shortname);
    
    return $statements['course_create']->execute();
}

/**
 * Returns the info related to a course
 * @param String $course_code
 */
function db_course_read($course_code) {
    global $statements;
    
    $statements['course_read']->bindParam(':course_code', $course_code);
    
    $statements['course_read']->execute();
    return $statements['course_read']->fetch();
}

/**
 * Returns the users associated to a course
 * @param String $course_code 
 */
function db_course_get_users($course_code) {
    global $statements;
    
    $statements['course_get_users']->bindParam(':course_code', $course_code);
    $statements['course_get_users']->execute();
    
    return $statements['course_get_users']->fetchAll();
}

/**
 * Update a course
 * @param String $course_code
 */
function db_course_update($course_code, $course_name, $shortname, $in_recorders) {
    global $statements;

    $statements['course_update']->bindParam(':course_code', $course_code);
    $statements['course_update']->bindParam(':course_name', $course_name);
    $statements['course_update']->bindParam(':shortname', $shortname);
    $statements['course_update']->bindParam(':in_recorders', $in_recorders);
    
    return $statements['course_update']->execute();
}

/**
 * Delete course
 * @param $course_code
 */
function db_course_delete($course_code) {
    global $statements;
    
    $statements['course_delete']->bindParam(':course_code', $course_code);
    
    return $statements['course_delete']->execute();
}

/**
 * Returns the list of (recorder/ezmanager) admins
 */
function db_admins_list() {
    global $statements;
    
    $statements['users_get_admins']->execute();
    return $statements['users_get_admins']->fetchAll();
}

function db_users_list($user_ID, $surname, $forename, $origin, $is_admin, $order, $limit) {    
    global $db_object;
       
    $query = 
        'SELECT DISTINCT SQL_CALC_FOUND_ROWS ' .  
            db_gettable('users') . '.user_ID, ' .
            db_gettable('users') . '.surname, ' .
            db_gettable('users') . '.forename, ' .
            db_gettable('users') . '.permissions, ' .
            db_gettable('users') . '.origin ' .
        'FROM ' . db_gettable('users') . ' ' .
        'WHERE user_ID LIKE "' . $user_ID . '" AND ' .
            'forename LIKE "' . $forename . '" AND ' .
            'surname LIKE "' . $surname . '"' .
            ($is_admin != -1 ? ' AND permissions = ' . $is_admin : '') .
            ($origin != -1 ? ' AND origin LIKE "' . addslashes($origin) . '"' : '') .
        ($order ? ' ORDER BY ' . $order : '') .
        ($limit ? ' LIMIT ' . $limit : '');
      
    $res = $db_object->query($query);
           
    return $res;
}

/**
 * Return infos about a user (user_ID, forename, surname, permissions and origin)
 * @global array $statements
 * @param type $user_ID
 * @return type 
 */
function db_user_read($user_ID) {
    global $statements;
    
    $statements['user_read']->bindParam(':user_ID', $user_ID);
    $statements['user_read']->execute();
    
    return $statements['user_read']->fetch();
}

function db_user_get_courses($user_ID) {
    global $statements;
    
    $statements['user_courses_get']->bindParam(':user_ID', $user_ID);
    $statements['user_courses_get']->execute();
    
    return $statements['user_courses_get']->fetchAll();
}

function db_users_courses_get($course_code, $user_ID) {
    global $statements;
    
    $statements['users_courses_get']->bindParam(':course_code', $course_code);
    $statements['users_courses_get']->bindParam(':user_ID', $user_ID);
    
    $statements['users_courses_get']->execute();
    return $statements['users_courses_get']->fetch(); 
    
}

/**
 * 
 * @global array $statements
 * @param type $room_ID
 * @param type $name
 * @param type $ip
 * @param type $enabled -1 for both enabled and disabled; 0 for disabled only; 1 for enabled only
 */
function db_classrooms_search($room_ID, $name, $ip, $enabled, $order, $limit) {
    global $db_object;
    
    $query =
       'SELECT DISTINCT SQL_CALC_FOUND_ROWS ' .
            db_gettable('classrooms') . '.room_ID, ' .
            db_gettable('classrooms') . '.name, ' .
            db_gettable('classrooms') . '.IP, ' .
            db_gettable('classrooms') . '.IP_remote, ' .
            db_gettable('classrooms') . '.enabled ' .
       'FROM '. db_gettable('classrooms') . ' ' .
       'WHERE ' . 
            'room_ID LIKE "' . $room_ID . '" AND ' .
            'name LIKE "' . $name . '" AND '. 
            'IP LIKE "' . $ip . '"' .
            ($enabled != -1 ? ' AND enabled = ' . $enabled : '') .
       ($order ? ' ORDER BY ' . $order : '') .
       ($limit ? ' LIMIT ' . $limit : '');
    ;

    return $db_object->query($query);
}

/**
 * Returns the name, ID and IP of all the recorders
 */
function db_classrooms_list() {
    global $statements;
    
    $statements['classrooms_list']->execute();
    return $statements['classrooms_list']->fetchAll();
}

/**
 * Returns the name, ID and IP of all the enabled recorders
 */
function db_classrooms_list_enabled() {
    global $statements;
    
    $statements['classrooms_list_enabled']->execute();
    return $statements['classrooms_list_enabled']->fetchAll();
}

/**
 * Sets the "enabled" bit to true or false
 * @global array $statements
 * @param type $room_ID
 * @param type $enabled
 * @return type 
 */
function db_classroom_update_enabled($room_ID, $enabled) {
    global $statements;
    
    $statements['classroom_update_enabled']->bindParam(':room_ID', $room_ID);
    $statements['classroom_update_enabled']->bindParam(':enabled', $enabled, PDO::PARAM_INT);
    
    $res = $statements['classroom_update_enabled']->execute();
    
    return $res;
}

function db_users_in_recorder_get() {
    global $statements;
    
    $statements['get_users_in_recorder']->execute();
    return $statements['get_users_in_recorder']->fetchAll();
}

/**
 * Returns the list of users created manually
 */
function db_users_internal_get() {
    global $statements;
    
    $statements['get_internal_users']->execute();
    return $statements['get_internal_users']->fetchAll();
}


function db_users_courses_create($course_code, $user_ID) {
    global $statements;
    
    if(db_users_courses_get($course_code, $user_ID)) return false;
    
    $statements['users_courses_create']->bindParam(':course_code', $course_code);
    $statements['users_courses_create']->bindParam(':user_ID', $user_ID);
    
    if(!$statements['users_courses_create']->execute()) return false;
    
    // return informations
    global $db_object;
    $user = db_user_read($user_ID);
    if(!$user) return false;
    
    $course = db_course_read($course_code);
    if(!$course) return false;
    
    return array('user' => $user, 'course' => $course, 'id' => $db_object->lastInsertId());
}

function db_users_courses_delete($user_course_ID) {
    global $statements;
        
    $statements['users_courses_delete']->bindParam(':user_course_ID', $user_course_ID);
    
    return $statements['users_courses_delete']->execute();
}

function db_unlink_user($user_ID) {
    global $statements;
        
    $statements['unlink_user']->bindParam(':user_ID', $user_ID);
    
    return $statements['unlink_user']->execute();
}

function db_unlink_course($course_code) {
    global $statements;
        
    $statements['unlink_course']->bindParam(':course_code', $course_code);
    
    return $statements['unlink_course']->execute();
}

function db_found_rows() {
    global $statements;
        
    $statements['found_rows']->execute(); 
    
    $res = $statements['found_rows']->fetch();   
    return intval($res[0]);
}

function db_user_create($user_ID, $surname, $forename, $recorder_passwd, $permissions) {
    global $statements;
        
    $statements['user_create']->bindParam(':user_ID', strtolower($user_ID));
    $statements['user_create']->bindParam(':surname', $surname);
    $statements['user_create']->bindParam(':forename', $forename);
    $statements['user_create']->bindParam(':recorder_passwd', $recorder_passwd);
    $statements['user_create']->bindParam(':permissions', $permissions);
    
    return $statements['user_create']->execute();
}

function db_user_delete($user_ID) {
    global $statements;
        
    $statements['user_delete']->bindParam(':user_ID', $user_ID);
    
    return $statements['user_delete']->execute();
}

function db_user_update($user_ID, $surname, $forename, $recorder_passwd, $permissions) {
    global $statements;
    
    if(empty($recorder_passwd)) {
        $statements['user_update_short']->bindParam(':user_ID', $user_ID);
        $statements['user_update_short']->bindParam(':surname', $surname);
        $statements['user_update_short']->bindParam(':forename', $forename);
        $statements['user_update_short']->bindParam(':permissions', $permissions);

        return $statements['user_update_short']->execute();     
    }
    
    $statements['user_update']->bindParam(':user_ID', $user_ID);
    $statements['user_update']->bindParam(':surname', $surname);
    $statements['user_update']->bindParam(':forename', $forename);
    $statements['user_update']->bindParam(':recorder_passwd', $recorder_passwd);
    $statements['user_update']->bindParam(':permissions', $permissions);
    
    return $statements['user_update']->execute();
}

/**
 * Logs the action 'action' performed on table 'table'
 * @param type $table
 * @param type $action 
 */
function db_log($table, $action, $author) {
    global $statements;
    
    $statements['log_action']->bindParam(':table', $table);
    $statements['log_action']->bindParam(':message', $action);
    $statements['log_action']->bindParam(':author', $author);
    
    return $statements['log_action']->execute();
}

function db_logs_get($date_start, $date_end, $table, $author, $limit) {
    global $db_object;
    
    $query = 'SELECT DISTINCT SQL_CALC_FOUND_ROWS `time`, `table`, message, author FROM '.  db_gettable('logs');
    
    $where = '';
    if(!empty($date_start)) {
        $where .= 'time >= \''.$date_start.' 00:00:00\'';
    }
    
    if(!empty($date_end)) {
        if(!empty($where))
            $where .= ' AND ';
        $where .= 'time <= \''.$date_end.' 00:00:00\'';
    }
    
    if(!empty($table)) {
        if($table != 'all') {
            if(!empty($where))
                $where .= ' AND ';
            $where .= '`table` LIKE \''.db_gettable($table).'\'';
        }
            
    }
    
    if(!empty($author)) {
        if(!empty($where))
            $where .= ' AND ';
        $where .= 'author LIKE %'.$author.'%';
    }
    
    $fullQuery = $query;
    
    if(!empty($where))
        $fullQuery .= ' WHERE ' . $where;

    return $db_object->query($fullQuery.' ORDER BY `time` DESC LIMIT ' . $limit);
}

function db_classroom_create($room_ID, $name, $ip, $ip_remote, $enabled) {
    global $statements;
    
    $statements['classroom_create']->bindParam(':room_ID', $room_ID);
    $statements['classroom_create']->bindParam(':name', $name);
    $statements['classroom_create']->bindParam(':ip', $ip);
    $statements['classroom_create']->bindParam(':ip_remote', $ip_remote);
    $statements['classroom_create']->bindParam(':enabled', $enabled);
    
    return $statements['classroom_create']->execute();
}

function db_classroom_update($ID, $room_ID, $name, $ip, $ip_remote) {
    global $statements;

    $statements['classroom_update']->bindParam(':ID', $ID);
    $statements['classroom_update']->bindParam(':room_ID', $room_ID);
    $statements['classroom_update']->bindParam(':name', $name);
    $statements['classroom_update']->bindParam(':ip', $ip);
    $statements['classroom_update']->bindParam(':ip_remote', $ip_remote);
    
    return $statements['classroom_update']->execute();
}

function db_classroom_delete($room_ID) {    
    global $statements;

    $statements['classroom_delete']->bindParam(':room_ID', $room_ID);
    
    return $statements['classroom_delete']->execute();
}


?>
