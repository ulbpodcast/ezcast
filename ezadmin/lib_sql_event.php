<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
*            Detobel Rémy <rdetobel@ulb.ac.be>
* 	     Arnaud Wijns <awijns@ulb.ac.be>
*            Antoine Dewilde
*            Thibaut Roskam
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



require_once '../commons/lib_database.php';

if(file_exists('config.inc')) {
    include_once 'config.inc';

    db_prepare(event_statements_get());
}
 

function event_statements_get() {
    return array(
            'get_all_event' =>
                    'SELECT * ' .
                    'FROM ' . db_gettable('events'). ' ' .
                    'ORDER BY event_time'
        );
}

/**
 * Return all the event list (without event status)
 * @global array $statements
 */
function db_event_get_all() {
    global $statements;

    $statements['get_all_event']->execute();
    return $statements['get_all_event']->fetchAll();
}

function db_event_get($asset, $origin, $asset_classroom_id, $asset_course, $asset_author,
        $first_event_time, $last_event_time, $type_id, $context,
        $loglevel, $message, 
        $colOrder = "event_time", $orderSort = "ASC",
        $start_elem = "", $max_elem = "") {
    
    global $db_object;
    
    $strSQL = 'SELECT SQL_CALC_FOUND_ROWS events.* ' .
                    'FROM ' . db_gettable('events'). ' events ';
    
    $whereParam = array();
    $valueWhereParam = array();
    
    if($asset != "") {
        $whereParam[] = "asset = ?";
        $valueWhereParam[] = $asset;
    }
    
    if($origin != "") {
        $whereParam[] = "origin = ?";
        $valueWhereParam[] = $origin;
    }
    
    if($asset_classroom_id != "") {
        $whereParam[] = "asset_classroom_id = ?";
        $valueWhereParam[] = db_sanitize($asset_classroom_id);
    }
    
    if($asset_course != "") {
        $whereParam[] = "asset_course = ?";
        $valueWhereParam[] = db_sanitize($asset_course);
    }
    
    if($asset_author != "") {
        $whereParam[] = "asset_author = ?";
        $valueWhereParam[] = db_sanitize($asset_author);
    }
    
    if($first_event_time != "") {
        $whereParam[] = "event_time >= ?";
        $valueWhereParam[] = $first_event_time;
    }
    
    if($last_event_time != "") {
        $whereParam[] = "event_time <= ?";
        $valueWhereParam[] = $last_event_time;
    }
    
    if($type_id != "") {
        $whereParam[] = "type_id = ?";
        $valueWhereParam[] = $type_id;
    }
    
    if($context != "") {
        $whereParam[] = "context LIKE ?";
        $valueWhereParam[] = db_sanitize($context);
    }
    
    if($loglevel != "") {
        $whereParam[] = "loglevel = ?";
        $valueWhereParam[] = $loglevel;
    }
    
    if($message != "") {
        $whereParam[] = "message LIKE ?";
        $valueWhereParam[] = db_sanitize($message);
    }
    
    if(!empty($whereParam)) {
        $strSQL .= " WHERE ";
    }
    $strSQL .= implode(" AND ", $whereParam);
    if($colOrder != "") {
        $strSQL .= " ORDER BY ".$colOrder." ";
        if($orderSort == "DESC") {
            $strSQL .= " DESC ";
        }
    }
    
    if($max_elem != "" && $max_elem >= 0) {
        if($start_elem != "" && $start_elem >= 0) {
            $strSQL .= " LIMIT ".$start_elem.",".$max_elem;
        } else {
            $strSQL .= " LIMIT ".$max_elem;
        }
    }
    
    echo $strSQL;
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($valueWhereParam);
    
    return $reqSQL->fetchAll();
}

