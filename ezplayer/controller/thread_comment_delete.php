<?php

/**
 * Used to remove a comment
 * @global type $input
 */
function index($param = array())
{
    global $input;
    $id = $input['comment_id'];

    comment_delete_by_id($id);
    cache_asset_threads_unset($_SESSION['album'], $_SESSION['asset']);

    $_SESSION['current_thread'] = $input['thread_id'];
    trace_append(array('3', 'comment_delete', $_SESSION['album'], $_SESSION['asset'], $input['thread_id'], $id));
    
    return thread_details_update();
}
