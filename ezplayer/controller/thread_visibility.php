<?php

/**
 * renders a modal window for the thread visibility choice
 */
function index($param = array())
{
	global $input;

	if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    include_once template_getpath('popup_thread_visibility_choice.php');
}
