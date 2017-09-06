<?php

/**
 * Used to post a new comment
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;
    $album = $input['album'];
    $asset = $input['asset'];
    $comment_message = surround_url($input['message']);
    $comment_thread = $input['thread_id'];

    if (!acl_user_is_logged() && $album != '' && $asset != '' &&
            $comment_thread != '') {
        return false;
    }

    // remove php and javascript tags
    $comment_message = safe_text($comment_message);

    $values = array(
        "message" => $comment_message,
        "thread" => $comment_thread,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s')
    );
    comment_insert($values);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    $_SESSION['current_thread'] = $comment_thread;
    trace_append(array('3', 'comment_add', $album, $asset, $comment_thread));
    
    return thread_details_update();
}
