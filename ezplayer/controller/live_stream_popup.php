<?php

/**
 * renders a modal window related to a live stream
 * This window is displayed according to specific actions (video_switch | ...)
 * @global array $input
 */
function index($param = array())
{
    global $input;

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    switch ($input['display']) {
        case 'video_switch':
            include_once template_getpath('popup_streaming_video_switch.php');
            break;
    }
}
