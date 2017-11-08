<?php

/**
 * Used to post a message in the chat
 * @global type $input
 * @return boolean
 */


require_once(__DIR__."/../lib_streaming.php");

function index($param = array())
{
    global $input;
    global $repository_path;

    ezmam_repository_path($repository_path);

    $album = $input['chat_album'];
    $asset = $input['chat_asset'];
    if (!isset($album) || $album == '') {
        $album = $_SESSION['album'];
    }
    if (!isset($asset) || $asset == '') {
        $asset = $_SESSION['asset'];
    }
    $asset_meta = ezmam_asset_metadata_get($album, $asset);
    $timecode = intval($input['chat_timecode']);
    // removes private message for anonymous
    if (substr(rtrim($input['chat_message']), 0, strlen('@anon ')) === '@anon ') {
        $message = substr(rtrim($input['chat_message']), strlen('@anon '));
    } else {
        $message = rtrim($input['chat_message']);
    }
    $message = surround_url($message);

    if (is_nan($timecode)) {
        $timecode = 0;
    }

    // remove php and javascript tags
    $message = safe_text($message);
    $message = str_replace(PHP_EOL, '<br/>', $message); // TODO why not nl2br ?
    
    $record_date = $asset_meta['record_date'];
    if ($album == '' || $message == '' || $record_date == '') {
        return false;
    }
    
    $values = array(
        "message" => $message,
        "timecode" => $timecode,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "albumName" => $album,
        "assetName" => $record_date
    );

    message_insert($values);

    cache_asset_chat_unset($album, $asset);

    trace_append(array('3', 'chat_message_add', $album, $record_date, $timecode, $message));  
    asset_streaming_chat_get_last(true);
    return asset_streaming_player_update(false);
}
