<?php


function index($param = array())
{
    global $input;
    
    if (count($param) == 1) {
        switch ($param[0]) {
            case 'add':
                thread_add();
                break;
            
            case 'delete':
                thread_delete();
                break;
            
            default:
                // TO DO: error
                break;
        }
    }
    
    
    
    $album = $input['album'];
    $asset = $input['asset'];
    
    if (!isset($album) || $album == '' || $album == 'undefined') {
        $album = $_SESSION['album'];
    }
    
    if (!isset($asset) || $asset == '' || $asset == 'undefined') {
        $asset = $_SESSION['asset'];
    }
    
    if (acl_display_threads()) {
        $threads = threads_select_by_asset($album, $asset);
    }
    
    $_SESSION['current_thread'] = '';
    include_once template_getpath('div_threads_list.php');
}


/**
 * Used to post a thread
 * @global type $input
 * @return boolean
 */
function thread_add()
{
    global $input;

    $thread_album = $input['album'];
    $thread_asset = $input['asset'];
    $thread_asset_title = $input['assetTitle'];
    $thread_timecode = intval($input['timecode']);
    $thread_title = htmlspecialchars($input['title']);
    $thread_description = surround_url($input['description']);
    $thread_visibility = (array_key_exists('visibility', $input) && $input['visibility'] == "on") ? '1' : '0';

    if (!acl_user_is_logged()) {
        return false;
    }
    if (is_nan($thread_timecode)) {
        $thread_timecode = 0;
    }

    // remove php and javascript tags
    $thread_description = safe_text($thread_description);

    $values = array(
        "title" => $thread_title,
        "message" => $thread_description,
        "timecode" => $thread_timecode,
        "authorId" => $_SESSION['user_login'],
        "authorFullName" => $_SESSION['user_full_name'],
        "creationDate" => date('Y-m-d H:i:s'),
        "lastEditDate" => date('Y-m-d H:i:s'),
        "studentOnly" => $thread_visibility,
        "albumName" => $thread_album,
        "assetName" => $thread_asset,
        "assetTitle" => $thread_asset_title
    );

    thread_insert($values);

    cache_asset_threads_unset($thread_album, $thread_asset);
    cache_album_threads_unset($thread_album);

    trace_append(array('3', 'thread_add', $thread_album, $thread_asset, $thread_timecode, $thread_title, $thread_visibility));
}


/**
 * Used to remove a thread
 * @global array $input
 * @return boolean
 */
function thread_delete()
{
    global $input;

    $id = $input['thread_id'];
    $album = $input['thread_album'];
    $asset = $input['thread_asset'];

    if (!acl_is_admin()) {
        return false;
    }
    if (!isset($album) || $album == '') {
        $album = $_SESSION['album'];
    }
    if (!isset($asset) || $asset == '') {
        $asset = $_SESSION['asset'];
    }

    thread_delete_by_id($id, $album, $asset);
    cache_asset_threads_unset($album, $asset);
    cache_album_threads_unset($album);

    trace_append(array('3', 'thread_delete', $album, $asset, $id));
}
