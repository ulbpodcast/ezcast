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

    $stmt_array = event_statements_get();
    db_prepare($stmt_array);
}
 

function event_statements_get() {
    return array(
            'get_all_event' =>
                    'SELECT (`asset`, `origin`, `asset_classroom_id`, `asset_course`, `asset_author`,'
                            . ' `asset_cam_slide`, `classroom_event_id`, `event_time`, `type_id`, `context`, `loglevel`, `message`) ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' ' .
                    'ORDER BY event_time, classroom_event_id',
        
            'get_event_loglevel_most' =>
                    'SELECT MIN(loglevel) AS max_loglevel ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' ' .
                    'WHERE asset = :asset',
        
            'get_record_after_date' =>
                    'SELECT `asset`, `start_time`, `end_time`, `asset_classroom_id`, '
                        . '`asset_course`, `asset_author`, `asset_cam_slide` ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' ' .
                    'WHERE asset_classroom_id = :asset_classroom_id AND '
                        . 'start_time >= :time_limit AND end_time IS NOT NULL',
        
            'status_insert' => 
                    'INSERT INTO ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' ' .
                    '(asset, status, author, status_time, description) ' . 
                    'VALUES(:asset, :status, :author, NOW(), :description)',
        
            'asset_parent_add' =>
                'INSERT INTO ' . db_gettable(ServerLogger::EVENT_ASSET_PARENT_TABLE_NAME) . ' ' .
                '(asset, parent_asset) ' .
                'VALUES(:asset, :parent_asset)',
        
            'asset_status_exist' =>
                'SELECT 1 ' .
                'FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' ' .
                'WHERE asset = :asset LIMIT 1',
        
            'asset_parent_remove' =>
                'DELETE FROM ' . db_gettable(ServerLogger::EVENT_ASSET_PARENT_TABLE_NAME) . ' ' .
                'WHERE asset = :asset'
        
        );
}

/**
 * Return all events (BIG!)
 * @global array $statements
 */
function db_event_get_all() {
    global $statements;

    $statements['get_all_event']->execute();
    return $statements['get_all_event']->fetchAll();
}

/**
 * Get the most important loglevel for a specific asset
 * (As an integer, most important is lowest)
 * 
 * @global Array $statements slq request
 * @param String $asset name of the asset
 * @return int the most important loglevel (or -1 if not exist)
 */
function db_event_get_event_loglevel_most($asset) {
    global $statements;
    
    $statements['get_event_loglevel_most']->bindParam(':asset', $asset);
    
    $statements['get_event_loglevel_most']->execute();
    $res = $statements['get_event_loglevel_most']->fetch();
    if(array_key_exists('max_loglevel', $res) && $res['max_loglevel'] != "") {
        return $res['max_loglevel'];
    }
    return -1;
}

/**
 * Get all record after given date in a specific classroom
 * 
 * @global PDO $statements prepared request list
 * @param String $start_date all record after this date
 * @param String $classroom_id id of the classroom
 * @param String $courses name of the course
 * @param String $teacher name of the teacher
 * @return Array all restult
 */
