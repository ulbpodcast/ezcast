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

/**
 * open a database
 *
 * connect to sgbd and select a database
 * Returns connection handle.
 *
 * @param string $dbhost
 * @param string $dbname
 * @param string $login
 * @param string $passwd
 * @return db_handle
 */
function db_open($db_host, $db_name, $db_login, $db_passwd) {
    $db = mysql_connect($db_host, $db_login, $db_passwd);
    if (!$db) {
        return false;
    }
    if (!mysql_select_db($db_name, $db)) {
        echo 'Could not select database';
        return false;
    }
    return $db;
}

// 
//   
/**
 * DB_CLOSE
 *
 * Closes instance associated to connection handle.
 * 
 * @param db_handle $db
 */
function db_close($db) {
    mysql_close($db);
}

/**
 *
 * Executes select sql statement in specified database.
 * Returns array of associative arrays mapped on queried records.
 *
 * @param string $db
 * @param string $sql
 * @param string $option
 * @return array_of_assoc_arrays
 */
function db_select($db, $sql, $option = "assoc") {
    //print $sql;
    $res = mysql_query($sql, $db);
    if (!$res) {
        print mysql_error($db);
        return false;
    }
    $result = array();
    $idx = 0;
    if ($option == "assoc")
        $option = MYSQL_ASSOC; //return associative array
    else
        $option = MYSQL_NUM; //return array

    while ($result[$idx] = mysql_fetch_array($res, $option)) {
        $idx+=1;
    }
    unset($result[$idx]);
    mysql_free_result($res);
    return $result;
}

/**
 *
 * Executes select sql statement in specified database and limit number of results.
 * Returns array of associative arrays mapped on queried records.
 *
 * @param string $db
 * @param string $sql
 * @param integer $limit return max elements
 * @param integer $offset start at row #
 *  * @param string $option
 * @return array_of_assoc_arrays
 */
function db_select_limit($db, $sql, $limit, $offset, $option = "assoc") {
    $sqllimit = " LIMIT $limit";
    $sqloffset = " OFFSET $offset";
    $sql = $sql . $sqllimit . $sqloffset;
    //print $sql;
    $res = mysql_query($sql, $db);
    if (!$res) {
        print mysql_error($db);
        return false;
    }
    $result = array();
    $idx = 0;
    if ($option == "assoc")
        $option = MYSQL_ASSOC; //return associative array
    else
        $option = MYSQL_NUM; //return array

    while ($result[$idx] = mysql_fetch_array($res, $option)) {
        $idx+=1;
    }
    unset($result[$idx]);
    mysql_free_result($res);
    return $result;
}

/**
 * DB_SELECT_nofetch
 *
 *  Executes select sql statement in specified database.
 *  Returns pointer to result (usable by mysql_fetch_array).
 *  after this run db_select_numrows($res), db_select_fetch_next($res), db_select_close($res)
 *
 *
 * @param string $db
 * @param string $sql
 * @param string $option
 * @return handle_to_query_result
 */
function db_select_nofetch($db, $sql, $option = "assoc") {
    //print $sql;
    $res = mysql_query($sql, $db);
    if (!$res) {
        print mysql_error($db);
        return false;
    }

    return $res;
}

/**
 *
 * returns the number of elements impacted/returned by the query
 * @param query resource $res 
 * @return interger
 */
function db_select_numrows($res) {
    //Look for the number of returned rows
    $nbrows = mysql_num_rows($res);
    return $nbrows;
}

// DB_SELECT_fetchnext
//   Executes select sql statement in specified database.
//   Returns  associative array mapped on queried records.

/**
 *
 * fetches next record from the result of db_select_nofetch().
 * Returns an associative array mapped on queried records.
 *
 * @param handle_to_query_result $dbres
 * @return assoc_array
 */
function db_select_fetchnext($dbres) {
    $result = mysql_fetch_assoc($dbres);
    return $result;
}

