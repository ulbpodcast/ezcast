<?php

function index($param = array())
{
    global $input;

    if (!isset($input['user_ID']) || empty($input['user_ID'])) {
        die;
    }

    remove_admin_from_file($input['user_ID']);

    db_log(db_gettable('users'), 'Denied admin rights to ' . $input['user_ID'], $_SESSION['user_login']);

    echo json_encode(array('success' => 1));
    die;
}
