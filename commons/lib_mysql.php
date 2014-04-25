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

$db_object = null; 
$statements = null;
$db_prepared = false;

/**
 * Verifies that the DB exists and answers correctly with the given credentials
 */
function db_ping($host, $login, $passwd, $dbname) {
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname", $login, $passwd);
    }
    catch(PDOException $e) {
        return false;
    }
    
    unset($db);
    return true;
}

/**
/*
 * Opens a connection to the DB and prepares the statements.
 * Returns a PDO object representing this connection.
 *
 * @param string $dbhost
 * @param string $dbname
 * @param string $login
 * @param string $passwd
 * @param string $db_prefix
 * @return db_object
 */
function db_prepare($db_host, $db_name, $db_login, $db_passwd, $db_prefix = '') {
    global $db_object;
    global $db_prepared;
    
    try {
        $db_object = new PDO("mysql:host=$db_host;dbname=$db_name", $db_login, $db_passwd);
    } catch (PDOException $e){
        return false;
    }
    
    db_statement_prepare('course_list',
        'SELECT DISTINCT ' . 
            $db_prefix.'courses' . '.course_code AS mnemonic, ' .
            $db_prefix.'courses'  . '.course_name AS label ' .
        'FROM ' . $db_prefix.'courses'  . ' ' .
        'ORDER BY mnemonic ASC'
    );
    
    db_statement_prepare('user_courses_get', 
        'SELECT DISTINCT ' .
            $db_prefix.'courses'.'.course_code AS mnemonic, '.
            $db_prefix.'courses'.'.course_name AS label '.
        'FROM '.  $db_prefix.'courses'.' ' .
        'INNER JOIN '.  $db_prefix.'users_courses' .' ON '.  $db_prefix.'courses'.'.course_code = '.  $db_prefix.'users_courses'.'.course_code '.
        'WHERE '. $db_prefix.'users_courses.user_ID = :user_ID '.
        'ORDER BY ' . $db_prefix.'courses.course_code'
    );
    
    $db_prepared = true;
    
    return $db_object;
}

// 
//   
/**
 * DB_CLOSE
 *
 * Closes instance associated to db object.
 * 
 */
function db_close() {
    global $db_object;
    global $statements;
    global $db_prepared;
    
    $db_object = null;
    unset($statements); 
    $db_prepared = false;
}

/**
 * Creates a prepared statement $statement and saves it in the global $statements array.
 * @global array $statements
 * @global string $db_object
 * @param type $statement_name
 * @param type $statement 
 */
function db_statement_prepare($statement_name, $statement){
    global $statements;
    global $db_object;
    
    $statements[$statement_name] = $db_object->prepare($statement);
}

/**
 * Transforms HTML-friendly input into SQL-friendly input.
 * @param type $input
 * @return type 
 */
function db_sanitize($input) {
    return (empty($input)) ? '%' : '%'.$input.'%';
    //return $input;
}

//---------------------------
// PAGE-SPECIFIC FUNCTIONS
//---------------------------

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
function db_courses_list() {
    global $statements;
    
    $statements['course_list']->execute();
    return $statements['course_list']->fetchAll();  
}

/**
 * Retrieves all courses for a given user
 * @global array $statements
 * @param type $user_ID
 * @return type
 */
function db_user_courses_get($user_ID) {
    global $statements;
        
    $statements['user_courses_get']->bindParam(':user_ID', $user_ID);
    $statements['user_courses_get']->execute();
    
    return $statements['user_courses_get']->fetchAll();
}
?>
