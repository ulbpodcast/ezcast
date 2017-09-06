<?php

/**
 * Renders a modal window related to an album
 * This window is displayed according to specific actions (delete | rss feed | ...)
 * @global array $input
 * @global type $repository_path
 * @global type $ezmanager_url
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $ezmanager_url;

    ezmam_repository_path($repository_path);
    $album = acl_token_get($input['album']);

    $album['rss'] = $ezmanager_url . "/distribute.php?action=rss&album=" . $album['album'] . "&quality=ezplayer&token=" . $album['token'];

    switch ($input['display']) {
        case 'delete':
            include_once template_getpath('popup_album_delete.php');
            break;
        case 'rss':
            include_once template_getpath('popup_album_rss_share.php');
            break;
    }
}
