<?php


function index($param = array())
{
    global $input;

    $res = media_submit_error($input['id']);
    if (!$res) {
        log_append('warning', 'upload_error: ' . ezmam_last_error());
        $array["error"] = ezmam_last_error();
        echo json_encode($array);
        die;
    }
}
