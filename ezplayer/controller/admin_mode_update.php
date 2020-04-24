<?php

/**
 * Enables/disables Admin mode
 * @return boolean
 */
function index($param = array())
{
    global $input;
    //global $ezplayer_url;

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    // if user is admin, changes the admin mode status
    if (acl_admin_user()) {
        $_SESSION['admin_enabled'] = !$_SESSION['admin_enabled'];
    }

    // gets the previous action
    $input['action'] = $_SESSION['ezplayer_mode'];

    /*if (count($input) > 0) {
        $ezplayer_url .= '/index.php?';
        foreach ($input as $key => $value) {
            $ezplayer_url .= "$key=$value&";
        }
    }*/

    $url_now = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    trace_append(array('0', 'admin_mode_update', $_SESSION['admin_enabled']));

    // Displaying the previous page
    header("Location: " . $url_now);
    load_page();
}
