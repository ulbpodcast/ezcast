<?php
/*
* EZCAST EZadmin
* Copyright (C) 2016 Université libre de Bruxelles
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
require_once __DIR__ . '/lib_database.php';
if (file_exists(__DIR__.'/config.inc') && !$in_install) {
    include_once 'config.inc';
    $stmt_array = statements_get();
    db_prepare($stmt_array);
    global $db_object;
}

function statements_get()
{
    return array(
        'update_courses_hasalbums' =>
                'UPDATE '.  db_gettable('courses'). ' ' .
                'SET has_albums = 1 '.
                'WHERE course_code = :course_code',
            'course_list' =>
                    'SELECT ' .
                            'courses.course_code, ' .
                            'courses.course_name, ' .
                            'courses.course_code_public, ' .
                            'courses.in_recorders, ' .
                            'courses.has_albums, ' .
                            'courses.origin, ' .
                            'courses.date_created ' .
                    'FROM ' . db_gettable('courses') . ' courses ' .
                    'WHERE ' .
                            'courses.course_code LIKE :course_code AND ' .
                            'courses.course_name LIKE :course_name AND ' .
                            'courses.in_recorders = :in_recorders AND ' .
                            'courses.has_albums = :has_albums AND ' .
                            'courses.origin = :origin',

            'course_create' =>
                    'INSERT INTO ' . db_gettable('courses') . '(course_code, course_code_public, course_name, in_recorders, has_albums, date_created, origin) ' .
                    'VALUES (:course_code, :course_code_public, :course_name, :in_recorders, 0, NOW(), \'internal\')',
        
            'course_read' =>
                    'SELECT ' .
                            db_gettable('courses') . '.course_code, ' .
                            db_gettable('courses') . '.course_code_public, ' .
                            db_gettable('courses') . '.course_name, ' .
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
                    'INNER JOIN '.  db_gettable('users_courses').' ON '.  db_gettable('users').'.user_ID = '.
                        db_gettable('users_courses').'.user_ID '.
                    'WHERE course_code = :course_code',

            'course_update' =>
                    'UPDATE ' . db_gettable('courses') . ' ' .
                    'SET course_name = :course_name, in_recorders = :in_recorders ' .
                    'WHERE course_code = :course_code',
        
            'enable_recorders_for_all_courses' =>
                    'UPDATE ' . db_gettable('courses') . ' c ' .
                    'JOIN '.db_gettable('users_courses').' link ON '.
                        'link.user_ID = :userID AND '.
                        'link.course_code = c.course_code '.
                    'SET c.in_recorders = true',
        
            'user_get_courses_with_recorder' =>
                    'SELECT c.course_code, c.course_name ' . 
                    'FROM ' . db_gettable('courses') . ' c ' .
                    'JOIN '.db_gettable('users_courses').' link ON '.
                        'link.user_ID = :userID AND '.
                        'link.course_code = c.course_code AND '.
                        'c.in_recorders = 1 ',

            'course_update_anon' =>
                'UPDATE ' . db_gettable('courses') . ' ' .
                'SET anon_access = :anon_access ' .
                'WHERE course_code = :course_code',
        
            'course_delete' =>
                    'DELETE FROM ' . db_gettable('courses') . ' ' .
                    'WHERE course_code = :course_code AND origin = \'internal\'',

            'user_read' =>
                    'SELECT ' .
                            db_gettable('users') . '.user_ID, ' .
                            db_gettable('users') . '.surname, ' .
                            db_gettable('users') . '.forename, ' .
                      '(' . db_gettable('users') . '.recorder_passwd = "" OR '.db_gettable('users').'.recorder_passwd IS NULL) as passNotSet, ' .
                            db_gettable('users') . '.permissions, ' .
                            db_gettable('users') . '.origin ' .
                            //db_gettable('users') . '.date_created ' .
                    'FROM ' . db_gettable('users') . ' ' .
                    'WHERE user_ID = :user_ID',

            'user_courses_get' =>
                    'SELECT DISTINCT ' .
                            db_gettable('users_courses').'.ID, '.
                            db_gettable('courses').'.course_code_public, '.
                            db_gettable('courses').'.course_code, '.
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
                            db_gettable('courses').'.course_code_public, '.
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
        
            'classrooms_from_name_get_ip' =>
                    'SELECT IP ' .
                    'FROM ' . db_gettable('classrooms') . ' ' .
                    'WHERE room_ID = :room_ID',

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

            'users_courses_delete_row' =>
                'DELETE FROM ' . db_gettable('users_courses') . ' ' .
                'WHERE course_code=:course_code AND user_ID=:user_ID',
                'users_courses_get' =>
                        'SELECT * ' .
                        'FROM ' . db_gettable('users_courses') . ' ' .
                        'WHERE course_code=:course_code AND user_ID=:user_ID',

            'users_courses_get_users' =>
                'SELECT user_ID ' .
                'FROM ' . db_gettable('users_courses') . ' ' .
                'WHERE course_code=:course_code',
        
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
        
            'user_update_recorder_passwd' =>
                    'UPDATE ' . db_gettable('users') . ' ' .
                    'SET recorder_passwd = :passwd' . ' ' .
                    'WHERE user_ID = :user_ID',

            'log_action' =>
                    'INSERT INTO ' . db_gettable('admin_logs') . ' (`time`, `table`, message, author) ' .
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
                    'WHERE room_ID = :room_ID',

            'stream_create' =>
                        'INSERT INTO ' . db_gettable('streams') . ' (`cours_id`, `asset`, `classroom`, `record_type`, '.
                        '`netid`, `stream_name`, `token`, `module_type`, `ip`, `status`, `quality`, `protocol`, `server`, `port`) ' .
                        'VALUES (:cours_id, :asset, :classroom, :record_type, :netid, :stream_name, :token, :module_type, '.
                        ':ip, :status, :quality, :protocol, :server, :port)',

            'stream_update_status' =>
                    'UPDATE ' . db_gettable('streams') . ' ' .
                    'SET status = :status' . ' ' .
                    'WHERE cours_id = :course AND asset = :asset AND module_type = :module_type ',

            'get_stream_info' =>
                    'SELECT  * ' .
                    'FROM ' . db_gettable('streams') . ' ' .
                    'WHERE cours_id=:cours_id AND asset=:asset ',

            'in_recorder_update' =>
                'UPDATE ' . db_gettable('courses') . ' ' .
                'SET in_recorders = :in_recorders ' .
                'WHERE course_code = :course_code',
        
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
function db_courses_search(
    $course_code,
    $user_ID,
    $include_external,
    $include_internal,
    $has_albums,
    $in_classrooms,
    $with_teacher,
    $order,
    $limit
) {
    global $db_object;

    $origin = $include_external && $include_internal ? '%' : ($include_external ? 'external' : 'internal');

    $join = 'LEFT';
    if ($with_teacher == 1) {
        $join = 'INNER';
    }

    // will have origin = NULL when no user is yet added to course
    $query =
            'SELECT DISTINCT SQL_CALC_FOUND_ROWS ' .
                    ' table_courses.course_code, ' .
                    ' table_courses.course_code_public, ' .
                    ' table_users.user_ID AS user_ID, ' .
                    ' table_courses.origin, ' .
                    ' table_courses.in_recorders, ' .
                    ' table_courses.has_albums, ' .
                    ' table_courses.course_name, ' .
                    ' table_users.forename, ' .
                    ' table_users.surname ' .
                    'FROM ' . db_gettable('courses') . ' table_courses ' .
                    'LEFT OUTER JOIN ' . db_gettable('users_courses') . ' users_courses ' .
                            'ON ' . ' table_courses.course_code = users_courses.course_code ' .
                    $join . ' JOIN ' . db_gettable('users') . ' table_users ' .
                            'ON table_users.user_ID = users_courses.user_ID ' .
                    'WHERE ' .
                            '( table_courses.course_code_public LIKE "' . addslashes($course_code) . '"' .
                            '  OR table_courses.course_code LIKE "' . addslashes($course_code) . '" )' .
                            ($origin != "%" ? ' AND users_courses.origin LIKE "' . addslashes($origin) . '"' : '') .
                            ($user_ID != "%" ? ' AND table_users.user_ID LIKE "' . $user_ID . '"': '') .
                            ($has_albums != -1 ? ' AND ' . ' table_courses.has_albums = "' . $has_albums . '"': '') .
                            ($in_classrooms != -1 ? ' AND ' . ' table_courses.in_recorders = "' . $in_classrooms . '"': '') .
                            ($with_teacher == 0 ? ' AND table_users.user_ID IS NULL': '') .
                    ' GROUP BY course_code ' .
                    ($order ? ' ORDER BY ' . $order : '') .
                    ($limit ? ' LIMIT ' . $limit : '');

    $res = $db_object->query($query);

    return $res;
}

/**
 * Updates the "has_albums" field in the DB. Scans the content of the repo and updates the fields that have changed.
 * @param type $repo_content
 */
