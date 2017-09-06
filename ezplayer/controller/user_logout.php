<?php

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function index($param = array())
{
    logout();
    global $ezplayer_url;
   
    // Displaying the logout message

    include_once template_getpath('logout.php');

    unset($_SESSION['lang']);
}
