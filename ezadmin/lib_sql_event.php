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
                    'SELECT (`asset`, `origin`, `classroom_event_id`, `event_time`, ' .
                    '`type_id`, `context`, `loglevel`, `message`) ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' ' .
                    'ORDER BY event_time, classroom_event_id',
        
            'get_event_loglevel_most' =>
                    'SELECT MIN(loglevel) AS max_loglevel ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' ' .
                    'WHERE asset = :asset',
        
            'get_record_after_date' =>
                    'SELECT `asset`, `start_time`, `end_time`, `classroom_id`, '
                        . '`course`, `author`, `cam_slide` ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' ' .
                    'WHERE classroom_id = :asset_classroom_id AND '
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
                'WHERE asset = :asset',
        
            'status_last_insert' => 
                'SELECT status_time ' .
                'FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' '.
                'ORDER BY status_time DESC ' .
                'LIMIT 1',
        
            'status_nbr_success' =>
                'SELECT COUNT(*) AS total ' .
                'FROM ' .  db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' '.
                'WHERE (status = "auto_success" OR status = "auto_success_errors" ' .
                'OR status = "auto_success_warnings" OR status = "manual_ok" OR ' .
                'status = "manual_partial_ok") AND status_time >= :start_date AND ' .
                'status_time <= :end_date',
        
            'status_nbr_error' =>
                'SELECT COUNT(*) AS total ' .
                'FROM ' .  db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' '.
                'WHERE (status = "auto_failure" OR status = "manual_failure") '. 
                'AND status_time >= :start_date AND ' .
                'status_time <= :end_date',
        
            'status_date_asset' =>
                'SELECT status, status_time ' .
                'FROM ' .  db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME) . ' '.
                'WHERE status_time >= :start_date AND ' .
                'status_time <= :end_date',
        
            'asset_info_camslide' => 
                'SELECT cam_slide, COUNT(cam_slide) AS total_type '.
                'FROM ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' ' .
                'WHERE start_time >= :start_date AND end_time <= :end_date ' .
                ' AND cam_slide != "" '.
                'GROUP BY cam_slide'
        
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
 * @global PDO $db_object
 * 
 * @param String $start_date all record after this date
 * @param String $classroom_id id of the classroom
 * @param String $courses name of the course
 * @param String $teacher name of the teacher
 * @return Array all restult
 */
