<?php

function index($param = array())
{
    global $input;

    if (isset($input['update'])) {
        include template_getpath('div_db_updater_home.php');
    } else {
        include template_getpath('div_db_updater_updating.php'); //NYI
    }
}
