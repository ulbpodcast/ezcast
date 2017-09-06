<?php

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function index($param = array())
{
    global $ezmanager_url;
    // 1) Deleting the ACLs from the session var
    acl_exit();

    // 2) Unsetting session vars
    unset($_SESSION['podman_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['podman_logged']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezmanager_url;

    unset($_SESSION['lang']);
}
