<?php

/**
 * Used to update a thread information
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;
    
    $thread_id = $input['thread_id'];
    $thread_message = surround_url($input['thread_message'] . edited_on());
    $thread_timecode = intval($input['thread_timecode']);
    $thread_title = htmlspecialchars($input['thread_title']);
    $album = $input['thread_album'];
    $asset = $input['thread_asset'];

    $_SESSION['current_thread '] = $thread_id;

    // remove php and javascript tags
    $thread_message = safe_text($thread_message);

    thread_update($thread_id, $thread_title, $thread_message, $thread_timecode, $album, $_SESSION['user_full_name']);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    trace_append(array('3', 'thread_edit', $album, $asset, $thread_timecode, $thread_id));

    return thread_details_update();
}
