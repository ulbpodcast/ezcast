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

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    include_once template_getpath('popup_bookmarks_upload.php');
}
