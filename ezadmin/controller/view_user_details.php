<?php

function index($param = array())
{
    global $input;

    if (empty($input['user_ID'])) {
        die;
    }

    if (isset($input['post'])) {
        $user_ID = $input['user_ID'];
        $forename = $input['forename'];
        $surname = $input['surname'];
        $is_ezadmin = $input['is_ezadmin'] ? 1 : 0;
        $is_admin = $input['permissions'] ? 1 : 0;
        $recorder_passwd = trim($input['recorder_passwd']);

        if (empty($forename)) {
            $error = template_get_message('missing_forename', get_lang());
        } elseif (empty($surname)) {
            $error = template_get_message('missing_surname', get_lang());
        } else {
            db_user_update($user_ID, $surname, $forename, $recorder_passwd, $is_admin);
            if ($is_ezadmin) {
                add_admin_to_file($input['user_ID']);
            } else {
                remove_admin_from_file($input['user_ID']);
            }
            db_log(db_gettable('users'), 'Edited user ' . $input['user_ID'], $_SESSION['user_login']);
        }
        notify_changes();
    }

    include 'admin.inc';

    $userinfo = db_user_read($input['user_ID']);

    if ($userinfo) {
        $courses = db_user_get_courses($input['user_ID']);

        // Manipulate info
        $user_ID = $userinfo['user_ID'];
        $surname = $userinfo['surname'];
        $forename = $userinfo['forename'];
        $passNotSet = (isset($userinfo['passNotSet'])) ? $userinfo['passNotSet'] : '';
        $origin = $userinfo['origin'];
        $is_admin = ($userinfo['permissions'] != 0);
        $in_classroom = false;
        $is_ezadmin = array_key_exists($user_ID, $users);

        //$in_classroom true if user has any course in classrooms
        foreach ($courses as $c) {
            if ($c['in_recorders'] != '0') {
                $in_classroom = true;
                break;
            }
        }
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_user_details.php');
    include template_getpath('div_main_footer.php');
}
