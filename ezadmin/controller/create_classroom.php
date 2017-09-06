<?php

function index($param = array())
{
    global $input;

    $error = false;
    $room_ID = "";
    $name = "";
    $ip = "";
    $ip_remote = "";
    $enabled = 0;
    $ignore_ssh_check = false;
    
    if (isset($input) && isset($input['create']) && $input['create']) {
        $room_ID = $input['room_ID'];
        $name = $input['name'];
        $ip = $input['ip'];
        $ip_remote = isset($input['ip_remote']) ? $input['ip_remote'] : '';
        $enabled = (isset($input['enabled']) && $input['enabled']) ? 1 : 0;
        $ignore_ssh_check = (isset($input['ignore_ssh_check']) && $input['ignore_ssh_check']) ? 1 : 0;
        $success = false;
        
        if (!$ignore_ssh_check) {
            $master_ok = check_classroom_ssh_access($ip);
            $slave_ok = true;
            if ($ip_remote != '') {
                $slave_ok = check_classroom_ssh_access($ip_remote);
            }
       
            if (!$master_ok) {
                $error = template_get_message('cannot_ssh_recorder', get_lang()) . ' (' . $ip . ')';
            } elseif (!$slave_ok) {
                $error = template_get_message('cannot_ssh_recorder', get_lang()) . ' (' . $ip_remote . ')';
            }
        }

        if (!$error) {
            if (empty($room_ID)) {
                $error = template_get_message('missing_room_id', get_lang());
            } elseif (empty($ip)) {
                $error = template_get_message('missing_ip', get_lang());
            } elseif (checkipsyntax($ip)) {
                $error = template_get_message('format_ip', get_lang());
            } else {
                $success = db_classroom_create($room_ID, $name, $ip, $ip_remote, $enabled);
            }
        }

        if ($success) {
            db_log(db_gettable('classrooms'), 'Created classroom ' . $input['room_ID'], $_SESSION['user_login']);
            notify_changes();
            redirectToController('view_classrooms');
            return;
        } else {
            if (!$error) {
                $error = template_get_message('db_request_failed', get_lang());
            }
        }
    }

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_classroom.php');
    include template_getpath('div_main_footer.php');
}

function check_classroom_ssh_access($ip)
{
    global $recorder_user;
    
    $return_status = false;
    system("ssh -o ConnectTimeout=5 -q $recorder_user@$ip exit", $return_status);

    return $return_status == 0;
}
