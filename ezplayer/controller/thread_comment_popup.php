<?php

/**
 * renders a modal window related to a thread comment
 * This window is displayed according to specific actions (delete | ...)
 * @global array $input
 */
function index($param = array())
{
    global $input;

    $comment_id = $input['comment_id'];

    $comment = comment_select_by_id($comment_id);

    switch ($input['display']) {
        case 'delete':
            include_once template_getpath('popup_thread_comment_delete.php');
            break;
    }
}
