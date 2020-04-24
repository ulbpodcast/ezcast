<?php

/**
 * Displays the file input form
 * @global type $album
 * @global type $asset
 */
function index($param = array())
{
    global $album;
    global $asset;
    global $input;

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    include_once template_getpath('popup_bookmarks_upload.php');
}
