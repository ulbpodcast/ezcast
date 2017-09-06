<?php

function index($param = array())
{
    global $input;

    if (!isset($input['user_ID']) || empty($input['user_ID'])) {
        die;
    }

    $res = add_admin_to_file($input['user_ID']);
    $u = db_user_read($input['user_ID']);

    if (!$res) {
        echo json_encode(array(
            'error' => '1'
        ));
        die;
    }

    echo json_encode(array(
        'user_ID' => $input['user_ID'],
        'forename' => $u['forename'],
        'surname' => $u['surname']
    ));

    db_log(db_gettable('users'), 'Gave admin rights to ' . $input['user_ID'], $_SESSION['user_login']);

    die;
}
