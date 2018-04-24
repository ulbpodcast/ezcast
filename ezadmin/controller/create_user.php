<?php

function index($param = array())
{
    global $input;

    if (isset($input['create']) && $input['create']) {
        $user_ID = $input['user_ID'];
        $surname = $input['surname'];
        $forename = $input['forename'];
        $recorder_passwd = $input['recorder_passwd'];
        $permissions = $input['permissions'] ? 1 : 0;
        $is_ezadmin = $input['is_ezadmin'] ? 1 : 0;

        $valid = false;
        if (empty($user_ID)) {
            $error = template_get_message('missing_user_ID', get_lang());
        } elseif (empty($input['recorder_passwd'])) {
            $error = template_get_message('missing_recorder_passwd', get_lang());
        } elseif (empty($forename)) {
            $error = template_get_message('missing_forename', get_lang());
        } elseif (empty($surname)) {
            $error = template_get_message('missing_surname', get_lang());
        } else {
            $valid = db_user_create($user_ID, $surname, $forename, $recorder_passwd, $permissions);
            if ($is_ezadmin) {
                add_admin_to_file($input['user_ID']);
            } else {
                remove_admin_from_file($input['user_ID']);
            }
            db_log(db_gettable('users'), 'Created user ' . $input['user_ID'], $_SESSION['user_login']);
        }

        if ($valid) {
            $input['user_ID'] = $user_ID;

            global $statements;
            redirectToController('view_user_details');
            return;
        }
    }

    notify_changes();

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_user.php');
    include template_getpath('div_main_footer.php');
}
