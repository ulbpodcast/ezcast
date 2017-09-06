<?php

/**
 * Resets the RSS feed for both high and low res, and displays a confirmation message to the user
 */
function index($param = array())
{
    global $input;
    global $repository_path;

    ezmam_repository_path($repository_path);

    if (!isset($input['album'])) {
        echo "Usage: index.php?action=reset_rss&album=ALBUM";
        die;
    }

    if (!ezmam_album_exists($input['album'])) {
        echo 'album ! ' . $input['album'];
        error_print_message(ezmam_last_error());
        die;
    }
    
    //
    // We just have to reset the tokens ...
    //
    ezmam_album_token_reset($input['album']);
    $album = $input['album'];
    
    //
    // ... And display a confirmation message!
    //
    require_once template_getpath('popup_rss_successfully_reset.php');
}
