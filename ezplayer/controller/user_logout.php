<?php

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function index($param = array())
{
	global $ezplayer_url;
	global $input;

	if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    logout();
    
    // Displaying the logout message

    include_once template_getpath('logout.php');

    unset($_SESSION['lang']);
}
