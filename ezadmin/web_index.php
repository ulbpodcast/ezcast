<?php

/**
 * ezcast podcast manager main program (MVC Controller)
 *
 * @package ezcast.ezadmin.main
 */

//
// Check whether the product has been installed
//
if (!file_exists('config.inc')) {
    header('Location: install.php');
    return;
}

require_once 'config.inc';
session_name($appname);
session_start();
require_once 'lib_statistics.php';
require_once __DIR__ . '/../commons/lib_sql_management.php';
require_once __DIR__.'/../commons/lib_error.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_template.php';
require_once '../commons/lib_various.php';
require_once 'lib_various.php';
require_once __DIR__.'/../commons/lib_scheduling.php';

$input = array_merge($_GET, $_POST);

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');
//
// Login/logout
//
// If we're not logged in, we try to log in or display the login form
if (!user_logged_in()) {
    // Step 2: Logging in a user who already submitted the form
    if (isset($input['action']) && $input['action'] == 'login') {
        if (!isset($input['login']) || !isset($input['passwd'])) {
            error_print_message(template_get_message('empty_username_password', get_lang()));
            die;
        }

        user_login($input['login'], $input['passwd']);
    }
    // This is a tricky case:
    // If we do not have a session, but we have an action, that means we lost the
    // session somehow and are trying to load part of a page through AJAX call.
    // We do not want the login page to be displayed randomly inside a div,
    // so we refresh the whole page to get a full-page login form.
    elseif (isset($input['action']) && $input['action'] != 'login' && $input['action'] != 'logout') {
        view_login_form();
    }
    // Step 1: Displaying the login form
    // (happens if no "action" is provided)
    else {
        view_login_form();
    }
    // At this point of the code, the user is supposed to be logged in.
// We check whether they specified an action to perform. If not, it means they landed
// here through a page reload, so we check the session variables to restore the page as it was.
} elseif (isset($_SESSION['podcastcours_logged']) && (!isset($input['action']) || empty($input['action']))) {
    redraw_page();
}

// At this point of the code, the user is logged in and explicitly specified an action.
// We perform the action specified.
else {
    $action = $input['action'];
    $redraw = false;

    //
    // Actions
    //
    // Controller goes here
    //var_dump($action);

    $paramController = array();
    switch ($action) {
        // In case we want to log out
        case 'logout':
            requireController('user_logout.php');
            break;

        // The only case when we could possibly arrive here with a session created
        // and a "login" action is when the user refreshed the page. In that case,
        // we redraw the page with the last information saved in the session variables.
        case 'login':
            redraw_page();
            break;
        
        case 'view_logs':
            requireController('view_logs.php');
            break;

        case 'edit_config':
            requireController('edit_config.php');
            break;

        case 'edit_admins':
            requireController('edit_admins.php');
            break;

        case 'add_admin':
            requireController('add_admin.php');
            break;

        case 'remove_admin':
            requireController('remove_admin.php');
            break;

        case 'push_changes':
            requireController('push_changes.php');
            break;

        case 'create_user':
            requireController('create_user.php');
            break;

        case 'remove_user':
            requireController('remove_user.php');
            break;

        case 'view_users':
            requireController('view_users.php');
            break;

        case 'view_user_details':
            requireController('view_user_details.php');
            break;

        case 'link_unlink_course_user':
            requireController('link_unlink_course_user.php');
            break;

        case 'create_course':
            requireController('create_course.php');
            break;

        case 'remove_course':
            requireController('remove_course.php');
            break;

        case 'view_courses':
            requireController('view_courses.php');
            break;

        case 'view_course_details':
            requireController('view_course_details.php');
            break;

        case 'link_unlink_user_course':
            requireController('link_unlink_user_course.php');
            break;

        case 'create_classroom':
            requireController('create_classroom.php');
            break;

        case 'view_classrooms':
            requireController('view_classrooms.php');
            break;

        case 'view_classroom_details': // Must be remove ? Never used
            requireController('view_classroom_details.php');
            break;

        case 'enable_classroom':
            $paramController[] = true;
            requireController('disable_enable_classroom.php');
            break;

        case 'disable_classroom':
            $paramController[] = false;
            requireController('disable_enable_classroom.php');
            break;

        case 'remove_classroom':
            requireController('remove_classroom.php');
            break;

        case 'view_renderers':
            requireController('view_renderers.php');
            break;

        case 'enable_renderer':
            $paramController[] = true;
            requireController('disable_enable_renderer.php');
            break;

        case 'disable_renderer':
            $paramController[] = false;
            requireController('disable_enable_renderer.php');
            break;

        case 'create_renderer':
            requireController('create_renderer.php');
            break;

        case 'remove_renderer':
            requireController('remove_renderer.php');
            break;

        case 'view_queue':
            requireController('view_queue.php');
            break;
        case 'view_renderer_logs':
            requireController('view_renderer_logs.php');
            break;
        case 'free_unfreeze_job':
            requireController('freeze_unfreeze_job.php');
            break;

        case 'job_priority_up':
            requireController('job_priority_up.php');
            break;

        case 'job_priority_down':
            requireController('job_priority_down.php');
            break;

        case 'job_kill':
            requireController('job_kill.php');
            break;

        case 'sync_externals':
            requireController('sync_externals.php');
            break;

        case 'view_stats_ezplayer_threads':
            requireController('view_stats_threads.php');
            break;

        case 'get_month_stats':
            requireController('stat_get_by_month.php');
            break;

        case 'get_nDays_stats':
            requireController('stat_get_by_nDays.php');
            break;

        case 'get_csv_assets':
            requireController('stat_csv_by_asset.php');
            break;

        case 'view_report':
            requireController('view_report.php');
            break;
        
        // Monitoring
        case 'view_events':
            requireController('view_list_event.php');
            break;
        
        case 'view_track_asset':
            requireController('view_track_asset.php');
            break;
        
        case 'view_classroom_calendar':
            requireController('view_classroom_calendar.php');
            break;
        
        case 'view_event_calendar':
            requireController('view_event_calendar.php');
            break;
        
        // Service ping classroom
        case 'get_classrooms_status':
            requireController('get_classrooms_status.php');
            break;
        
        
        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            albums_view();
    }
    
    // Call the function to view the page
    index($paramController);
    
    db_close();
}

