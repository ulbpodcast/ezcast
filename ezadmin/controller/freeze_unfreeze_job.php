<?php


function index($param = array())
{
    global $input;

    if (!session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    $infos = scheduler_job_info_get($input['job']);
    if ($infos['status'] == 'frozen') {
        scheduler_unfreeze($input['job']);
    } elseif ($infos !== null) { // In queue = not frozen
        scheduler_freeze($input['job']);
    }

    redirectToController('view_queue');
}