/**
 *
 * Returns remaining result of db_select_nofetch array of associative arrays mapped on queried records.
 *
 * @param  handle_to_query_result $dbres
 * @param string $option
 * @return array_of_assoc_arrays
 */
function db_select_fetchall($dbres, $option = "assoc") {
    $result = array();
    $idx = 0;
    if ($option == "assoc")
        $option = MYSQL_ASSOC; //return associative array
    else
        $option = MYSQL_NUM; //return array

    while ($result[$idx] = mysql_fetch_array($dbres, $option)) {
        $idx+=1;
    }
    unset($result[$idx]);
    if ($idx > 0 && $res)
        mysql_free_result($res);
    return $result;
}

/**
 *
 * discards structure from db_select_nofetch()
 * @param handle_to_query_result $dbres
 * @return bool
 */
function db_select_close($dbres) {
    $result = mysql_free_result($dbres);
    return $result;
}

/**
 * DB_EXEC
 *
 *  Executes sql statement in specified database.
 * Return errorflag.
 *
 * @return bool
 */
function db_exec($db, $sql) {
    $res = mysql_query($sql, $db);
    if (!$res) {
        file_put_contents("/var/lib/ezcast/log/threads.log", "NO_RESULT : ".mysql_error($db).PHP_EOL,FILE_APPEND);
        print mysql_error($db);
        return true;
    }
    return false;
}

/**
 *
 * insert into a table with an auto number
 * eg: db_insert_seq($cnx,$value,users,"login,password","'jsmith','XxeZ3E'","user_id")
 * this will insert the sequence seq_user_id
 * @return result if>0error
 * @param db_handle $connexion
 * @param int &$autonumvalue  value of the inserted autonumber
 * @param string $table  table name
 * @param string $fieldlist  comma separated lists of fields
 * @param string $valuelist
 * @param string $autofield  fieldname of autonumber
 * @param string $seqname  name of oracle sequence (SEQ_$autofield by default)
 * 
 */
function db_insert_autonumber($db, &$autonumvalue, $table, $fieldlist, $valuelist, $autofield, $seqname = "") {


    $fieldlist = $fieldlist;
    $valuelist = $valuelist;
    $sql = "INSERT INTO `$table` ($fieldlist) VALUES ($valuelist)";
    $result = db_exec($db, $sql);
    if (!$result)
        $autonumvalue = mysql_insert_id($db);
    if ($result)
        trigger_error("error in call to db_exec $result", E_USER_WARNING);
    return $result;
}

function db_escape_string($str) {
    return mysql_real_escape_string($str);
}

/**
 *
 * Single quotes in strings need to be escaped by single quotes prior to
 *  inserting in DB.
 * @param string $quoted_string
 * @return string string with escaped quotes
 */
function escape_s_quotes($quoted_string) {
    return str_replace("'", "''", $quoted_string);
}

/**
 * @return string
 * @param csv_field_string $string
 * @desc escape csv  special chars in a field (add backslash: ',' ->'\;' '\'->'\\')
 */
function escape_csv($string) {
    $string = str_replace(array('\\', ',', "\n", "\r"), array('\\\\', '\\;', '\\n', '\\r'), $string);
    return $string;
}

/**
 * @return string
 * @param csv_field_string $string
 * @desc unescapesescape csv  special chars in a field ( backslash: '' ->'\' '\'->'\\')
 */
function unescape_csv($string) {
    $string = str_replace(array('\\n', '\\r', '\\;', '\\\\'), array("\n", "\r", ';', '\\'), $string);
    return $string;
}

// STRIP_QUOTES
//
/**
 *
 * Single quotes (if present) are stripped EVERYWHERE from the string.
 * Returns string without quotes.
 * @param string $quoted_string
 * @return string
 */
function strip_quotes($quoted_string) {
    return str_replace("'", "", $quoted_string);
}

?>
