<?php

/**
 * Enables/disables Admin mode
 * @return boolean
 */
function index($param = array())
{
    global $input;
    global $ezplayer_url;

    // if user is admin, changes the admin mode status
    if (acl_admin_user()) {
        $_SESSION['admin_enabled'] = !$_SESSION['admin_enabled'];
    }

    // gets the previous action
    $input['action'] = $_SESSION['ezplayer_mode'];

    if (count($input) > 0) {
        $ezplayer_url .= '/index.php?';
        foreach ($input as $key => $value) {
            $ezplayer_url .= "$key=$value&";
        }
    }

    trace_append(array('0', 'admin_mode_update', $_SESSION['admin_enabled']));

    // Displaying the previous page
    header("Location: " . $ezplayer_url);
    load_page();
}