function db_courses_update_hasalbums($repo_content)
{
    global $db_object;
    global $statements;

    $course_code = '';
    $db_object->beginTransaction();
    $statements['update_courses_hasalbums']->bindParam(':course_code', $course_code);

    $updated_courses = array(); // To prevent double update
    foreach ($repo_content as $album) {
        if ($album == '.' || $album == '..') {
            continue;
        }

        if ($course_code_str = strstr($album, '-pub', true)) {
            $course_code = $course_code_str;
            $statements['update_courses_hasalbums']->execute();
            $updated_courses[] = $course_code;
        } elseif (($course_code_str = strstr($album, '-priv', true)) && !in_array($course_code_str, $updated_courses)) {
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
 * @param boolean $in_recorders
 * @param integer $has_albums
 * @param Stirng $origin
 *
 */
function db_courses_list($course_code, $course_name, $in_recorders, $has_albums, $origin)
{
    global $statements;
    
    $statements['course_list']->bindParam(':course_code', $course_code);
    $statements['course_list']->bindParam(':course_name', $course_name);
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
 * @param boolean $in_recorders
 * @param integer $has_albums
 * @param Stirng $origin
 */
function db_course_create($course_id, $course_code_public, $course_name, $in_recorders)
{
    global $statements;
    
    $statements['course_create']->bindParam(':course_code', $course_id);
    $statements['course_create']->bindParam(':course_code_public', $course_code_public);
    $statements['course_create']->bindParam(':course_name', $course_name);
    $statements['course_create']->bindParam(':in_recorders', $in_recorders);
    return $statements['course_create']->execute();
}

/**
 * Returns the info related to a course
 * @param String $course_code
 */
function db_course_read($course_id)
{
    global $statements;
    
    $statements['course_read']->bindParam(':course_code', $course_id);
    
    $statements['course_read']->execute();
    return $statements['course_read']->fetch();
}

/**
 * Returns the users associated to a course
 * @param String $course_code
 */
function db_course_get_users($course_id)
{
    global $statements;
    
    $statements['course_get_users']->bindParam(':course_code', $course_id);
    $statements['course_get_users']->execute();
    
    return $statements['course_get_users']->fetchAll();
}

/**
 * Update a course
 * @param String $course_code
 */
function db_course_update($course_id, $course_name, $in_recorders)
{
    global $statements;
        
    $statements['course_update']->bindParam(':course_code', $course_id);
    $statements['course_update']->bindParam(':course_name', $course_name);
    $statements['course_update']->bindParam(':in_recorders', $in_recorders);
    
    return $statements['course_update']->execute();
}

function course_update_anon($course_id, $anon_access)
{
    global $statements;

    $statements['course_update_anon']->bindParam(':course_code', $course_id);
    $statements['course_update_anon']->bindParam(':anon_access', $anon_access);
    
    return $statements['course_update_anon']->execute();
}

/**
 * Delete course
 * @param $course_code
 */
function db_course_delete($course_id)
{
    global $statements;
    
    $statements['course_delete']->bindParam(':course_code', $course_id);
    
    return $statements['course_delete']->execute();
}

/**
 * Returns the list of (recorder/ezmanager) admins
 */
function db_admins_list()
{
    global $statements;
    
    $statements['users_get_admins']->execute();
    return $statements['users_get_admins']->fetchAll();
}

function db_users_list($user_ID, $surname, $forename, $origin, $is_admin, $order, $limit)
{
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

function db_user_read($user_ID)
{
    global $statements;
    
    $statements['user_read']->bindParam(':user_ID', $user_ID);
    $statements['user_read']->execute();
    
    return $statements['user_read']->fetch();
}

function db_user_get_courses($user_ID)
{
    global $statements;
    
    $statements['user_courses_get']->bindParam(':user_ID', $user_ID);
    $statements['user_courses_get']->execute();
    
    return $statements['user_courses_get']->fetchAll();
}

function db_users_courses_get($course_id, $user_ID)
{
    global $statements;
    
    $statements['users_courses_get']->bindParam(':course_code', $course_id);
    $statements['users_courses_get']->bindParam(':user_ID', $user_ID);
    
    $statements['users_courses_get']->execute();
    return $statements['users_courses_get']->fetch();
}

function users_courses_get_users($course_id)
{
    global $statements;
    
    $statements['users_courses_get_users']->bindParam(':course_code', $course_id);
    $statements['users_courses_get_users']->execute();
    return $statements['users_courses_get_users']->fetchAll();
}

/**
 * Search classroom
 *
 * @global PDO $db_object
 * @param String $room_ID id of the classroom
 * @param String $name of the classroom
 * @param String $ip adresse ip
 * @param int $enabled -1 for both enabled and disabled; 0 for disabled only; 1 for enabled only
 * @param String $colOrder column who must be use to order result
 * @param String $orderSort ASC or DESC
 * @param int $start_elem first element to return
 * @param int $max_elem max elements who must be return
 * @return PDO statement or FALSE on failure
 */
function db_classrooms_search(
    $room_ID,
    $name,
    $ip,
    $enabled,
    $colOrder,
    $orderSort,
        $start_elem,
    $max_elem
) {
    global $db_object;
    
    $strSQL =
       'SELECT DISTINCT SQL_CALC_FOUND_ROWS ' .
                    'classroom.room_ID, ' .
                    'classroom.name, ' .
                    'classroom.IP, ' .
                    'classroom.IP_remote, ' .
                    'classroom.enabled ' .
       'FROM '. db_gettable('classrooms') . ' classroom ';
    
    $valueWhereParam = array();
    $whereParam = array();
    
    if ($room_ID != "") {
        $whereParam[] = "room_ID LIKE ?";
        $valueWhereParam[] = db_sanitize($room_ID);
    }
    
    if ($name != "") {
        $whereParam[] = "name LIKE ?";
        $valueWhereParam[] = db_sanitize($name);
    }
    
    if ($ip != "") {
        $whereParam[] = "IP LIKE ?";
        $valueWhereParam[] = db_sanitize($ip);
    }
    
    if (($enabled == 0 || $enabled == 1) && $enabled != "") {
        $whereParam[] = "enabled = ?";
        $valueWhereParam[] = $enabled;
    }
    
    if (!empty($whereParam)) {
        $strSQL .= " WHERE ";
        $strSQL .= implode(" AND ", $whereParam);
    }
    
    if ($colOrder != "") {
        $strSQL .= " ORDER BY ".$colOrder." ";
        if ($orderSort == "DESC") {
            $strSQL .= " DESC ";
        }
    }
       
    if ($max_elem != "" && $max_elem >= 0) {
        if ($start_elem != "" && $start_elem >= 0) {
            $strSQL .= " LIMIT ".$start_elem.",".$max_elem;
        } else {
            $strSQL .= " LIMIT ".$max_elem;
        }
    }
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($valueWhereParam);
    
    return $reqSQL->fetchAll();
}

/**
 * Returns the name, ID and IP of all the recorders
 */
function db_classrooms_list()
{
    global $statements;

    $statements['classrooms_list']->execute();
    return $statements['classrooms_list']->fetchAll();
}

/**
 * Returns the name, ID and IP of all the enabled recorders
 */
function db_classrooms_list_enabled()
{
    global $statements;

    $statements['classrooms_list_enabled']->execute();
    return $statements['classrooms_list_enabled']->fetchAll();
}

/**
 * Return the IP of a specific room_ID
 *
 * @global array $statements
 * @param string $room_ID
 * @return string ip
 */
function db_classroom_from_name_get_ip($room_ID)
{
    global $statements;

    $statements['classrooms_from_name_get_ip']->bindParam(':room_ID', $room_ID);
    $statements['classrooms_from_name_get_ip']->execute();

    return $statements['classrooms_from_name_get_ip']->fetchAll(PDO::FETCH_COLUMN);
}


/**
 * Sets the "enabled" bit to true or false
 * @global array $statements
 * @param type $room_ID
 * @param type $enabled
 * @return type
 */
function db_classroom_update_enabled($room_ID, $enabled)
{
    global $statements;

    $statements['classroom_update_enabled']->bindParam(':room_ID', $room_ID);
    $statements['classroom_update_enabled']->bindParam(':enabled', $enabled, PDO::PARAM_INT);

    $res = $statements['classroom_update_enabled']->execute();

    return $res;
}
function db_users_in_recorder_get()
{
    global $statements;

    $statements['get_users_in_recorder']->execute();
    return $statements['get_users_in_recorder']->fetchAll();
}
/**
 * Returns the list of users created manually
 */
function db_users_internal_get()
{
    global $statements;

    $statements['get_internal_users']->execute();
    return $statements['get_internal_users']->fetchAll();
}
function db_users_courses_create($course_id, $user_ID)
{
    global $statements;

    if (db_users_courses_get($course_id, $user_ID)) {
        return false;
    }

    $statements['users_courses_create']->bindParam(':course_code', $course_id);
    $statements['users_courses_create']->bindParam(':user_ID', $user_ID);

    if (!$statements['users_courses_create']->execute()) {
        return false;
    }

    // return informations
    global $db_object;
    $user = db_user_read($user_ID);
    if (!$user) {
        return false;
    }

    $course = db_course_read($course_id);
    if (!$course) {
        return false;
    }

    return array('user' => $user, 'course' => $course, 'id' => $db_object->lastInsertId());
}

function db_users_courses_delete($user_course_ID)
{
    global $statements;

    $statements['users_courses_delete']->bindParam(':user_course_ID', $user_course_ID);

    return $statements['users_courses_delete']->execute();
}
function db_users_courses_delete_row($album, $user_course_ID)
{
    global $statements;
        
    $statements['users_courses_delete_row']->bindParam(':user_ID', $user_course_ID);
    $statements['users_courses_delete_row']->bindParam(':course_code', $album);
    
    return $statements['users_courses_delete_row']->execute();
}

function db_unlink_user($user_ID)
{
    global $statements;

    $statements['unlink_user']->bindParam(':user_ID', $user_ID);

    return $statements['unlink_user']->execute();
}

function db_unlink_course($course_id)
{
    global $statements;
    $statements['unlink_course']->bindParam(':course_code', $course_id);
    return $statements['unlink_course']->execute();
}

function db_found_rows()
{
    global $statements;
    $statements['found_rows']->execute();
    $res = $statements['found_rows']->fetch();
    return intval($res[0]);
}

function db_user_create($user_ID, $surname, $forename, $recorder_passwd, $permissions)
{
    global $statements;
    $lowered_user_id = strtolower($user_ID);
    $statements['user_create']->bindParam(':user_ID', $lowered_user_id);
    $statements['user_create']->bindParam(':surname', $surname);
    $statements['user_create']->bindParam(':forename', $forename);
    $statements['user_create']->bindParam(':recorder_passwd', $recorder_passwd);
    $statements['user_create']->bindParam(':permissions', $permissions);
    return $statements['user_create']->execute();
}
function db_user_delete($user_ID)
{
    global $statements;
    $statements['user_delete']->bindParam(':user_ID', $user_ID);
    return $statements['user_delete']->execute();
}

function db_user_update($user_ID, $surname, $forename, $recorder_passwd, $permissions)
{
    global $statements;
    if (empty($recorder_passwd)) {
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

//return number of line affected, or false on error
function db_user_set_recorder_passwd($user_ID, $recorder_passwd)
{
    $des_seed = chr(rand(33, 126)) . chr(rand(33, 126));
    $encrypted_passwd = crypt($recorder_passwd, $des_seed);
        
    global $statements;
    $statements['user_update_recorder_passwd']->bindParam(':user_ID', $user_ID);
    $statements['user_update_recorder_passwd']->bindParam(':passwd', $encrypted_passwd);
    $ok = $statements['user_update_recorder_passwd']->execute();
    if(!$ok)
        return false;
    
    return $statements['user_update_recorder_passwd']->rowCount();
}

//return number of line affected, or false on error
function db_user_enable_recorder_for_all_courses($user_ID) 
{
    global $statements;
    $statements['enable_recorders_for_all_courses']->bindParam(':userID', $user_ID);
    $ok = $statements['enable_recorders_for_all_courses']->execute();
    if(!$ok)
        return false;
    
    return $statements['enable_recorders_for_all_courses']->rowCount();
}

//return number of courses with recorders for this users, or false on failure
function db_user_get_courses_with_recorder($user_ID) 
{
    global $statements;
    $statements['user_get_courses_with_recorder']->bindParam(':userID', $user_ID);
    $ok = $statements['user_get_courses_with_recorder']->execute();
    if(!$ok)
        return false;
    
    return $statements['user_get_courses_with_recorder']->fetchAll();
}

/**
 * Logs the action 'action' performed on table 'table'
 * @param type $table
 * @param type $action
 */
function db_log($table, $action, $author)
{
    global $statements;
    $statements['log_action']->bindParam(':table', $table);
    $statements['log_action']->bindParam(':message', $action);
    $statements['log_action']->bindParam(':author', $author);
    return $statements['log_action']->execute();
}

function db_logs_get($date_start, $date_end, $table, $author, $startElem = -1, $limit = -1)
{
    global $db_object;
    $query = 'SELECT DISTINCT SQL_CALC_FOUND_ROWS `time`, `table`, message, author FROM '.  db_gettable('admin_logs');
    $where = '';
    if (!empty($date_start)) {
        $where .= 'time >= \''.$date_start.' 00:00:00\'';
    }
    if (!empty($date_end)) {
        if (!empty($where)) {
            $where .= ' AND ';
        }
        $where .= 'time <= \''.$date_end.' 00:00:00\'';
    }
    if (!empty($table)) {
        if ($table != 'all') {
            if (!empty($where)) {
                $where .= ' AND ';
            }
            $where .= '`table` LIKE \''.db_gettable($table).'\'';
        }
    }
    if (!empty($author)) {
        if (!empty($where)) {
            $where .= ' AND ';
        }
        $where .= 'author LIKE %'.$author.'%';
    }
    $fullQuery = $query;
    if (!empty($where)) {
        $fullQuery .= ' WHERE ' . $where;
    }
    
    $fullQuery .= ' ORDER BY `time` DESC';
    
    if ($startElem != -1 && $limit != -1) {
        $fullQuery .= ' LIMIT ' . $startElem . ', '.$limit;
    }
    
    
    return $db_object->query($fullQuery);
}

function db_classroom_create($room_ID, $name, $ip, $ip_remote, $enabled)
{
    global $statements;
    $statements['classroom_create']->bindParam(':room_ID', $room_ID);
    $statements['classroom_create']->bindParam(':name', $name);
    $statements['classroom_create']->bindParam(':ip', $ip);
    $statements['classroom_create']->bindParam(':ip_remote', $ip_remote);
    $statements['classroom_create']->bindParam(':enabled', $enabled);
    return $statements['classroom_create']->execute();
}

function db_classroom_update($ID, $room_ID, $name, $ip, $ip_remote)
{
    global $statements;
    $statements['classroom_update']->bindParam(':ID', $ID);
    $statements['classroom_update']->bindParam(':room_ID', $room_ID);
    $statements['classroom_update']->bindParam(':name', $name);
    $statements['classroom_update']->bindParam(':ip', $ip);
    $statements['classroom_update']->bindParam(':ip_remote', $ip_remote);
    return $statements['classroom_update']->execute();
}

function db_classroom_delete($room_ID)
{
    global $statements;
    $statements['classroom_delete']->bindParam(':room_ID', $room_ID);
    return $statements['classroom_delete']->execute();
}

function db_stream_create(
    $cours_id,
    $asset,
    $classroom,
    $record_type,
    $netid,
    $stream_name,
    $token="",
    $module_type,
    $ip,
    $status,
    $quality,
    $protocol,
    $server="",
    $port=""
) {
    global $statements;
    $statements['stream_create']->bindParam(':cours_id', $cours_id);
    $statements['stream_create']->bindParam(':asset', $asset);
    $statements['stream_create']->bindParam(':classroom', $classroom);
    $statements['stream_create']->bindParam(':record_type', $record_type);
    $statements['stream_create']->bindParam(':netid', $netid);
    $statements['stream_create']->bindParam(':stream_name', $stream_name);
    $statements['stream_create']->bindParam(':token', $token);
    $statements['stream_create']->bindParam(':module_type', $module_type);
    $statements['stream_create']->bindParam(':ip', $ip);
    $statements['stream_create']->bindParam(':status', $status);
    $statements['stream_create']->bindParam(':quality', $quality);
    $statements['stream_create']->bindParam(':protocol', $protocol);
    $statements['stream_create']->bindParam(':server', $server);
    $statements['stream_create']->bindParam(':port', $port);
        
    return $statements['stream_create']->execute();
}

function db_stream_update_status($course, $asset, $module_type, $status)
{
    global $statements;
    $statements['stream_update_status']->bindParam(':course', $course);
    $statements['stream_update_status']->bindParam(':asset', $asset);
    $statements['stream_update_status']->bindParam(':module_type', $module_type);
    $statements['stream_update_status']->bindParam(':status', $status);
    
    return $statements['stream_update_status']->execute();
}

/* return an array in the form if ... ?
 * Return null of no streams were found
 */
function db_get_stream_info($cours_id, $asset)
{
    global $statements;
    $statements['get_stream_info']->bindParam(':cours_id', $cours_id);
    $statements['get_stream_info']->bindParam(':asset', $asset);
    $statements['get_stream_info']->execute();
    $res = $statements['get_stream_info']->fetchAll();
    
    // generate a formated table
    $infos = null;
    for ($i=0; $i < count($res); $i++) {
        $infos[$cours_id][$asset]['classroom']                            = $res[$i]['classroom'];
        $infos[$cours_id][$asset]['netid']                                = $res[$i]['netid'];
        $infos[$cours_id][$asset]['record_type']                          = $res[$i]['record_type'];
        $infos[$cours_id][$asset]['stream_name']                          = $res[$i]['stream_name'];
        if (isset($res[$i]['token']) && $res[$i]['token'] != '') {
            $infos[$cours_id][$asset]['token']                            = $res[$i]['token'];
        }
        $infos[$cours_id][$asset][$res[$i]['module_type']]['ip']          = $res[$i]['ip'];
        $infos[$cours_id][$asset][$res[$i]['module_type']]['status']      = $res[$i]['status'];
        $infos[$cours_id][$asset][$res[$i]['module_type']]['quality']     = $res[$i]['quality'];
        $infos[$cours_id][$asset][$res[$i]['module_type']]['protocol']    = $res[$i]['protocol'];
        if (isset($res[$i]['server'])) {
            $infos[$cours_id][$asset][$res[$i]['module_type']]['server']  = $res[$i]['server'];
        } else {
            $infos[$cours_id][$asset][$res[$i]['module_type']]['server']  = null;
        }
        
        if (isset($res[$i]['port'])) {
            $infos[$cours_id][$asset][$res[$i]['module_type']]['port']    = $res[$i]['port'];
        } else {
            $infos[$cours_id][$asset][$res[$i]['module_type']]['port']    = null;
        }
    }
    
    return $infos;
}
function db_in_recorder_update($course,$value)
{
    global $statements;
    $statements['in_recorder_update']->bindParam(':course_code', $course);
    $statements['in_recorder_update']->bindParam(':in_recorders', $value);
    
    return $statements['in_recorder_update']->execute();
}