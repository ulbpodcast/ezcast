<?php

function index($param = array())
{
    global $input;

    if (!session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    if (isset($input['update'])) {
        include template_getpath('div_db_updater_home.php');
    } else {
        include template_getpath('div_db_updater_updating.php'); //NYI
    }
}
