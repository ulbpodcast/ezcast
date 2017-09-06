<?php

function index($param = array())
{
    global $input;

    if (!db_user_delete($input['user_ID'])) {
        redirectToController('view_user_details');
        return;
    }

    db_unlink_user($input['user_ID']);
    db_log(db_gettable('users'), 'Removed internal user ' . $input['user_ID'], $_SESSION['user_login']);
    $input['action'] = 'view_users';
    redirectToController('view_users');

    notify_changes();
}
