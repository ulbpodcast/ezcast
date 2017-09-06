<?php

/**
 * Displays the help page
 */
function index($param = array())
{
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, 'view_help'));
    require_once template_getpath('help.php');
}