function db_event_get_record_after_date($start_date, $classroom_id = "", $courses = "", $teacher = "", $cam_slide = "") {
    global $db_object;
    
    $strSQL = 'SELECT `asset`, `start_time`, `end_time`, `asset_classroom_id`, '
                        . '`asset_course`, `asset_author`, `asset_cam_slide` ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME);
    
    $whereParam = array('start_time >= :time_limit AND end_time IS NOT NULL');
    $valueWhereParam = array('time_limit' => $start_date);
    
    if($classroom_id != "") {
        $whereParam[] = 'asset_classroom_id = :asset_classroom_id';
        $valueWhereParam['asset_classroom_id'] = $classroom_id;
    }
    
    if($courses != "") {
        $whereParam[] = 'asset_course = :courses';
        $valueWhereParam['courses'] = $courses;
    }
    
    if($teacher != "") {
        $whereParam[] = 'asset_author = :teacher';
        $valueWhereParam['teacher'] = $teacher;
    }
    
    if($cam_slide != "") {
        $whereParam[] = 'asset_cam_slide = :cam_slide';
        $valueWhereParam['cam_slide'] = $cam_slide;
    }
    
    
    if(!empty($whereParam)) {
        $strSQL .= " WHERE ";
    }
    $strSQL .= implode(" AND ", $whereParam);
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($valueWhereParam);
    
    return $reqSQL->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Get event
 * 
 * @global PDO $db_object
 * @param String $asset
 * @param String $origin
 * @param String $asset_classroom_id
 * @param String $asset_course
 * @param String $asset_author
 * @param String $first_event_time
 * @param String $last_event_time
 * @param String $type_id
 * @param String $context
 * @param Array<Int> $loglevel
 * @param String $message
 * @param String $colOrder
 * @param String $orderSort
 * @param Integer $start_elem
 * @param Integer $max_elem
 * @return Array with result
 */
function db_event_get($asset, $origin, $asset_classroom_id, $asset_course, $asset_author,
        $first_event_time, $last_event_time, $type_id, $context,
        $loglevel, $message, 
        $colOrder = "event_time", $orderSort = "ASC",
        $start_elem = "", $max_elem = "") {
    
    global $db_object;
    
    $strSQL = 'SELECT SQL_CALC_FOUND_ROWS events.* ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' events ';
    
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
        $whereParam[] = "asset_classroom_id LIKE ?";
        $valueWhereParam[] = db_sanitize($asset_classroom_id);
    }
    
    if($asset_course != "") {
        $whereParam[] = "asset_course LIKE ?";
        $valueWhereParam[] = db_sanitize($asset_course);
    }
    
    if($asset_author != "") {
        $whereParam[] = "asset_author LIKE ?";
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
    
    if($loglevel != "" && !empty($loglevel) && $loglevel[0] != NULL) {
        $tempWhereParam = array();
        foreach($loglevel as $lvl) {
            $tempWhereParam[] = "loglevel = ?";
            $valueWhereParam[] = $lvl;
        }
        $whereParam[] = "(".implode(" OR ", $tempWhereParam).")";
    }
    
    if($message != "") {
        $whereParam[] = "message LIKE ?";
        $valueWhereParam[] = db_sanitize($message);
    }
    
    if(!empty($whereParam)) {
        $strSQL .= " WHERE ";
    }
    $strSQL .= implode(" AND ", $whereParam);
    
    $strSQL .= " ORDER BY ";
    if($colOrder != "") {
        $strSQL .= $colOrder.' ';
        if($orderSort == "DESC") {
            $strSQL .= " DESC ";
        } 
        $strSQL .= ', ';
    }
    $strSQL .= 'classroom_event_id '; //order by classroom_event_id too so that we can order events which happened in the same second
    
    if($max_elem != "" && $max_elem >= 0) {
        if($start_elem != "" && $start_elem >= 0) {
            $strSQL .= " LIMIT ".$start_elem.",".$max_elem;
        } else {
            $strSQL .= " LIMIT ".$max_elem;
        }
    }
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($valueWhereParam);
    
    return $reqSQL->fetchAll();
}


function db_event_status_get($firtDate, $endDate, $typeStatus, 
        $asset, $colOrder = "status_time", $orderSort = "ASC", 
        $start_elem = "", $max_elem = "") {
    
    global $db_object;
    
    $strSQL = 'SELECT SQL_CALC_FOUND_ROWS status.* ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME). ' status ';
    
    $whereParam = array();
    $valueWhereParam = array();
    
    if($firtDate != "") {
        $whereParam[] = "status_time >= ?";
        $valueWhereParam[] = $firtDate;
    }
    
    if($endDate != "") {
        $whereParam[] = "status_time <= ?";
        $valueWhereParam[] = $endDate;
    }
    
    if($typeStatus != "" && !empty($typeStatus) && $typeStatus[0] != NULL) {
        $tempWhereParam = array();
        foreach($typeStatus as $status) {
            $tempWhereParam[] = "status = ?";
            $valueWhereParam[] = $status;
        }
        $whereParam[] = "(".implode(" OR ", $tempWhereParam).")";
    }
    
    
    if($asset != "") {
        $whereParam[] = "asset = ?";
        $valueWhereParam[] = $asset;
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
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($valueWhereParam);
    
    return $reqSQL->fetchAll();
    
}

function db_event_get_asset_parent($asset = "") {
    global $db_object;
    
    $whereParam = array();
    $strSQL = 'SELECT asset, parent_asset FROM ' . db_gettable(ServerLogger::EVENT_ASSET_PARENT_TABLE_NAME);
    if($asset != "") {
        $strSQL .= " WHERE asset = :asset OR parent_asset = :asset";
        $whereParam[':asset'] = $asset;
    }
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($whereParam);
    
    return $reqSQL->fetchAll();
}

function db_event_status_add($asset, $status, $message = "", $author = "system") {
    global $statements;
    
    $statements['status_insert']->bindParam(':asset', $asset);
    $statements['status_insert']->bindParam(':status', $status);
    $statements['status_insert']->bindParam(':author', $author);
    $statements['status_insert']->bindParam(':description', $message);
    
    $statements['status_insert']->execute();
}

function db_event_asset_parent_remove($asset) {
    global $statements;
    
    $statements['asset_parent_remove']->bindParam(':asset', $asset);
    $statements['asset_parent_remove']->execute();
}


function db_event_asset_parent_add($asset, $asset_parent) {
    global $statements;
    
    $statements['asset_parent_add']->bindParam(':asset', $asset);
    $statements['asset_parent_add']->bindParam(':parent_asset', $asset_parent);
    
    $statements['asset_parent_add']->execute();
}

/**
 * Check if an asset exist in the event_status table
 * 
 * @param String $asset to test
 * @return True if exist
 */
function db_event_asset_status_exist($asset) {
    global $statements;
    
    $statements['asset_status_exist']->bindParam(':asset', $asset);
    $statements['asset_status_exist']->execute();
    
    $res = $statements['asset_status_exist']->fetchAll();
    return !empty($res);
}

function db_event_asset_infos_get($classroom = "") {
    global $db_object;
    
    $argument = array();
    
    $strSQL = 'SELECT asset_info.asset, asset_info.start_time, asset_info.end_time, 
                    asset_info.asset_classroom_id, status.status
                FROM ezcast_test_asset_infos asset_info
                    LEFT JOIN ezcast_test_event_status status
                        ON status.asset = asset_info.asset';
    
    if($classroom != "") {
        $strSQL .= ' WHERE asset_info.asset_classroom_id = :asset_classroom_id';
        $argument['asset_classroom_id'] = $classroom;
    }
    
    $strSQL .= ' GROUP BY asset_info.asset
                HAVING MAX(status.status_time) OR (status IS NULL)';
    
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($argument);
    return $reqSQL->fetchAll(PDO::FETCH_ASSOC);
}

