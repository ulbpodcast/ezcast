<?php

/**
 * Reloads the threads list
 * @global type $input
 */
function index($param = array())
{
    global $input;
    
    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    $display = (count($param) == 0 || $param[0]);

    $threads = get_threads($input['album'], $input['asset']);
    
    if ($display) {
        include_once template_getpath('div_threads_list.php');
        return true;
    } else {
        return $threads;
    }
}
