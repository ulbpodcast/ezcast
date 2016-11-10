<?php

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function index($param = array()) {
    global $ezplayer_url;
    // 1) Deleting the ACLs from the session var
    log_append("logout");
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'logout'));
    acl_exit();

    // 2) Unsetting session vars
    unset($_SESSION['ezplayer_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['ezplayer_logged']); // "boolean" stating that we're logged
    unset($_SESSION['ezplayer_anonymous']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezplayer_url;

    unset($_SESSION['lang']);
}