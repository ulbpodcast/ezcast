<?php

function index($param = array())
{
    global $input;

    if (!renderer_delete(trim($input['name']))) {
        echo json_encode(array('error' => '1'));
    } else {
        echo json_encode(array('success' => '1'));
        db_log("renderers", 'Deleted renderer ' . $input['name'], $_SESSION['user_login']);
        push_renderers_to_ezmanager();
        //    notify_changes();
    }
}
