<?php

/**
 * Return a specific thread to display in a popup (delete)
 * @global type $input
 */
function index($param = array())
{
    global $input;

    $thread_id = $input['thread_id'];

    $thread = thread_select_by_id($thread_id);

    switch ($input['display']) {
        case 'delete':
            include_once template_getpath('popup_thread_delete.php');
            break;
    }
}
