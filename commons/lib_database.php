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
 * @package ezcast.commons.lib.database
 */
if(file_exists(__DIR__.'/config.inc'))
    include_once __DIR__.'/config.inc'; //include instead of require because this file is used in installation where config may not be create yet

// GLOBALS
$db_object = null;
//contains all prepared statements. Other libs may add new statement to it.
$statements = null;
$db_prepared = false;

//-------------------
// GENERAL FUNCTIONS
//-------------------

/**
 * Verifies that the DB exists and answers correctly with the given credentials
 */
function db_ping($type, $host, $login, $passwd, $dbname)
{
    try {
        $db = new PDO("$type:host=$host;dbname=$dbname;charset=utf8", $login, $passwd);
    } catch (PDOException $e) {
        return false;
    }

    unset($db);
    return true;
}

/*
 * Opens a connection to the DB and prepares the statements.
 * Will add given statements to global $statements object.
 * Returns a PDO object representing this connection.
 * Throws an exception if connection failed
 */

function db_prepare(&$stmt_array = array())
{
    global $db_object;
    global $db_type;
    global $db_host;
    global $db_login;
    global $db_passwd;
    global $db_name;
    global $db_prepared;
    global $statements;
    global $debug_mode;
    
    if ($db_object == null) {
        try {
            $db_object = new PDO("$db_type:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_passwd);
        } catch (PDOException $e) {
            throw new Exception("Could not connect to database $db_host, $db_name with login $db_login");
        }
    }
    
    foreach ($stmt_array as $stmt_name => $stmt) {
        db_statement_prepare($stmt_name, $stmt);
    }

    $db_prepared = true;
    $stmt_array = $statements;

    if ($debug_mode) {
        $db_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    return $db_object;
}

function db_close()
{
    global $db_object;
    global $statements;
    global $db_prepared;

    $db_object = null;
    unset($statements);
    $db_prepared = false;
}

function db_ready()
{
    global $db_prepared;

    return $db_prepared;
}

/**
 * Creates a prepared statement $statement and saves it in the global $statements array.
 * @global array $statements
 * @global string $db_object
 * @param type $statement_name
 * @param type $statement
 */
function db_statement_prepare($statement_name, $statement)
{
    global $statements;
    global $db_object;

    $statements[$statement_name] = $db_object->prepare($statement);
}

/**
 * Transforms HTML-friendly input into SQL-friendly input.
 * @param type $input
 * @return type
 */
function db_sanitize($input)
{
    return (empty($input)) ? '%' : '%' . $input . '%';
}

function db_gettable($tableID)
{
    global $db_prefix;
    return $db_prefix . $tableID;
}
