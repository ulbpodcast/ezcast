<?php

/**
 * Used to reply to a comment
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;

    $album = $input['album'];
    $asset = $input['asset'];
    $comment_message = surround_url($input['answer_message']);
    $comment_thread = $input['thread_id'];
    $comment_parent = intval($input['answer_parent']);

    if (!acl_user_is_logged() && $album != '' && $asset != '' && $comment_thread != '' && $comment_parent != '') {
        return false;
    }

    // remove php and javascript tags
    $comment_message = safe_text($comment_message);

    $values = array(
        "message" => $comment_message,
        "thread" => $comment_thread,
        "parent" => $comment_parent,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s')
    );
    comment_insert($values);

    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    $_SESSION['current_thread'] = $comment_thread;
    trace_append(array('3', 'comment_reply_add', $album, $asset, $comment_thread, $comment_parent));

    return thread_details_update();
}
