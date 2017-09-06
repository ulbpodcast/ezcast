<?php

/**
 * Used to edit a comment
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;
    $comment_id = $input['comment_id'];
    $comment_message = surround_url($input['comment_message'] . edited_on());
    $album = $input['album'];
    $asset = $input['asset'];
    $thread = $input['thread_id'];

    // remove php and javascript tags
    $comment_message = safe_text($comment_message);

    comment_update($comment_id, $comment_message, $album, $asset, $thread, $_SESSION['user_full_name']);
    comment_approval_remove($comment_id);
    vote_delete($comment_id);

    $_SESSION['current_thread'] = $thread;
    trace_append(array('3', 'comment_edit', $album, $asset, $thread, $comment_id));
    
    return thread_details_update();
}
