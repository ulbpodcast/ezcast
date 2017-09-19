<?php

function index($param = array())
{
    $failed_cmd = array();
    //TODO: DO THIS FUNCTION
    // Save additional users into ezmanager
    push_users_to_ezmanager($failed_cmd);
    // Save changes to classroom into ezmanager
    push_classrooms_to_ezmanager($failed_cmd);
    // Save users & courses into recorder
    push_users_courses_to_recorder($failed_cmd);

    // Save admins into ezmanager & recorders
    push_admins_to_recorders_ezmanager($failed_cmd);
    
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
