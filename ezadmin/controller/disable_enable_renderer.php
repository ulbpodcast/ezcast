<?php

function index($param = array()) {
    global $input;
    $enable = (count($param) == 1 && $param[0]);

    if (empty($input['name']))
        die;

    if (renderer_update_enabled($input['name'], $enable, $error))
        echo json_encode(array('succes' => '1'));
    else
        echo json_encode(array('error' => '1'));

    if ($enable) {
        db_log("renderers", 'Enabled renderer ' . $input['name'], $_SESSION['user_login']);
    } else {
        db_log("renderers", 'Disabled renderer ' . $input['name'], $_SESSION['user_login']);
    }
    push_renderers_to_ezmanager();
    //   notify_changes();
}