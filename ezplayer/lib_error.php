<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
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
 * Error managing and logging library
 * @package ezcast.ezplayer.lib.error
 */

include_once 'config.inc';

/**
 * Prints the error message on screen and quits
 * @param string $msg The message to print
 * @param bool $log If set to false, the error won't be logged.
 */
function error_print_message($msg, $log = true) {
    //echo '<b>Error: </b>'.$msg;
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<script type="text/javascript">window.alert("'.$msg.'");</script>';
    //echo '<div style="display: none;" id="#error" onload="show_popup_from_inner_div(\'#error\');">'.$msg.'</div>';
    
    if($log) {
        log_append('error', $msg);
        die;
    }
}

/**
 * Prints an error corresponding to a HTTP code
 * @param int $http_code 
 */
function error_print_http($http_code) {
    switch($http_code) {
        case 403:
            header('HTTP/1.0 403 Forbidden');
            break;
        
        case 404:
            header('HTTP/1.0 404 Not Found');
            break;
    }
}

/**
 * Adds a line in log
 * @global string $ezplayer_logs Path to the log file
 * @param string $operation The operation done
 * @param string $message Additionnal information (parameters)
 */
function log_append($operation, $message = '') {
    global $ezplayer_logs;
    global $user_files_path;
    
    // 1) Date/time at which the event occurred
    $data = date('Y-m-d-H:i');
    $data .= ' ';
    
    // 2) IP address of the user that provoked the event
    $data .= (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'noip';
    $data .= ' ';
    
    // 3) Username and realname of the user that provoked the event
    // There can be no login if the operation was performed by a CLI tool for instance.
    // In that case, we display "nologin" instead.
    if(!isset($_SESSION['user_login']) || empty($_SESSION['user_login'])) {
        $data .= 'nologin';
    }
    // General case, where there is a login and (possibly) a real login
    else if(isset($_SESSION['real_login'])) {
        $data .= $_SESSION['real_login'].'/'.$_SESSION['user_login'];
    }
    else {
        $data .= $_SESSION['user_login'];
    }
    $data .= ' ';
    
    // 4) Operation performed
    $data .= $operation;
    
    // 5) Optionnal parameters
    if(!empty($message))
        $data .= ': '.$message;
    
    // 6) And we add a carriage return for readability
    $data .= PHP_EOL;
    
    // Then we save the new entry
    file_put_contents($ezplayer_logs, $data, FILE_APPEND | LOCK_EX);
    if (acl_user_is_logged()){
        $logs = $user_files_path . '/' . $_SESSION['user_login'] . '/ezplayer.log';
        file_put_contents($logs, $data, FILE_APPEND | LOCK_EX);        
    }
}
