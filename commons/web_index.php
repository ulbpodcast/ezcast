<?php

require 'config.inc';
include "lib_template.php";
session_start();


$input = array_merge($_GET, $_POST);

    $action = (isset($input['action'])) ? $input['action'] : "";
    $redraw = false;


    switch ($action) {

        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            albums_view();
    }



/**
 * Displays the main frame, without anything on the right side
 */
function albums_view()
{
    // TODO
    // include_once template_getpath('main.php');
    echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>EZcast</title>
    </head>
    <body>Hello World</body>
</html>";
}
