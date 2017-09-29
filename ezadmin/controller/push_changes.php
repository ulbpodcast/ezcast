<?php

require_once(__DIR__.'/../lib_push_changes.php');

function index($param = array())
{
    global $logger;
    
    $failed_cmd = push_changes();
    
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
