<?php

function index($param = array())
{
    //TODO: DO THIS FUNCTION
    // Save additional users into ezmanager
    push_users_to_ezmanager();
    // Save changes to classroom into ezmanager
    push_classrooms_to_ezmanager();
    
    // Save users & courses into recorder
    $failed_cmd = array();
    push_users_courses_to_recorder($failed_cmd);

    // Save admins into ezmanager & recorders
    $res = push_admins_to_recorders_ezmanager();

    if (!$res) {
        echo '<div class="alert alert-warning">' . template_get_message('push_to_recorders_unsuccessful', get_lang()) . '</div>';
    }
    // Remove "save changes" alert
    notify_changes(false);

    db_log('', 'Pushed changes on recorders and ezmanager', $_SESSION['user_login']);
    include template_getpath('div_main_header.php');
    echo '<div class="alert alert-success"> ' . template_get_message('save_successful', get_lang()) . '</div>';
    foreach ($failed_cmd as $error) {
        echo '<div class="alert alert-warning"> FAILURE: ' . htmlspecialchars($error). '</div>';
    }
    include template_getpath('div_main_footer.php');
}
