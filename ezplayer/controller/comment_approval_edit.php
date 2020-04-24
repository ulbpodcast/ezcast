<?php

/**
 * Updates a comments's aproval
 * @global type $input
 */
function index($param = array())
{
    global $input;
    $commentId = intval($input['approved_comment']);

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    comment_update_approval($commentId);
    trace_append(array('3', 'comment_approval_edit', $_SESSION['album'], $_SESSION['asset'], $commentId));
    return thread_details_update();
}
