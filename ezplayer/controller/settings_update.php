<?php

/**
 * Updates user preferences
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;
    global $ezplayer_url;

    $display_new_video_notification = ((isset($input['display_new_video_notification']) &&
            $input['display_new_video_notification'] === 'on') ? '1' : '0');
    $display_threads = ((isset($input['display_threads']) && $input['display_threads'] === 'on') ? '1' : '0');
    $display_thread_notification = ((isset($input['display_thread_notification']) && $input['display_thread_notification'] === 'on') ? '1' : '0');

    user_prefs_settings_update($_SESSION['user_login'], "display_new_video_notification", $display_new_video_notification);
    user_prefs_settings_update($_SESSION['user_login'], "display_threads", $display_threads);
    user_prefs_settings_update($_SESSION['user_login'], "display_thread_notification", $display_thread_notification);

    acl_update_settings();

    trace_append(array('0', 'preferences_update', $display_new_video_notification, $display_threads, $display_thread_notification));

    if ($display_new_video_notification) {
        // updates the watched videos count
        acl_update_watched_assets();
    } else {
        unset($_SESSION['acl_watched_assets']);
    }

    // loads the previous action
    $input['action'] = $_SESSION['ezplayer_mode'];
    $input['no_trace'] = true;
    
    $new_url = $ezplayer_url . '/index.php?'. http_build_query($input);

    // Displaying the previous page
    header("Location: " . $new_url);
    //load_page();
}
