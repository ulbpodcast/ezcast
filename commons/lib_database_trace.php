<?php

/*
 * EZCAST EZadmin 
 * Copyright (C) 2017 Université libre de Bruxelles
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

require_once 'config.inc';
require_once 'lib_database.php';

global $trace_statement;
$trace_statement = array();

function param_default_get() {
    $result = array();
    
    $result[':time_action'] = date('Y-m-d-H:i:s');
    $result[':session'] = session_id();
    $result[':ip'] = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'noip';
    if (!isset($_SESSION['user_login']) || empty($_SESSION['user_login']) || $_SESSION['user_login'] === "anon") {
        $result[':netid'] = 'nologin';
        
    // General case, where there is a login and (possibly) a real login
    } else if (isset($_SESSION['real_login'])) {
        $result[':netid'] = $_SESSION['real_login'] . '/' . $_SESSION['user_login'];
        
    } else {
        $result[':netid'] = $_SESSION['user_login'];
    }
    
    return $result;
}

function trace_request_build() {
    global $trace_statement;
    if(!empty($trace_statement)) {
        return;
    }
    
    global $in_install;
    if($in_install == true) {
        return; //no database when still in installation
    }

    global $db_object;
    if($db_object == null) { //db is not yet prepared yet
        db_prepare(); 
    }

    trace_prepare_statement($db_object);

    foreach ($trace_statement as $state) {
        if($state == false) {
            echo 'trace_statement: Prepared statement failed';
            print_r($this->db->errorInfo());
            throw new Exception("Prepared statement failed");
        }
    }
}

function trace_prepare_statement($db_object) {
    global $trace_statement;
    
    $trace_statement['insert_log_general'] = 'INSERT INTO log_general(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`info0`, `info1`, `info2`, `info3`, `info4`, `info5`, `info6`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:info0`, `:info1`, `:info2`, `:info3`, `:info4`, `:info5`, `:info6`)';

    $trace_statement['insert_log_video_switch'] = 'INSERT INTO log_video_switch(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, `previous_video_type`, ' .
            '`current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, `:previous_video_type`, ' . 
            '`:current_video_type`, `:quality`, `:origin`)';

    $trace_statement['insert_log_video_mute'] = 'INSERT INTO log_video_mute(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, ' .
            '`quality`, `mute`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, ' . 
            '`:quality`, `:mute`, `:origin`)';

    $trace_statement['insert_log_bookmark_form_hide'] = 'INSERT INTO log_bookmark_form(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`type_form`, `album`, `asset`, `video_duration`, `current_video_time`, ' .
            '`current_video_type`, `quality`, `target`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '"hide", `:album`, `:asset`, `:video_duration`, `:current_video_time`, ' . 
            '`:current_video_type`, `:quality`, `:target`, `:origin`)';

    $trace_statement['insert_log_bookmark_form_show'] = 'INSERT INTO log_bookmark_form(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`type_form`, `album`, `asset`, `video_duration`, `current_video_time`, ' .
            '`current_video_type`, `quality`, `target`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '"show", `:album`, `:asset`, `:video_duration`, `:current_video_time`, ' . 
            '`:current_video_type`, `:quality`, `:target`, `:origin`)';

    $trace_statement['insert_log_bookmarks_edit'] = 'INSERT INTO log_bookmarks_edit(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `timecode`, `target`, `type`, `title`, `descr`, `keywords`, `bookmark_lvl`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:timecode`, `:target`, `:type`, `:title`, `:descr`, `:keywords`, `:bookmark_lvl`)';


    $trace_statement['insert_log_search_keyword'] = 'INSERT INTO log_search(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`type_search`, `album`, `asset`, `searched_words`, `target`, `fields`, ' .
            '`fields_thread`, `tab`, `nbr_official_bookmarks_found`, `nbr_bookmarks_found`, ' .
            '`nbr_threads_found`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '"keyword", `:album`, `:asset`, `:searched_words`, `:target`, `:fields`, ' . 
            '`:fields_thread`, `:tab`, `:nbr_official_bookmarks_found`, `:nbr_bookmarks_found`, ' . 
            '`:nbr_threads_found`)';


    $trace_statement['insert_log_search_bookmarks'] = 'INSERT INTO log_search(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`type_search`, `album`, `asset`, `searched_words`, `target`, `fields`, ' .
            '`fields_thread`, `tab`, `nbr_official_bookmarks_found`, `nbr_bookmarks_found`, ' .
            '`nbr_threads_found`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '"bookmarks", `:album`, `:asset`, `:searched_words`, `:target`, `:fields`, ' . 
            '`:fields_thread`, `:tab`, `:nbr_official_bookmarks_found`, `:nbr_bookmarks_found`, ' . 
            '`:nbr_threads_found`)';


    $trace_statement['insert_log_thread_list_back'] = 'INSERT INTO log_thread_list_back(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`)';


    $trace_statement['insert_log_thread_detail_show'] = 'INSERT INTO log_thread_detail_show(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `thread_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:thread_id`)';


    $trace_statement['insert_log_vote_up'] = 'INSERT INTO log_vote(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`positive`, `album`, `asset`, `comment_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '1, `:album`, `:asset`, `:comment_id`)';


    $trace_statement['insert_log_vote_down'] = 'INSERT INTO log_vote(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`positive`, `album`, `asset`, `comment_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '0, `:album`, `:asset`, `:comment_id`)';


    $trace_statement['insert_log_bookmarks_swap'] = 'INSERT INTO log_bookmarks_swap(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `bookmark_type`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:bookmark_type`)';


    $trace_statement['insert_log_comment_reply_add'] = 'INSERT INTO log_comment_reply_add(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `thread_id`, `comment_parent`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:thread_id`, `:comment_parent`)';


    $trace_statement['insert_log_comment_edit'] = 'INSERT INTO log_comment_edit(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `thread_id`, `comment_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:thread_id`, `:comment_id`)';



    $trace_statement['insert_log_chat_message_add'] = 'INSERT INTO log_chat_message_add(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `timecode`, `message`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:timecode`, `:message`)';


    $trace_statement['insert_log_comment_delete'] = 'INSERT INTO log_comment_delete(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `thread_id`, `comment_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:thread_id`, `:comment_id`)';


    $trace_statement['insert_log_view_asset_details'] = 'INSERT INTO log_view_asset_details(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `record_type`, `permissions`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:record_type`, `:permissions`, `:origin`)';


    $trace_statement['insert_log_thread_add'] = 'INSERT INTO log_thread_add(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `timecode`, `thread_title`, `thread_visibility`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:timecode`, `:thread_title`, `:thread_visibility`)';


    $trace_statement['insert_log_browser_fullscreen_enter'] = 'INSERT INTO log_browser_fullscreen(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`enter`, `album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '1, `:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`)';


    $trace_statement['insert_log_browser_fullscreen_exit'] = 'INSERT INTO log_browser_fullscreen(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`enter`, `album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '0, `:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`)';


    $trace_statement['insert_log_video_forward'] = 'INSERT INTO log_video_forward(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, ' .
            '`current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, ' . 
            '`:current_video_type`, `:quality`, `:origin`)';


    $trace_statement['insert_log_video_rewind'] = 'INSERT INTO log_video_rewind(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`, `:origin`)';


    $trace_statement['insert_log_video_fullscreen_enter'] = 'INSERT INTO log_video_fullscreen(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`enter`, `album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '1, `:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`, `:origin`)';



    $trace_statement['insert_log_video_fullscreen_exit'] = 'INSERT INTO log_video_fullscreen(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`enter`, `album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '0, `:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`, `:origin`)';


    $trace_statement['insert_log_video_play'] = 'INSERT INTO log_video_play(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`, `:origin`)';


    $trace_statement['insert_log_video_pause'] = 'INSERT INTO log_video_pause(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `current_video_time`, `current_video_type`, `quality`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:current_video_time`, `:current_video_type`, `:quality`, `:origin`)';


    $trace_statement['insert_log_video_seeked'] = 'INSERT INTO log_video_seeked(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `previous_video_time`, `current_video_time`, `current_video_type`, `quality`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:previous_video_time`, `:current_video_time`, `:current_video_type`, `:quality`)';


    $trace_statement['insert_log_playback_speed_up'] = 'INSERT INTO log_playback_speed(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`speed_up`, `album`, `asset`, `video_duration`, `current_video_time`, ' .
            '`current_video_type`, `quality`, `playback_speed`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '1, `:album`, `:asset`, `:video_duration`, `:current_video_time`, ' . 
            '`:current_video_type`, `:quality`, `:playback_speed`, `:origin`)';


    $trace_statement['insert_log_playback_speed_down'] = 'INSERT INTO log_playback_speed(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`speed_up`, `album`, `asset`, `video_duration`, `current_video_time`, ' .
            '`current_video_type`, `quality`, `playback_speed`, `origin`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '0, `:album`, `:asset`, `:video_duration`, `:current_video_time`, ' . 
            '`:current_video_type`, `:quality`, `:playback_speed`, `:origin`)';


    $trace_statement['insert_log_asset_bookmark_add'] = 'INSERT INTO log_asset_bookmark_add(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `timecode`, `target`, `type`, `title`, `descr`, ' .
            '`keywords`, `bookmark_lvl`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:timecode`, `:target`, `:type`, `:title`, `:descr`, ' . 
            '`:keywords`, `:bookmark_lvl`)';



    $trace_statement['insert_log_comment_add'] = 'INSERT INTO log_comment_add(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `thread_id`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:thread_id`)';


    $trace_statement['insert_log_video_bookmark_click'] = 'INSERT INTO log_video_bookmark_click(`time_action`, `session`, `ip`, `netid`, `level`, ' .
            '`album`, `asset`, `video_duration`, `previous_video_time`, `current_video_time`, ' .
            '`previous_video_type`, `current_video_type`, `bookmark_type`, `quality`) ' .
        'VALUES(`:time_action`, `:session`, `:ip`, `:netid`, `:level`, ' . 
            '`:album`, `:asset`, `:video_duration`, `:previous_video_time`, `:current_video_time`, ' . 
            '`:previous_video_type`, `:current_video_type`, `:bookmark_type`, `:quality`)';
    
}


function trace_insert_general($level, $action, $info0 = NULL, $info1 = NULL, $info2 = NULL, $info3 = NULL, 
        $info4 = NULL, $info5 = NULL, $info6 = NULL) {
    global $trace_on;

    if (!$trace_on) {
        return false;
    }
    $result = param_default_get();
    $result[':level'] = $level;

    $idx = 0;
    $max_idx = count($array);
    foreach ($array as $value) {
        $idx++;
        $data .= $value;
        if ($idx != $max_idx)
            $data .= ' | ';
    }
    // 6) And we add a carriage return for readability
    $data .= PHP_EOL;
}

function trace_log_general($time_action, $session, $ip, $netid, $level, 
        $info0, $info1, $info2, $info3, $info4, $info5, $info6) {
    global $trace_statement;
    
    $trace_statement['insert_log_general']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_general']->bindParam(':session', $session);
    $trace_statement['insert_log_general']->bindParam(':ip', $ip);
    $trace_statement['insert_log_general']->bindParam(':netid', $netid);
    $trace_statement['insert_log_general']->bindParam(':level', $level);
    $trace_statement['insert_log_general']->bindParam(':info0', $info0);
    $trace_statement['insert_log_general']->bindParam(':info1', $info1);
    $trace_statement['insert_log_general']->bindParam(':info2', $info2);
    $trace_statement['insert_log_general']->bindParam(':info3', $info3);
    $trace_statement['insert_log_general']->bindParam(':info4', $info4);
    $trace_statement['insert_log_general']->bindParam(':info5', $info5);
    $trace_statement['insert_log_general']->bindParam(':info6', $info6);
    $trace_statement['insert_log_general']->execute();
}
function trace_log_video_switch($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $previous_video_type, 
        $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_switch']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_switch']->bindParam(':session', $session);
    $trace_statement['insert_log_video_switch']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_switch']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_switch']->bindParam(':level', $level);
    $trace_statement['insert_log_video_switch']->bindParam(':album', $album);
    $trace_statement['insert_log_video_switch']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_switch']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_switch']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_switch']->bindParam(':previous_video_type', $previous_video_type);
    $trace_statement['insert_log_video_switch']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_switch']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_switch']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_switch']->execute();
}

function trace_log_video_mute($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, 
        $quality, $mute, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_mute']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_mute']->bindParam(':session', $session);
    $trace_statement['insert_log_video_mute']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_mute']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_mute']->bindParam(':level', $level);
    $trace_statement['insert_log_video_mute']->bindParam(':album', $album);
    $trace_statement['insert_log_video_mute']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_mute']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_mute']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_mute']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_mute']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_mute']->bindParam(':mute', $mute);
    $trace_statement['insert_log_video_mute']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_mute']->execute();
}

function trace_log_bookmark_form_hide($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, 
        $current_video_type, $quality, $target, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_bookmark_form']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_bookmark_form']->bindParam(':session', $session);
    $trace_statement['insert_log_bookmark_form']->bindParam(':ip', $ip);
    $trace_statement['insert_log_bookmark_form']->bindParam(':netid', $netid);
    $trace_statement['insert_log_bookmark_form']->bindParam(':level', $level);
    $trace_statement['insert_log_bookmark_form']->bindParam(':type_form', $type_form);
    $trace_statement['insert_log_bookmark_form']->bindParam(':album', $album);
    $trace_statement['insert_log_bookmark_form']->bindParam(':asset', $asset);
    $trace_statement['insert_log_bookmark_form']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_bookmark_form']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_bookmark_form']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_bookmark_form']->bindParam(':quality', $quality);
    $trace_statement['insert_log_bookmark_form']->bindParam(':target', $target);
    $trace_statement['insert_log_bookmark_form']->bindParam(':origin', $origin);
    $trace_statement['insert_log_bookmark_form_hide']->execute();
}

function trace_log_bookmark_form_show($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, 
        $current_video_type, $quality, $target, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_bookmark_form']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_bookmark_form']->bindParam(':session', $session);
    $trace_statement['insert_log_bookmark_form']->bindParam(':ip', $ip);
    $trace_statement['insert_log_bookmark_form']->bindParam(':netid', $netid);
    $trace_statement['insert_log_bookmark_form']->bindParam(':level', $level);
    $trace_statement['insert_log_bookmark_form']->bindParam(':type_form', $type_form);
    $trace_statement['insert_log_bookmark_form']->bindParam(':album', $album);
    $trace_statement['insert_log_bookmark_form']->bindParam(':asset', $asset);
    $trace_statement['insert_log_bookmark_form']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_bookmark_form']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_bookmark_form']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_bookmark_form']->bindParam(':quality', $quality);
    $trace_statement['insert_log_bookmark_form']->bindParam(':target', $target);
    $trace_statement['insert_log_bookmark_form']->bindParam(':origin', $origin);
    $trace_statement['insert_log_bookmark_form_show']->execute();
}

function trace_log_bookmarks_edit($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $timecode, $target, $type, $title, $descr, $keywords, $bookmark_lvl) {
    global $trace_statement;
    
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':session', $session);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':ip', $ip);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':netid', $netid);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':level', $level);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':album', $album);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':asset', $asset);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':timecode', $timecode);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':target', $target);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':type', $type);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':title', $title);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':descr', $descr);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':keywords', $keywords);
    $trace_statement['insert_log_bookmarks_edit']->bindParam(':bookmark_lvl', $bookmark_lvl);
    $trace_statement['insert_log_bookmarks_edit']->execute();
}
    

function trace_log_search_keyword($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $searched_words, $target, $fields, 
        $fields_thread, $tab, $nbr_official_bookmarks_found, $nbr_bookmarks_found, 
        $nbr_threads_found) {
    global $trace_statement;
    
    $trace_statement['insert_log_search_keyword']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_search_keyword']->bindParam(':session', $session);
    $trace_statement['insert_log_search_keyword']->bindParam(':ip', $ip);
    $trace_statement['insert_log_search_keyword']->bindParam(':netid', $netid);
    $trace_statement['insert_log_search_keyword']->bindParam(':level', $level);
    $trace_statement['insert_log_search_keyword']->bindParam(':album', $album);
    $trace_statement['insert_log_search_keyword']->bindParam(':asset', $asset);
    $trace_statement['insert_log_search_keyword']->bindParam(':searched_words', $searched_words);
    $trace_statement['insert_log_search_keyword']->bindParam(':target', $target);
    $trace_statement['insert_log_search_keyword']->bindParam(':fields', $fields);
    $trace_statement['insert_log_search_keyword']->bindParam(':fields_thread', $fields_thread);
    $trace_statement['insert_log_search_keyword']->bindParam(':tab', $tab);
    $trace_statement['insert_log_search_keyword']->bindParam(':nbr_official_bookmarks_found', $nbr_official_bookmarks_found);
    $trace_statement['insert_log_search_keyword']->bindParam(':nbr_bookmarks_found', $nbr_bookmarks_found);
    $trace_statement['insert_log_search_keyword']->bindParam(':$nbr_threads_found', $$nbr_threads_found);
    $trace_statement['insert_log_search_keyword']->execute();
}
    

function trace_log_search_bookmarks($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $searched_words, $target, $fields, 
        $fields_thread, $tab, $nbr_official_bookmarks_found, $nbr_bookmarks_found, 
        $nbr_threads_found) {
    global $trace_statement;
    
    $trace_statement['insert_log_search_bookmarks']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':session', $session);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':ip', $ip);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':netid', $netid);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':level', $level);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':album', $album);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':asset', $asset);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':searched_words', $searched_words);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':target', $target);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':fields', $fields);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':fields_thread', $fields_thread);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':tab', $tab);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':nbr_official_bookmarks_found', $nbr_official_bookmarks_found);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':nbr_bookmarks_found', $nbr_bookmarks_found);
    $trace_statement['insert_log_search_bookmarks']->bindParam(':$nbr_threads_found', $$nbr_threads_found);
    $trace_statement['insert_log_search_bookmarks']->execute();
}


function trace_log_thread_list_back($time_action, $session, $ip, $netid, $level, 
        $album, $asset) {
    global $trace_statement;
    
    $trace_statement['insert_log_thread_list_back']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_thread_list_back']->bindParam(':session', $session);
    $trace_statement['insert_log_thread_list_back']->bindParam(':ip', $ip);
    $trace_statement['insert_log_thread_list_back']->bindParam(':netid', $netid);
    $trace_statement['insert_log_thread_list_back']->bindParam(':level', $level);
    $trace_statement['insert_log_thread_list_back']->bindParam(':album', $album);
    $trace_statement['insert_log_thread_list_back']->bindParam(':asset', $asset);
    $trace_statement['insert_log_thread_list_back']->execute();
}
    

function trace_log_thread_detail_show($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $thread_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_thread_detail_show']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':session', $session);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':ip', $ip);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':netid', $netid);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':level', $level);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':album', $album);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':asset', $asset);
    $trace_statement['insert_log_thread_detail_show']->bindParam(':thread_id', $thread_id);
    $trace_statement['insert_log_thread_detail_show']->execute();
}
    

function trace_log_vote_up($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $comment_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_vote_up']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_vote_up']->bindParam(':session', $session);
    $trace_statement['insert_log_vote_up']->bindParam(':ip', $ip);
    $trace_statement['insert_log_vote_up']->bindParam(':netid', $netid);
    $trace_statement['insert_log_vote_up']->bindParam(':level', $level);
    $trace_statement['insert_log_vote_up']->bindParam(':album', $album);
    $trace_statement['insert_log_vote_up']->bindParam(':asset', $asset);
    $trace_statement['insert_log_vote_up']->bindParam(':comment_id', $comment_id);
    $trace_statement['insert_log_vote_up']->execute();
}
    

function trace_log_vote_down($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $comment_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_vote_down']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_vote_down']->bindParam(':session', $session);
    $trace_statement['insert_log_vote_down']->bindParam(':ip', $ip);
    $trace_statement['insert_log_vote_down']->bindParam(':netid', $netid);
    $trace_statement['insert_log_vote_down']->bindParam(':level', $level);
    $trace_statement['insert_log_vote_down']->bindParam(':album', $album);
    $trace_statement['insert_log_vote_down']->bindParam(':asset', $asset);
    $trace_statement['insert_log_vote_down']->bindParam(':comment_id', $comment_id);
    $trace_statement['insert_log_vote_down']->execute();
}
    

function trace_log_bookmarks_swap($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $bookmark_type) {
    global $trace_statement;
    
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':session', $session);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':ip', $ip);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':netid', $netid);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':level', $level);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':album', $album);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':asset', $asset);
    $trace_statement['insert_log_bookmarks_swap']->bindParam(':bookmark_type', $bookmark_type);
    $trace_statement['insert_log_bookmarks_swap']->execute();
}
    

function trace_log_comment_reply_add($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $thread_id, $comment_parent) {
    global $trace_statement;
    
    $trace_statement['insert_log_comment_reply_add']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':session', $session);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':ip', $ip);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':netid', $netid);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':level', $level);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':album', $album);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':asset', $asset);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':thread_id', $thread_id);
    $trace_statement['insert_log_comment_reply_add']->bindParam(':comment_parent', $comment_parent);
    $trace_statement['insert_log_comment_reply_add']->execute();
}
    

function trace_log_comment_edit($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $thread_id, $comment_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_comment_edit']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_comment_edit']->bindParam(':session', $session);
    $trace_statement['insert_log_comment_edit']->bindParam(':ip', $ip);
    $trace_statement['insert_log_comment_edit']->bindParam(':netid', $netid);
    $trace_statement['insert_log_comment_edit']->bindParam(':level', $level);
    $trace_statement['insert_log_comment_edit']->bindParam(':album', $album);
    $trace_statement['insert_log_comment_edit']->bindParam(':asset', $asset);
    $trace_statement['insert_log_comment_edit']->bindParam(':thread_id', $thread_id);
    $trace_statement['insert_log_comment_edit']->bindParam(':comment_id', $comment_id);
    $trace_statement['insert_log_comment_edit']->execute();
}

    

function trace_log_chat_message_add($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $timecode, $message) {
    global $trace_statement;
    
    $trace_statement['insert_log_chat_message_add']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_chat_message_add']->bindParam(':session', $session);
    $trace_statement['insert_log_chat_message_add']->bindParam(':ip', $ip);
    $trace_statement['insert_log_chat_message_add']->bindParam(':netid', $netid);
    $trace_statement['insert_log_chat_message_add']->bindParam(':level', $level);
    $trace_statement['insert_log_chat_message_add']->bindParam(':album', $album);
    $trace_statement['insert_log_chat_message_add']->bindParam(':asset', $asset);
    $trace_statement['insert_log_chat_message_add']->bindParam(':timecode', $timecode);
    $trace_statement['insert_log_chat_message_add']->bindParam(':message', $message);
    $trace_statement['insert_log_chat_message_add']->execute();
}
    

function trace_log_comment_delete($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $thread_id, $comment_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_comment_delete']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_comment_delete']->bindParam(':session', $session);
    $trace_statement['insert_log_comment_delete']->bindParam(':ip', $ip);
    $trace_statement['insert_log_comment_delete']->bindParam(':netid', $netid);
    $trace_statement['insert_log_comment_delete']->bindParam(':level', $level);
    $trace_statement['insert_log_comment_delete']->bindParam(':album', $album);
    $trace_statement['insert_log_comment_delete']->bindParam(':asset', $asset);
    $trace_statement['insert_log_comment_delete']->bindParam(':thread_id', $thread_id);
    $trace_statement['insert_log_comment_delete']->bindParam(':comment_id', $comment_id);
    $trace_statement['insert_log_comment_delete']->execute();
}
    

function trace_log_view_asset_details($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $record_type, $permissions, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_view_asset_details']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_view_asset_details']->bindParam(':session', $session);
    $trace_statement['insert_log_view_asset_details']->bindParam(':ip', $ip);
    $trace_statement['insert_log_view_asset_details']->bindParam(':netid', $netid);
    $trace_statement['insert_log_view_asset_details']->bindParam(':level', $level);
    $trace_statement['insert_log_view_asset_details']->bindParam(':album', $album);
    $trace_statement['insert_log_view_asset_details']->bindParam(':asset', $asset);
    $trace_statement['insert_log_view_asset_details']->bindParam(':record_type', $record_type);
    $trace_statement['insert_log_view_asset_details']->bindParam(':permissions', $permissions);
    $trace_statement['insert_log_view_asset_details']->bindParam(':origin', $origin);
    $trace_statement['insert_log_view_asset_details']->execute();
}
    

function trace_log_thread_add($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $timecode, $thread_title, $thread_visibility) {
    global $trace_statement;
    
    $trace_statement['insert_log_thread_add']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_thread_add']->bindParam(':session', $session);
    $trace_statement['insert_log_thread_add']->bindParam(':ip', $ip);
    $trace_statement['insert_log_thread_add']->bindParam(':netid', $netid);
    $trace_statement['insert_log_thread_add']->bindParam(':level', $level);
    $trace_statement['insert_log_thread_add']->bindParam(':album', $album);
    $trace_statement['insert_log_thread_add']->bindParam(':asset', $asset);
    $trace_statement['insert_log_thread_add']->bindParam(':timecode', $timecode);
    $trace_statement['insert_log_thread_add']->bindParam(':thread_title', $thread_title);
    $trace_statement['insert_log_thread_add']->bindParam(':thread_visibility', $thread_visibility);
    $trace_statement['insert_log_thread_add']->execute();
}
    

function trace_log_browser_fullscreen_enter($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality) {
    global $trace_statement;
    
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':session', $session);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':ip', $ip);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':netid', $netid);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':level', $level);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':album', $album);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':asset', $asset);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_browser_fullscreen_enter']->bindParam(':quality', $quality);
    $trace_statement['insert_log_browser_fullscreen_enter']->execute();
}
    

function trace_log_browser_fullscreen_exit($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality) {
    global $trace_statement;
    
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':session', $session);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':ip', $ip);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':netid', $netid);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':level', $level);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':album', $album);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':asset', $asset);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_browser_fullscreen_exit']->bindParam(':quality', $quality);
    $trace_statement['insert_log_browser_fullscreen_exit']->execute();
}
    

function trace_log_video_forward($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, 
        $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_forward']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_forward']->bindParam(':session', $session);
    $trace_statement['insert_log_video_forward']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_forward']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_forward']->bindParam(':level', $level);
    $trace_statement['insert_log_video_forward']->bindParam(':album', $album);
    $trace_statement['insert_log_video_forward']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_forward']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_forward']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_forward']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_forward']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_forward']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_forward']->execute();
}
    

function trace_log_video_rewind($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_rewind']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_rewind']->bindParam(':session', $session);
    $trace_statement['insert_log_video_rewind']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_rewind']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_rewind']->bindParam(':level', $level);
    $trace_statement['insert_log_video_rewind']->bindParam(':album', $album);
    $trace_statement['insert_log_video_rewind']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_rewind']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_rewind']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_rewind']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_rewind']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_rewind']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_rewind']->execute();
}
    

function trace_log_video_fullscreen_enter($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':session', $session);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':level', $level);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':album', $album);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_fullscreen_enter']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_fullscreen_enter']->execute();
}

    

function trace_log_video_fullscreen_exit($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':session', $session);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':level', $level);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':enter', $enter);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':album', $album);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_fullscreen_exit']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_fullscreen_exit']->execute();
}
    

function trace_log_video_play($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_play']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_play']->bindParam(':session', $session);
    $trace_statement['insert_log_video_play']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_play']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_play']->bindParam(':level', $level);
    $trace_statement['insert_log_video_play']->bindParam(':album', $album);
    $trace_statement['insert_log_video_play']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_play']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_play']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_play']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_play']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_play']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_play']->execute();
}
    

function trace_log_video_pause($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, $current_video_type, $quality, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_pause']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_pause']->bindParam(':session', $session);
    $trace_statement['insert_log_video_pause']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_pause']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_pause']->bindParam(':level', $level);
    $trace_statement['insert_log_video_pause']->bindParam(':album', $album);
    $trace_statement['insert_log_video_pause']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_pause']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_pause']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_pause']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_pause']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_pause']->bindParam(':origin', $origin);
    $trace_statement['insert_log_video_pause']->execute();
}
    

function trace_log_video_seeked($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $previous_video_time, $current_video_time, $current_video_type, $quality) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_seeked']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_seeked']->bindParam(':session', $session);
    $trace_statement['insert_log_video_seeked']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_seeked']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_seeked']->bindParam(':level', $level);
    $trace_statement['insert_log_video_seeked']->bindParam(':album', $album);
    $trace_statement['insert_log_video_seeked']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_seeked']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_seeked']->bindParam(':previous_video_time', $previous_video_time);
    $trace_statement['insert_log_video_seeked']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_seeked']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_seeked']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_seeked']->execute();
}
    

function trace_log_playback_speed_up($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, 
        $current_video_type, $quality, $playback_speed, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_playback_speed_up']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':session', $session);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':ip', $ip);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':netid', $netid);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':level', $level);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':album', $album);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':asset', $asset);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':quality', $quality);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':playback_speed', $playback_speed);
    $trace_statement['insert_log_playback_speed_up']->bindParam(':origin', $origin);
    $trace_statement['insert_log_playback_speed_up']->execute();
}
    

function trace_log_playback_speed_down($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $current_video_time, 
        $current_video_type, $quality, $playback_speed, $origin) {
    global $trace_statement;
    
    $trace_statement['insert_log_playback_speed_down']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':session', $session);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':ip', $ip);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':netid', $netid);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':level', $level);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':album', $album);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':asset', $asset);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':quality', $quality);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':playback_speed', $playback_speed);
    $trace_statement['insert_log_playback_speed_down']->bindParam(':origin', $origin);
    $trace_statement['insert_log_playback_speed_down']->execute();
}
    

function trace_log_asset_bookmark_add($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $timecode, $target, $type, $title, $descr, 
        $keywords, $bookmark_lvl) {
    global $trace_statement;
    
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':session', $session);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':ip', $ip);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':netid', $netid);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':level', $level);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':album', $album);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':asset', $asset);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':timecode', $timecode);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':target', $target);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':type', $type);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':title', $title);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':descr', $descr);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':keywords', $keywords);
    $trace_statement['insert_log_asset_bookmark_add']->bindParam(':bookmark_lvl', $bookmark_lvl);
    $trace_statement['insert_log_asset_bookmark_add']->execute();
}

    

function trace_log_comment_add($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $thread_id) {
    global $trace_statement;
    
    $trace_statement['insert_log_comment_add']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_comment_add']->bindParam(':session', $session);
    $trace_statement['insert_log_comment_add']->bindParam(':ip', $ip);
    $trace_statement['insert_log_comment_add']->bindParam(':netid', $netid);
    $trace_statement['insert_log_comment_add']->bindParam(':level', $level);
    $trace_statement['insert_log_comment_add']->bindParam(':album', $album);
    $trace_statement['insert_log_comment_add']->bindParam(':asset', $asset);
    $trace_statement['insert_log_comment_add']->bindParam(':thread_id', $thread_id);
    $trace_statement['insert_log_comment_add']->execute();
}
    

function trace_log_video_bookmark_click($time_action, $session, $ip, $netid, $level, 
        $album, $asset, $video_duration, $previous_video_time, $current_video_time, 
        $previous_video_type, $current_video_type, $bookmark_type, $quality) {
    global $trace_statement;
    
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':time_action', $time_action);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':session', $session);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':ip', $ip);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':netid', $netid);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':level', $level);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':album', $album);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':asset', $asset);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':video_duration', $video_duration);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':previous_video_time', $previous_video_time);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':current_video_time', $current_video_time);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':previous_video_type', $previous_video_type);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':current_video_type', $current_video_type);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':bookmark_type', $bookmark_type);
    $trace_statement['insert_log_video_bookmark_click']->bindParam(':quality', $quality);
    $trace_statement['insert_log_video_bookmark_click']->execute();
}
    
