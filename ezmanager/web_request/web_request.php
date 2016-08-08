<?php

require_once __DIR__."/../classroom_recorder_ip.inc"; //valid ip file

//global variable
$caller_ip = null;

function is_authorized_caller() {
    global $podcv_ip;
    global $podcs_ip;
    global $caller_ip;
    
    //look for caller's ip in config files
    $caller_ip = trim($_SERVER["REMOTE_ADDR"]);

    $key = array_search($caller_ip, $podcv_ip);
    if ($key === false) {
        $key = array_search($caller_ip, $podcs_ip);
        if ($key === false) {
            //ip not found
            return false;
        }
    }
    
    return true;
}
