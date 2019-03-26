<?php

function index($param = array())
{
    global $input;
    global $input_validation_regex;
    global $recorder_user;
    global $recorder_basedir;
    global $recorder_subdir;

    $error = false;
    $room_ID = "";
    $name = "";
    $ip = "";
    $ip_remote = "";
    $enabled = 0;
    $ignore_ssh_check = false;
    
    if (!session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    if (isset($input) && isset($input['create']) && $input['create']) {

        $room_ID = $input['room_ID'];
        $name = $input['name'];
        $ip = $input['ip'];
        $ip_remote = isset($input['ip_remote']) ? $input['ip_remote'] : '';

        $user_name=$input['user_name'];
        $base_dir=$input['base_dir'];
        $sub_dir=$input['sub_dir'];
    

        $enabled = (isset($input['enabled']) && $input['enabled']) ? 1 : 0;
        $ignore_ssh_check = (isset($input['ignore_ssh_check']) && $input['ignore_ssh_check']) ? 1 : 0;
        $success = false;

        if (mb_strlen($input['room_ID']) > 20) {
            $error = template_get_message('error_validation_max_size_name', get_lang());

        } elseif (!check_validation_text($room_ID)) {
                $error = template_get_message('error_validation_roomID', get_lang());
        }

        if (!check_validation_text($name)) {
            $newError = template_get_message('error_validation_name', get_lang());
            if($error) {
                $error .= "<br>".$newError;
            } else {
                $error = $newError;
            }
        }

        if (!$ignore_ssh_check)
        {
            $master_ok = check_classroom_ssh_access($ip,$user_name);
            $slave_ok = true;
            if ($ip_remote != '') {
                $slave_ok = check_classroom_ssh_access($ip_remote,$user_name);
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
            } elseif (empty($user_name)) {
                $error = template_get_message('missing_user_name', get_lang());
            } elseif (empty($base_dir)) {
                $error = template_get_message('missing_base_dir', get_lang());
            } elseif (empty($sub_dir)) {
                $error = template_get_message('missing_sub_dir', get_lang());
            } else {
                $success = db_classroom_create($room_ID, $name, $ip, $ip_remote, $user_name, $base_dir, $sub_dir, $enabled);
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
    }else{
        $user_name=$recorder_user;
        $base_dir=$recorder_basedir;
        $sub_dir=$recorder_subdir;
    }
    
    include template_getpath('div_main_header.php');
    include template_getpath('div_create_classroom.php');
    include template_getpath('div_main_footer.php');
}

function check_classroom_ssh_access($ip,$recorder_user)
{
    //global $recorder_user;
    
    $return_status = false;
    system("ssh -o ConnectTimeout=5 -q $recorder_user@$ip exit", $return_status);

    return $return_status == 0;
}