//
// Helper functions
//

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in()
{
    return isset($_SESSION['podcastcours_logged']);
}

//
// Display functions
//

/**
 * Displays the login form
 */
function view_login_form()
{
    global $ezadmin_url;
    global $error, $input;

    //check if we receive a no_flash parameter (to disable flash progressbar on upload)
    $url = $ezadmin_url;
    // template include goes here
    include_once template_getpath('login.php');
    //include_once "tmpl/fr/login.php";
}


/**
 * Displays the main frame, without anything on the right side
 */
function albums_view()
{
    // TODO
    include_once template_getpath('main.php');
}

/**
 * This function is called whenever the user chose to refresh the page.
 * It reloads the page as it was
 */
function redraw_page()
{
    // Update stuff
    // Whatever happens, the first thing to do is display the whole page.
    albums_view();
}



/**
 * Reloads the whole page
 *
 * TODO NOT USED
 */
//function refresh_page() {
//    global $ezadmin_url;
//    // reload the page
//    echo '<script>window.location.reload();</script>';
//    die;
//}

//
// "Business logic" functions
//

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_login($login, $passwd)
{
    global $input;
    global $template_folder;
    global $error;
    global $ezadmin_url;

    // 0) Sanity checks
    if (empty($login) || empty($passwd)) {
        $error = template_get_message('empty_username_password', get_lang());
        view_login_form();
        die;
    }

    $login_parts = explode("/", $login);

    // checks if runas
    if (count($login_parts) >= 2) {
        $error = "No runas here !";
        view_login_form();
        die;
    }

    if (!file_exists('admin.inc')) {
        $error = "No admin list present";
        view_login_form();
        die;
    }
    include 'admin.inc'; //file containing an assoc array of admin users
    if (!isset($users[$login_parts[0]])) {
        $error = "User not authorized. <br/> Check admin.inc file.";
        view_login_form();
        die;
    }

    $res = checkauth(strtolower($login), $passwd);
    if (!$res) {
        $error = checkauth_last_error();
        view_login_form();
        die;
    }

    // 1) Initializing session vars
    $_SESSION['podcastcours_logged'] = "LEtimin"; // "boolean" stating that we're logged
    $_SESSION['user_login'] = $login;
    $_SESSION['user_real_login'] = $res['real_login'];
    $_SESSION['user_full_name'] = $res['full_name'];
    $_SESSION['user_email'] = $res['email'];

    // 3) Setting correct language
    set_lang($input['lang']);

    // 4) Resetting the template path to the one of the language chosen
    template_repository_path($template_folder . get_lang());

    // 5) Logging the login operation
    log_append("login");

    // 6) Displaying the page

    header("Location: " . $ezadmin_url);
    albums_view();
}


///// NOTIFICATION ALERT /////

/**
 * Changes have been made but not saved yet: we display an alert
 */
function notify_changes($enable = true)
{
    if ($enable) {
        $_SESSION['changes_to_push'] = true;
    } else {
        unset($_SESSION['changes_to_push']);
    }
}
