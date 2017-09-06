<?php

function index($param = array())
{
    global $input;

    if (!db_classroom_delete(trim($input['id']))) {
        echo json_encode(array('error' => '1'));
    } else {
        echo json_encode(array('success' => '1'));
        db_log(db_gettable('classrooms'), 'Deleted classroom ' . $input['id'], $_SESSION['user_login']);
        notify_changes();
    }
}
