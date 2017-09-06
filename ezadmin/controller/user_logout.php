<?php

function index($param = array())
{
    global $ezadmin_url;

    // 2) Unsetting session vars
    unset($_SESSION['podcastcours_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['podcastcours_logged']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezadmin_url;

    unset($_SESSION['lang']);
}