function db_event_get_record_after_date($start_date, $classroom_id = "", $courses = "", $teacher = "", $cam_slide = "", $not_in_courses = false) {
    global $db_object;
    
    $strSQL = 'SELECT `asset`, `start_time`, `end_time`, `classroom_id`, '
                        . '`course`, `author`, `cam_slide` ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME);
    
    $whereParam = array('start_time >= :time_limit AND end_time IS NOT NULL');
    $valueWhereParam = array('time_limit' => $start_date);
    
    if($classroom_id != "") {
        $whereParam[] = 'classroom_id = :asset_classroom_id';
        $valueWhereParam['asset_classroom_id'] = $classroom_id;
    }
    
    if($courses != "") {
        $whereParam[] = 'course = :courses';
        $valueWhereParam['courses'] = $courses;
    }
    
    if(is_array($not_in_courses)) {
        $param = 'course NOT IN(';
        $count = 1;
        foreach($not_in_courses as $value) {
            $bind_variable = "not_in$count";
            $param .= ":$bind_variable,";
            $valueWhereParam[$bind_variable] = $value;
            $count++;
        }
        $param = rtrim($param, ","); //remove last comma before ending ( )
        $param .= ')';
        $whereParam[] = $param;
    }
    
    if($teacher != "") {
        $whereParam[] = 'author = :teacher';
        $valueWhereParam['teacher'] = $teacher;
    }
    
    if($cam_slide != "") {
        $whereParam[] = 'cam_slide = :cam_slide';
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
        $colOrder = "event_time", $orderSort = "DESC",
        $start_elem = "", $max_elem = "") {
    
    global $db_object;
    
    $strSQL = 'SELECT SQL_CALC_FOUND_ROWS events.asset, events.origin, events.event_time,'
            . ' events.type_id, events.context, events.loglevel, events.message, infos.start_time, '
            . 'infos.end_time, events.classroom_id, infos.course, infos.author, infos.cam_slide ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_TABLE_NAME). ' events ' .
                ' LEFT JOIN ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME) . ' infos ' .
                    ' on events.asset = infos.asset';
    
    
    
    $whereParam = array();
    $valueWhereParam = array();
    
    if($asset != "") {
        $whereParam[] = "events.asset = ?";
        $valueWhereParam[] = $asset;
    }
    
    if($origin != "") {
        $whereParam[] = "events.origin = ?";
        $valueWhereParam[] = $origin;
    }
    
    if($asset_classroom_id != "") {
        $whereParam[] = "events.classroom_id LIKE ?";
        $valueWhereParam[] = db_sanitize($asset_classroom_id);
    }
    
    if($asset_course != "") {
        $whereParam[] = "infos.course LIKE ?";
        $valueWhereParam[] = db_sanitize($asset_course);
    }
    
    if($asset_author != "") {
        $whereParam[] = "infos.author LIKE ?";
        $valueWhereParam[] = db_sanitize($asset_author);
    }
    
    if($first_event_time != "") {
        $whereParam[] = "events.event_time >= ?";
        $valueWhereParam[] = $first_event_time;
    }
    
    if($last_event_time != "") {
        $whereParam[] = "events.event_time <= ?";
        $valueWhereParam[] = $last_event_time;
    }
    
    if($type_id != "") {
        $whereParam[] = "events.type_id = ?";
        $valueWhereParam[] = $type_id;
    }
    
    if($context != "") {
        $whereParam[] = "events.context LIKE ?";
        $valueWhereParam[] = db_sanitize($context);
    }
    
    if($loglevel != "" && !empty($loglevel) && $loglevel[0] != NULL) {
        $tempWhereParam = array();
        foreach($loglevel as $lvl) {
            $tempWhereParam[] = "events.loglevel = ?";
            $valueWhereParam[] = $lvl;
        }
        $whereParam[] = "(".implode(" OR ", $tempWhereParam).")";
    }
    
    if($message != "") {
        $whereParam[] = "events.message LIKE ?";
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
    $strSQL .= 'classroom_event_id DESC '; //order by classroom_event_id too so that we can order events which happened in the same second
    
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
        $asset, $colOrder = "status.status_time", $orderSort = "ASC", 
        $start_elem = "", $max_elem = "", $not_in_courses = false) {
    
    global $db_object;
    
    $strSQL = 'SELECT SQL_CALC_FOUND_ROWS status.* ' .
                    'FROM ' . db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME). ' status ' .
                    'JOIN ' . db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME). ' info ON status.asset = info.asset ';
    
    $whereParam = array();
    $valueWhereParam = array();
    
    if($firtDate != "") {
        $whereParam[] = "status.status_time >= ?";
        $valueWhereParam[] = $firtDate;
    }
    
    if($endDate != "") {
        $whereParam[] = "status.status_time <= ?";
        $valueWhereParam[] = $endDate;
    }
    
    if($typeStatus != "" && !empty($typeStatus) && $typeStatus[0] != NULL) {
        $tempWhereParam = array();
        foreach($typeStatus as $status) {
            $tempWhereParam[] = "status.status = ?";
            $valueWhereParam[] = $status;
        }
        $whereParam[] = "(".implode(" OR ", $tempWhereParam).")";
    }
    
    if(is_array($not_in_courses)) {
        $param = 'info.course NOT IN(';
        foreach($not_in_courses as $value) {
            $param .= "?,";
            $valueWhereParam[] = $value;
        }
        $param = rtrim($param, ","); //remove last comma before ending ( )
        $param .= ')';
        $whereParam[] = $param;
    }
    
    
    if($asset != "") {
        $whereParam[] = "status.asset = ?";
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
                    asset_info.classroom_id, asset_info.author, asset_info.course, status.status
                FROM '. db_gettable(ServerLogger::EVENT_ASSET_INFO_TABLE_NAME).' asset_info
                    LEFT JOIN '. db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME).' status
                        ON status.asset = asset_info.asset';
    
    $strSQL .= ' WHERE (status IS NULL) OR (';
    if($classroom != "") {
        $strSQL .= ' asset_info.classroom_id = :asset_classroom_id AND ';
        $argument['asset_classroom_id'] = $classroom;
    }
    //get only last status
    $strSQL .= "status.status_time = (SELECT MAX(s2.status_time) FROM ".db_gettable(ServerLogger::EVENT_STATUS_TABLE_NAME)." s2 WHERE s2.asset = asset_info.asset)";
    $strSQL .= ")";
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute($argument);
    return $reqSQL->fetchAll(PDO::FETCH_ASSOC);
}

function db_event_status_last_insert() {
    global $statements;
    $statements['status_last_insert']->execute();
    return $statements['status_last_insert']->fetch(PDO::FETCH_NUM);
}

/**
 * Return the number of success and fail asset
 * 
 * @param String $start_date start date of the status who must be selected
 * @param String $end_date limit of the date
 * @return Array with the number of success first and error after
 */
function db_event_status_get_nbr($start_date, $end_date) {
    global $statements;
    
    $statements['status_nbr_success']->bindParam(':start_date', $start_date);
    $statements['status_nbr_success']->bindParam(':end_date', $end_date);
    $statements['status_nbr_success']->execute();
    $reqResSuccess = $statements['status_nbr_success']->fetch(PDO::FETCH_NUM);
    $success = $reqResSuccess[0];
    
    $statements['status_nbr_error']->bindParam(':start_date', $start_date);
    $statements['status_nbr_error']->bindParam(':end_date', $end_date);
    $statements['status_nbr_error']->execute();
    $reqResError = $statements['status_nbr_error']->fetch(PDO::FETCH_NUM);
    $error = $reqResError[0];
    
    return array($success, $error);
}

/**
 * Return an array with date of success and error asset
 * 
 * @param String $start_date start date of the status who must be selected
 * @param String $end_date limit of the date
 * @return Array with the number of success first and error after
 */
function db_event_status_get_date($start_date, $end_date) {
    global $statements;
    $res = array('success' => array(),
                'error' => array());
    
    $valide_status = array('auto_success', 'auto_success_errors', 'auto_success_warnings', 
                    'manual_ok', 'manual_partial_ok');
    
    $statements['status_date_asset']->bindParam(':start_date', $start_date);
    $statements['status_date_asset']->bindParam(':end_date', $end_date);
    $statements['status_date_asset']->execute();
    
    foreach($statements['status_date_asset']->fetchAll(PDO::FETCH_ASSOC) as $line) {
        if(in_array($line['status'], $valide_status)) {
            $type = 'success';
        } else {
            $type = 'error';
        }
        array_increment_or_init_static($res[$type], strtotime($line['status_time']).'000');
    }
    
    return $res;
}

function db_event_info_camslide_nbr($start_date, $end_date) {
    global $statements;
    
    $statements['asset_info_camslide']->bindParam(':start_date', $start_date);
    $statements['asset_info_camslide']->bindParam(':end_date', $end_date);
    $statements['asset_info_camslide']->execute();
    return $statements['asset_info_camslide']->fetchAll(PDO::FETCH_ASSOC);
}

function get_courses_excluded_from_stats() {
    global $courses_excluded_from_stats;
    return $courses_excluded_from_stats;
}