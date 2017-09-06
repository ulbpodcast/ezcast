<?php

function index($param = array())
{
    global $input;
    $enable = (count($param) == 1 && $param[0]);
    
    if (empty($input['id'])) {
        die;
    }
    $classroom = trim($input['id']);
    
    if (db_classroom_update_enabled($classroom, $enable)) {
        echo json_encode(array('success' => '1'));
    } else {
        echo json_encode(array('error' => '1'));
    }

    if ($enable) {
        db_log(db_gettable('classrooms'), 'Enabled classroom ' . $classroom, $_SESSION['user_login']);
    } else {
        db_log(db_gettable('classrooms'), 'Disabled classroom ' . $classroom, $_SESSION['user_login']);
    }
    notify_changes();
}
