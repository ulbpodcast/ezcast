<?php


function index($param = array())
{
    global $input;

    $res = media_submit_error($input['id']);

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    if (!$res) {
        log_append('warning', 'upload_error: ' . ezmam_last_error());
        $array["error"] = ezmam_last_error();
        echo json_encode($array);
        die;
    }
}
