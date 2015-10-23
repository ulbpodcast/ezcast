<?php

/**
 * ezcast podcast manager main program (MVC Controller)
 *
 * @package ezcast.ezadmin.main
 */
/*
 * EZCAST EZadmin 
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 		    Arnaud Wijns <awijns@ulb.ac.be>
 *                   Antoine Dewilde
 *                   Thibaut Roskam
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//
// Check whether the product has been installed
//
if (!file_exists('config.inc')) {
    header('Location: install.php');
}

require 'config.inc';
session_name($appname);
session_start();
require_once 'lib_statistics.php';
require_once 'lib_sql_management.php';
require_once 'lib_error.php';
require_once '../commons/lib_auth.php';
require_once '../commons/lib_template.php';
require_once '../commons/lib_various.php';
require_once 'lib_various.php';
require_once 'lib_scheduling.php';


$input = array_merge($_GET, $_POST);

template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

//
// Login/logout
//
// If we're not logged in, we try to log in or display the login form
if (!user_logged_in()) {
    /* if (isset($input['action']) && $input['action'] == 'view_help'){
      view_help ();
      die;
      } */
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
    else if (isset($input['action']) && $input['action'] != 'login' && $input['action'] != 'logout') {
        view_login_form();
    }
    // Step 1: Displaying the login form
    // (happens if no "action" is provided)
    else {
        view_login_form();
    }
}

// At this point of the code, the user is supposed to be logged in.
// We check whether they specified an action to perform. If not, it means they landed
// here through a page reload, so we check the session variables to restore the page as it was.
else if (isset($_SESSION['podcastcours_logged']) && (!isset($input['action']) || empty($input['action']))) {
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

    if (strpos($action, '_list') !== false) {
        $table = strstr($action, '_list', true);
        $action = 'view_table';
    } else if (strpos($action, '_new' !== false)) {
        $table = strstr($action, '_new', true);
        $action = 'add_to_table';
    }

    switch ($action) {
        // In case we want to log out
        case 'logout':
            user_logout();
            break;

        // The only case when we could possibly arrive here with a session created
        // and a "login" action is when the user refreshed the page. In that case,
        // we redraw the page with the last information saved in the session variables.
        case 'login':
            redraw_page();
            break;

        case 'view_logs':
            view_logs();
            break;

        case 'edit_config':
            edit_config();
            break;

        case 'edit_admins':
            edit_admins();
            break;

        case 'add_admin':
            add_admin();
            break;

        case 'remove_admin':
            remove_admin();
            break;

        case 'push_changes':
            push_changes();
            break;

        case 'create_user':
            create_user();
            break;

        case 'remove_user':
            remove_user();
            break;

        case 'view_users':
            view_users();
            break;

        case 'view_user_details':
            view_user_details();
            break;

        case 'link_unlink_course_user':
            link_unlink_course_user();
            break;

        case 'create_course':
            create_course();
            break;

        case 'remove_course':
            remove_course();
            break;

        case 'view_courses':
            view_courses();
            break;

        case 'view_course_details':
            view_course_details();
            break;

        case 'link_unlink_user_course':
            link_unlink_user_course();
            break;

        case 'create_classroom':
            create_classroom();
            break;

        case 'view_classrooms':
            view_classrooms();
            break;

        case 'view_classroom_details':
            view_classroom_details();
            break;

        case 'enable_classroom':
            disable_enable_classroom(true);
            break;

        case 'disable_classroom':
            disable_enable_classroom(false);
            break;

        case 'remove_classroom':
            remove_classroom();
            break;

        case 'view_renderers':
            view_renderers();
            break;

        case 'enable_renderer':
            disable_enable_renderer(true);
            break;

        case 'disable_renderer':
            disable_enable_renderer(false);
            break;

        case 'create_renderer':
            create_renderer();
            break;

        case 'remove_renderer':
            remove_renderer();
            break;

        case 'view_queue':
            view_queue();
            break;

        case 'free_unfreeze_job':
            freeze_unfreeze_job();
            break;

        case 'job_priority_up':
            job_priority_up();
            break;

        case 'job_priority_down':
            job_priority_down();
            break;

        case 'job_kill':
            job_kill();
            break;

        case 'sync_externals':
            sync_externals();
            break;

        case 'view_stats_ezplayer_threads':
            view_stats_threads();
            break;

        case 'get_month_stats':
            stat_get_by_month();
            break;

        case 'get_nDays_stats':
            stat_get_by_nDays();
            break;

        case 'get_csv_assets':
            stat_csv_by_asset();
            break;

        // No action selected: we choose to display the homepage again
        default:
            // TODO: check session var here
            albums_view();
    }

    db_close();
}

//
// Helper functions
//

/**
 * Helper function
 * @return bool true if the user is already logged in; false otherwise
 */
function user_logged_in() {
    return isset($_SESSION['podcastcours_logged']);
}

//
// Display functions
//

/**
 * Displays the login form
 */
function view_login_form() {
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
function albums_view() {
    // TODO
    include_once template_getpath('main.php');
}

/**
 * This function is called whenever the user chose to refresh the page.
 * It reloads the page as it was
 */
function redraw_page() {
    // Update stuff
    // Whatever happens, the first thing to do is display the whole page.
    albums_view();
}

function view_courses() {
    global $input;

    $courses = array();

    if (isset($input['post'])) {
        $course_code = db_sanitize($input['course_code']);
        $teacher = db_sanitize($input['teacher']);
        $intern = $input['intern'] ? 1 : 0;
        $extern = $input['extern'] ? 1 : 0;
        $has_albums = $input['has_albums'] ? 1 : 0;
        $no_albums = $input['no_albums'] ? 1 : 0;
        $in_recorders = $input['in_recorders'] ? 1 : 0;
        $out_recorders = $input['out_recorders'] ? 1 : 0;
        $with_teacher = $input['with_teacher'] ? 1 : 0;
        $without_teacher = $input['without_teacher'] ? 1 : 0;
        $page = $input['page'];
        $col = $input['col'];
        $order = $input['order'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $courses = db_courses_search($course_code, $teacher, $extern, $intern, $has_albums == $no_albums ? -1 : $has_albums, $in_recorders == $out_recorders ? -1 : $in_recorders, $with_teacher == $without_teacher ? -1 : $with_teacher, $col . ' ' . $order, '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input['intern'] = 1;
        $input['extern'] = 1;
        $input['has_albums'] = 1;
        $input['no_albums'] = 1;
        $input['in_recorders'] = 1;
        $input['out_recorders'] = 1;
        $input['with_teacher'] = 1;
        $input['without_teacher'] = 1;
        $input['page'] = 1;
        $input['col'] = 'course_code';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_course.php');
    if (!empty($courses)) {
        include template_getpath('div_list_courses.php');
    }
    include template_getpath('div_main_footer.php');
}

function view_course_details() {
    global $input;

    if (empty($input['course_code']))
        die;

    if (isset($input['post'])) {
        $course_code = $input['course_code'];
        $course_name = $input['course_name'];
        $shortname = $input['shortname'];
        $in_recorders = $input['in_recorders'] ? 1 : 0;

        if (empty($course_name)) {
            $error = template_get_message('missing_course_name', get_lang());
        } else {
            db_course_update($course_code, $course_name, $shortname, $in_recorders);
            db_log(db_gettable('course'), 'Edited course ' . $input['course_ID'], $_SESSION['user_login']);
        }
    }

    $courseinfo = db_course_read($input['course_code']);
    $users = db_course_get_users($input['course_code']);

    // Manipulate info
    $course_code = $courseinfo['course_code'];
    $course_name = $courseinfo['course_name'];
    $shortname = $courseinfo['shortname'];
    $origin = $courseinfo['origin'];
    $has_albums = ($courseinfo['has_albums'] != '0');
    $in_classroom = ($courseinfo['in_recorders'] == '1');
    //$users = array();
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_course_details.php');
    include template_getpath('div_main_footer.php');
}

function link_unlink_user_course() {
    global $input;

    if (empty($input['course_code']))
        die;

    switch ($input['query']) {
        case 'link':
            $info = db_users_courses_create($input['course_code'], $input['id']);
            if (!$info) {
                echo json_encode(array('error' => '1'));
                return;
            }
            echo json_encode(array(
                'id' => $info['id'],
                'netid' => $info['user']['user_ID'],
                'name' => $info['user']['surname'] . ' ' . $info['forename'],
                'origin' => 'internal'
            ));
            db_log(db_gettable('users_courses'), 'Added link between user ' . $info['user']['user_ID'] . ' and course ' . $input['course_code'], $_SESSION['user_login']);
            break;
        case 'unlink':
            if (!db_users_courses_delete($input['id'])) {
                echo json_encode(array('error' => '1'));
            } else {
                echo json_encode(array('success' => '1'));
                db_log(db_gettable('classrooms'), 'Removed link between user ' . $input['user_ID'] . ' and course ' . $input['course_code'], $_SESSION['user_login']);
            }

            break;
        default:
            echo json_encode(array('error' => 'Unknown query'));
            break;
    }

    notify_changes();
}

function link_unlink_course_user() {
    global $input;

    if (empty($input['user_ID']))
        die;

    switch ($input['query']) {
        case 'link':
            $info = db_users_courses_create($input['id'], $input['user_ID']);
            if (!$info) {
                echo json_encode(array('error' => '1'));
                return;
            }
            echo json_encode(array(
                'id' => $info['id'],
                'course_code' => $info['course']['course_code'],
                'course_name' => $info['course']['course_name'],
                'origin' => 'internal'
            ));
            db_log(db_gettable('users_courses'), 'Added link between user ' . $input['user_ID'] . ' and course ' . $info['course']['course_code'], $_SESSION['user_login']);
            break;
        case 'unlink':
            if (!db_users_courses_delete($input['id'])) {
                echo json_encode(array('error' => '1'));
            } else {
                echo json_encode(array('success' => '1'));
                db_log(db_gettable('classrooms'), 'Removed link between user ' . $input['user_ID'] . ' and course ' . $input['course_code'], $_SESSION['user_login']);
            }
            break;
        default:
            echo json_encode(array('error' => 'Unknown query'));
            break;
    }

    notify_changes();
}

function view_users() {
    global $input;

    $users = array();

    if (isset($input['post'])) {
        $user_ID = db_sanitize($input['user_ID']);
        $forename = db_sanitize($input['forename']);
        $surname = db_sanitize($input['surname']);
        $intern = $input['intern'] ? 1 : 0;
        $extern = $input['extern'] ? 1 : 0;
        $is_admin = $input['is_admin'] ? 1 : 0;
        $is_not_admin = $input['is_not_admin'] ? 1 : 0;
        $page = intval($input['page']);
        $col = addslashes($input['col']);
        $order = $input['order'] == 'ASC' ? 'ASC' : 'DESC';
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $users = db_users_list($user_ID, $surname, $forename, ($intern == $extern) ? -1 : (($extern == 1) ? 'external' : 'internal'), ($is_admin == $is_not_admin) ? -1 : $is_admin, $col . ' ' . $order, '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input['intern'] = 1;
        $input['extern'] = 1;
        $input['is_admin'] = 1;
        $input['is_not_admin'] = 1;
        $input['page'] = 1;
        $input['col'] = 'user_ID';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_user.php');
    if (!empty($users)) {
        include template_getpath('div_list_users.php');
    }
    include template_getpath('div_main_footer.php');
}

function view_logs() {
    global $input;

    if (isset($input['post']) && !empty($input['post'])) {
        $page = $input['page'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $logs = db_logs_get($input['date_start'], $input['date_end'], $input['table'], $input['author'], '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_logs.php');
    if (!empty($logs)) {
        include template_getpath('div_list_logs.php');
    }
    include template_getpath('div_main_footer.php');
}

function view_user_details() {
    global $input;

    if (empty($input['user_ID']))
        die;

    if (isset($input['post'])) {
        $user_ID = $input['user_ID'];
        $forename = $input['forename'];
        $surname = $input['surname'];
        $is_ezadmin = $input['is_ezadmin'] ? 1 : 0;
        $is_admin = $input['permissions'] ? 1 : 0;
        $des_seed = chr(rand(33, 126)) . chr(rand(33, 126));
        $recorder_passwd = (trim($input['recorder_passwd']) == '') ? '' : crypt($input['recorder_passwd'], $des_seed);

        if (empty($forename)) {
            $error = template_get_message('missing_forename', get_lang());
        } else if (empty($surname)) {
            $error = template_get_message('missing_surname', get_lang());
        } else {
            db_user_update($user_ID, $surname, $forename, $recorder_passwd, $is_admin);
            if ($is_ezadmin)
                add_admin_to_file($input['user_ID']);
            else
                remove_admin_from_file($input['user_ID']);
            db_log(db_gettable('users'), 'Edited user ' . $input['user_ID'], $_SESSION['user_login']);
        }
    }

    include 'admin.inc';

    $userinfo = db_user_read($input['user_ID']);

    if ($userinfo) {
        $courses = db_user_get_courses($input['user_ID']);

        // Manipulate info
        $user_ID = $userinfo['user_ID'];
        $surname = $userinfo['surname'];
        $forename = $userinfo['forename'];
        $origin = $userinfo['origin'];
        $is_admin = ($userinfo['permissions'] != 0);
        $in_classroom = false; // TODO: CHANGE THIS!!!
        $is_ezadmin = array_key_exists($user_ID, $users);

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

function view_classrooms() {
    global $input;

    if (isset($input['update'])) {
        db_classroom_update($input['room_ID'], $input['u_room_ID'], $input['u_name'], $input['u_ip'], $input['u_ip_remote']);
    }

    if (isset($input['post'])) {
        $room_ID = db_sanitize($input['room_ID']);
        $name = db_sanitize($input['name']);
        $ip = db_sanitize($input['IP']);
        $enabled = $input['enabled'] ? 1 : 0;
        $not_enabled = $input['not_enabled'] ? 1 : 0;
        $page = $input['page'];
        $col = $input['col'];
        $order = $input['order'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $classrooms = db_classrooms_search($room_ID, $name, $ip, $enabled == $not_enabled ? -1 : $enabled, $col . ' ' . $order, '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input["enabled"] = 1;
        $input["not_enabled"] = 1;
        $input['page'] = 1;
        $input['col'] = 'room_ID';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_classroom.php');
    if (!empty($classrooms)) {
        include template_getpath('div_list_classrooms.php');
    }
    include template_getpath('div_main_footer.php');
}

/**
 *
 * @param type $enable If true: enable. If false: disable
 */
function disable_enable_classroom($enable) {
    global $input;

    if (empty($input['id']))
        die;

    if (db_classroom_update_enabled($input['id'], $enable))
        echo json_encode(array('succes' => '1'));
    else
        echo json_encode(array('error' => '1'));

    if ($enable) {
        db_log(db_gettable('classrooms'), 'Enabled classroom ' . $input['id'], $_SESSION['user_login']);
    } else {
        db_log(db_gettable('classrooms'), 'Disabled classroom ' . $input['id'], $_SESSION['user_login']);
    }
    notify_changes();
}

function edit_config() {
    global $input;

    if (isset($input['confirm']) && !empty($input['confirm'])) {
        $allow_recorder = ($input['recording_enabled'] == 'on');
        $add_users = ($input['add_users_enabled'] == 'on');
        $pwd_storage = ($input['password_storage_enabled'] == 'on');
        $use_course_name = ($input['courses_by_name'] == 'on');
        $use_user_name = ($input['users_by_name'] == 'on');

        update_config_file($allow_recorder, $add_users, $pwd_storage, $use_course_name, $use_user_name);

        $alert = '<div class="alert alert-success">' . template_get_message('save_successful', get_lang()) . '</div>';
    }

    $params = parse_config_file();
    //update_config_file(false, true, true);

    include template_getpath('div_main_header.php');
    include template_getpath('div_edit_config.php');
    include template_getpath('div_main_footer.php');
}

function edit_admins() {
    global $input;

    $admins = parse_admin_file();
    //update_config_file(false, true, true);

    include template_getpath('div_main_header.php');
    include template_getpath('div_list_admins.php');
    include template_getpath('div_main_footer.php');
}

function add_admin() {
    global $input;

    if (!isset($input['user_ID']) || empty($input['user_ID']))
        die;

    $res = add_admin_to_file($input['user_ID']);
    $u = db_user_read($input['user_ID']);

    if (!$res) {
        echo json_encode(array(
            'error' => '1'
        ));
        die;
    }

    echo json_encode(array(
        'user_ID' => $input['user_ID'],
        'forename' => $u['forename'],
        'surname' => $u['surname']
    ));

    db_log(db_gettable('users'), 'Gave admin rights to ' . $input['user_ID'], $_SESSION['user_login']);

    die;
}

function remove_admin() {
    global $input;

    if (!isset($input['user_ID']) || empty($input['user_ID']))
        die;

    remove_admin_from_file($input['user_ID']);

    db_log(db_gettable('users'), 'Denied admin rights to ' . $input['user_ID'], $_SESSION['user_login']);

    echo json_encode(array('success' => 1));
    die;
}

function push_changes() {
    //TODO: DO THIS FUNCTION
    // Save admins into ezmanager & recorders
    $res = push_admins_to_recorders_ezmanager();

    if (!$res) {
        echo '<div class="alert alert-warning">' . template_get_message('push_to_recorders_unsuccessful', get_lang()) . '</div>';
    }

    // Save users & courses into recorder
    push_users_courses_to_recorder();

    // Save additional users into ezmanager
    push_users_to_ezmanager();

    // Save changes to classroom into ezmanager
    push_classrooms_to_ezmanager();

    // Remove "save changes" alert
    remove_changes_alert();

    db_log('', 'Pushed changes on recorders and ezmanager', $_SESSION['user_login']);
    include template_getpath('div_main_header.php');
    echo '<div class="alert alert-success">' . template_get_message('save_successful', get_lang()) . '</div>';
    include template_getpath('div_main_footer.php');
}

/* function view_classroom_details() {
  global $input;

  if(empty($input['room_ID'])) die;

  $roominfo = db_classroom_read($input['room_ID']);

  // Manipulate info
  $room_ID = $courseinfo['room_ID'];
  $name = $courseinfo['name'];
  $ip = $courseinfo['IP'];
  $enabled = $courseinfo['enabled'];

  // Display page
  include template_getpath('div_main_header.php');
  include template_getpath('div_classroom_details.php');
  include template_getpath('div_main_footer.php');
  } */

function create_user() {
    global $input;

    if ($input['create']) {
        $user_ID = $input['user_ID'];
        $surname = $input['surname'];
        $forename = $input['forename'];
        $des_seed = chr(rand(33, 126)) . chr(rand(33, 126));
        $recorder_passwd = crypt($input['recorder_passwd'], $des_seed);
        $permissions = $input['permissions'] ? 1 : 0;
        $is_ezadmin = $input['is_ezadmin'] ? 1 : 0;

        $valid = false;
        if (empty($user_ID)) {
            $error = template_get_message('missing_user_ID', get_lang());
        } else if (empty($input['recorder_passwd'])) {
            $error = template_get_message('missing_recorder_passwd', get_lang());
            ;
        } else if (empty($forename)) {
            $error = template_get_message('missing_forename', get_lang());
            ;
        } else if (empty($surname)) {
            $error = template_get_message('missing_surname', get_lang());
            ;
        } else {
            $valid = db_user_create($user_ID, $surname, $forename, $recorder_passwd, $permissions);
            if ($is_ezadmin)
                add_admin_to_file($input['user_ID']);
            else
                remove_admin_from_file($input['user_ID']);
            db_log(db_gettable('users'), 'Created user ' . $input['user_ID'], $_SESSION['user_login']);
        }

        if ($valid) {
            $input['user_ID'] = $user_ID;

            global $statements;
            view_user_details();
            return;
        }
    }

    notify_changes();

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_user.php');
    include template_getpath('div_main_footer.php');
}

function view_stats_threads() {

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_stats_threads.php');
    include template_getpath('div_main_footer.php');
}

function remove_user() {
    global $input;

    if (!db_user_delete($input['user_ID'])) {
        view_user_details();
        return;
    }

    db_unlink_user($input['user_ID']);
    db_log(db_gettable('users'), 'Removed internal user ' . $input['user_ID'], $_SESSION['user_login']);
    $input['action'] = 'view_users';
    view_users();

    notify_changes();
}

function create_course() {
    global $input;

    if ($input['create']) {
        $course_code = $input['course_code'];
        $course_name = $input['course_name'];
        $shortname = $input['shortname'];

        $valid = false;
        if (empty($course_code)) {
            $error = template_get_message('missing_course_code', get_lang());
        } else if (empty($course_name)) {
            $error = template_get_message('missing_course_name', get_lang());
        } else {
            $valid = db_course_create($course_code, $course_name, $shortname);
        }

        if ($valid) {
            $input['course_code'] = $course_code;
            view_course_details();
            return;
        }
    }

    db_log(db_gettable('courses'), 'Created course ' . $input['course_code'], $_SESSION['user_login']);
    notify_changes();

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_course.php');
    include template_getpath('div_main_footer.php');
}

function remove_course() {
    global $input;

    if (!db_course_delete($input['course_code'])) {
        view_course_details();
        return;
    }

    db_unlink_course($input['course_code']);

    db_log(db_gettable('courses'), 'Deleted internal course ' . $input['course_code'], $_SESSION['user_login']);
    notify_changes();
    view_courses();
}

function create_classroom() {
    global $input;

    if ($input['create']) {
        $room_ID = $input['room_ID'];
        $name = $input['name'];
        $ip = $input['ip'];
        $ip_remote = isset($input['ip_remote']) ? $input['ip_remote'] : '';
        $enabled = $input['enabled'] ? 1 : 0;

        $valid = false;
        if (empty($room_ID)) {
            $error = template_get_message('missing_room_id', get_lang());
        } else if (empty($ip)) {
            $error = template_get_message('missing_ip', get_lang());
        } else if (checkipsyntax($ip)) {
            $error = template_get_message('format_ip', get_lang());
        } else {
            $valid = db_classroom_create($room_ID, $name, $ip, $ip_remote, $enabled);
        }

        if ($valid) {
            view_classrooms();
            db_log(db_gettable('classrooms'), 'Created classroom ' . $input['room_ID'], $_SESSION['user_login']);
            notify_changes();
            return;
        }
    }

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_classroom.php');
    include template_getpath('div_main_footer.php');
}

function remove_classroom() {
    global $input;

    if (!db_classroom_delete(trim($input['id']))) {
        echo json_encode(array('error' => '1'));
    } else {
        echo json_encode(array('success' => '1'));
        db_log(db_gettable('classrooms'), 'Deleted classroom ' . $input['id'], $_SESSION['user_login']);
        notify_changes();
    }
}

function view_renderers() {
    if (!file_exists('renderers.inc')) {
        $renderers = array();
    } else {
        $renderers = require_once 'renderers.inc';
    }

    require_once template_getpath('div_main_header.php');
    require_once template_getpath('div_list_renderers.php');
    require_once template_getpath('div_main_footer.php');
}

/**
 *
 * @param type $enable If true: enable. If false: disable
 */
function disable_enable_renderer($enable) {
    global $input;

    if (empty($input['name']))
        die;

    if (renderer_update_enabled($input['name'], $enable, $error))
        echo json_encode(array('succes' => '1'));
    else
        echo json_encode(array('error' => '1'));

    if ($enable) {
        db_log("renderers", 'Enabled renderer ' . $input['name'], $_SESSION['user_login']);
    } else {
        db_log("renderers", 'Disabled renderer ' . $input['name'], $_SESSION['user_login']);
    }
    push_renderers_to_ezmanager();
    //   notify_changes();
}

function create_renderer() {
    global $input;
    global $ssh_public_key;
    global $apache_username;
    global $ssh_pub_key_location;
    global $ssh_timeout;
    global $basedir;
    global $renderers_options;

    $ssh_timeout = 30;

    if (!isset($input['renderer_step']))
        $input['renderer_step'] = "default";

    switch ($input['renderer_step']) {


        // 1. Receive information about renderer (server address, username and name)
        case "1" :

            $renderer_name = $input['renderer_name'];
            $renderer_address = $input['renderer_address'];
            $renderer_user = $input['renderer_user'];
            $enabled = $input['enabled'] ? 1 : 0;

            if (empty($renderer_name)) {
                $error = template_get_message('missing_renderer_name', get_lang());
            } else if (renderer_exists($renderer_name) !== false) {
                $error = template_get_message('existing_renderer_name', get_lang());
            } else if (empty($renderer_address)) {
                $error = template_get_message('missing_renderer_address', get_lang());
            } else if (empty($renderer_user)) {
                $error = template_get_message('missing_renderer_user', get_lang());
            } else {

                // saves infos as session var
                $_SESSION['renderer_name'] = $renderer_name;
                $_SESSION['renderer_user'] = $renderer_user;
                $_SESSION['renderer_address'] = $renderer_address;
                $_SESSION['renderer_enabled'] = $enabled;

                // retrieves the ssh key to display
                if ($ssh_pub_key_location == "")
                    $ssh_pub_key_location = "~$apache_username/.ssh/*.pub";
                $ssh_public_key = exec("cat $ssh_pub_key_location");

                // ssh public key not found
                if ($ssh_public_key == "")
                    $ssh_public_key = "Key not found ! &#10;&#10;You can specify the path to Apache user's SSH public key by assigning a value to \$ssh_pub_key_location in commons/config.inc.";

                // saves ssh key as session var if the user returns to the previous step afterward
                $_SESSION['renderer_ssh_key'] = $ssh_public_key;

                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step2.php');
                include template_getpath('div_main_footer.php');

                die;
            }

            include template_getpath('div_main_header.php');
            include template_getpath('div_create_renderer_step1.php');
            include template_getpath('div_main_footer.php');

            die;
            break;



        // 2. Ask user to copy SSH key on remote renderer and test connection
        case "2" :

            if ($input['submit_step_2_prev']) {
                // back to step 1
                $input['renderer_name'] = $_SESSION['renderer_name'];
                $input['renderer_user'] = $_SESSION['renderer_user'];
                $input['renderer_address'] = $_SESSION['renderer_address'];

                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step1.php');
                include template_getpath('div_main_footer.php');
            } else {
                $ssh_public_key = $_SESSION['renderer_ssh_key'];

                // test the SSH connection  
                $res = ssh_connection_test($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout);

                if ($res === true) {
                    // SSH connection is correctly set. Ask for more information on remote renderer
                    // set default values
                    $input['renderer_php'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"which php\"");
                    $input['renderer_php'] = ($input['renderer_php'] == "") ? "PHP binary not found !" : $input['renderer_php'];
                    $input['renderer_root_path'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"echo ~" . $_SESSION['renderer_user'] . "\"/ezrenderer");
                    $input['renderer_ffmpeg'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"echo `which ffmpeg`\"");
                    $input['renderer_ffmpeg'] = ($input['renderer_ffmpeg'] == "") ? "FFMPEG binary not found !" : $input['renderer_ffmpeg'];
                    $input['renderer_ffprobe'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"echo `which ffprobe`\"");
                    $input['renderer_ffprobe'] = ($input['renderer_ffprobe'] == "") ? "FFPROBE binary not found !" : $input['renderer_ffprobe'];
                    $input['renderer_num_threads'] = 4;
                    $input['renderer_num_jobs'] = 4;

                    include template_getpath('div_main_header.php');
                    include template_getpath('div_create_renderer_step3.php');
                    include template_getpath('div_main_footer.php');
                    die;
                } else if ($res === false) {
                    // SSH connection has failed. Back to step 2
                    $error = template_get_message('ssh_connection_failed', get_lang());
                } else if ($res == "known_hosts_error") {
                    // could not add SSH public key from remote renderer to known_hosts file
                    $error = template_get_message('ssh_known_hosts_error', get_lang());
                } else if ($res == "keyscan_error") {
                    // SSH public key from remote renderer is not in known_hosts yet
                    $error = template_get_message('ssh_keyscan_error', get_lang());
                }
                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step2.php');
                include template_getpath('div_main_footer.php');
                die;
            }
            break;



        // 3. Additional information for EZrenderer installation
        case "3" :
            if ($input['submit_step_3_prev']) {
                // back to step 2
                $ssh_public_key = $_SESSION['renderer_ssh_key'];
                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step2.php');
                include template_getpath('div_main_footer.php');
                die;
            } else {
                // Go to next step
                $renderer_root_path = $input['renderer_root_path'];
                $renderer_option = $input['renderer_options'];
                $renderer_php = $input['renderer_php'];
                $renderer_ffmpeg = $input['renderer_ffmpeg'];
                $renderer_ffprobe = $input['renderer_ffprobe'];
                $renderer_num_jobs = $input['renderer_num_jobs'];
                $renderer_num_threads = $input['renderer_num_threads'];

                $error = "";

                if (empty($renderer_root_path)) {
                    $error = template_get_message('missing_renderer_root_path', get_lang());
                } else if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"if [ -e " . dirname($renderer_root_path) . " ]; then echo 'exists'; fi;\"") != 'exists') {
                    $error = template_get_message('bad_renderer_root_path', get_lang());
                } else if (empty($renderer_php) || $renderer_php == "PHP binary not found !") {
                    $error = template_get_message('missing_renderer_php', get_lang());
                } else if (empty($renderer_ffmpeg)) {
                    $error = template_get_message('missing_renderer_ffmpeg', get_lang());
                } else if (empty($renderer_ffprobe) || $renderer_ffprobe == "FFPROBE binary not found !") {
                    $error = template_get_message('missing_renderer_ffprobe', get_lang());
                }

                if ($error != "") {
                    include template_getpath('div_main_header.php');
                    include template_getpath('div_create_renderer_step3.php');
                    include template_getpath('div_main_footer.php');
                    die;
                }

                if ($input['submit_step_3_next']) {
                    // tests PHP, FFMPEG and FFPROBE

                    $error = "";

                    // verification for PHP
                    $res = test_php_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_php);
                    switch ($res) {
                        case "php_not_found" :
                            $error .= "- " . template_get_message('php_not_found', get_lang()) . "<br/>";
                            break;
                        case "php_deprecated" :
                            $error .= "- " . template_get_message('php_deprecated', get_lang()) . "<br/>";
                            break;
                        case "php_missing_xml":
                            $error .= "- " . template_get_message('missing_module_xml', get_lang()) . "<br/>";
                            break;
                        case "php_missing_gd" :
                            $error .= "- " . template_get_message('missing_module_gd', get_lang()) . "<br/>";
                            break;
                        case "gd_missing_freetype" :
                            $error .= "- " . template_get_message('missing_freetype', get_lang()) . "<br/>";
                            break;
                        default: $error .= "";
                    }

                    // verification for FFMPEG
                    if ($renderer_option == 'ffmpeg' || $renderer_option == 'ffmpeg_exp') {
                        $res = test_ffmpeg_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_ffmpeg, $renderer_option == 'ffmpeg_exp');
                        switch ($res) {
                            case "ffmpeg_not_found" :
                                $error .= "- " . template_get_message('ffmpeg_not_found', get_lang()) . "<br/>";
                                break;
                            case "missing_codec_aac" :
                                $error .= "- " . template_get_message('missing_codec_aac', get_lang()) . "<br/>";
                                $display_ffmpeg_exp = true; // used in div_create_renderer_step3.php
                                break;
                            case "missing_codec_h264":
                                $error .= "- " . template_get_message('missing_codec_h264', get_lang()) . "<br/>";
                                break;
                            default: $error .= "";
                        }
                    }

                    // verification for FFPROBE
                    $res = test_ffprobe_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_ffprobe);
                    if ($res == "ffprobe_not_found") {
                        $error .= "- " . template_get_message('ffprobe_not_found', get_lang()) . "<br/>";
                    }

                    if ($error != "") {
                        include template_getpath('div_main_header.php');
                        include template_getpath('div_create_renderer_step3.php');
                        include template_getpath('div_main_footer.php');
                        die;
                    } else {
                        $tests_success = true;
                    }
                }

                // Disables the remote renderer if tests have been skipped
                if (!$tests_success) {
                    $_SESSION['renderer_enabled'] = false;
                }
                $_SESSION['renderer_root_path'] = $renderer_root_path;
                $_SESSION['renderer_php'] = $renderer_php;
                $_SESSION['renderer_option'] = $renderers_options[$renderer_option];
                $_SESSION['renderer_ffmpeg'] = $renderer_ffmpeg;
                $_SESSION['renderer_ffprobe'] = $renderer_ffprobe;
                $_SESSION['renderer_num_jobs'] = (empty($renderer_num_jobs) || is_nan($renderer_num_jobs)) ? 4 : $renderer_num_jobs;
                $_SESSION['renderer_num_threads'] = (empty($renderer_num_threads) || is_nan($renderer_num_threads)) ? 4 : $renderer_num_threads;


                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step4.php');
                include template_getpath('div_main_footer.php');

                die;
            }
            break;



        // 4. Copies installation files, install EZrenderer and update renderers.inc
        case "4" :

            $input['renderer_root_path'] = $_SESSION['renderer_root_path'];
            $input['renderer_php'] = $_SESSION['renderer_php'];
            $input['renderer_ffmpeg'] = $_SESSION['renderer_ffmpeg'];
            $input['renderer_ffprobe'] = $_SESSION['renderer_ffprobe'];
            $input['renderer_num_jobs'] = $_SESSION['renderer_num_jobs'];
            $input['renderer_num_threads'] = $_SESSION['renderer_num_threads'];
            $input['renderer_options'] = $_SESSION['renderer_option']['name'];
            if ($input['renderer_options'] == 'ffmpeg_exp')
                $display_ffmpeg_exp = true;

            if ($input['submit_step_4_prev']) {
                // back to step 3

                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step3.php');
                include template_getpath('div_main_footer.php');
                die;
            } else {
                if ($input['installation_step'] == 1) {
                    // 4.1. Copies EZrenderer installation files on the remote renderer
                    // tests if ezrenderer is already installed
                    if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"if [ -e " . $_SESSION['renderer_root_path'] . " ]; then echo 'exists'; fi;\"") == 'exists') {
                        // EZrenderer root path already exists on the remote renderer 
                        $response['error'] = true;
                        $response['msg'] = "<div class='red'>" . template_get_message('root_already_exists', get_lang()) . "</div>";
                        echo json_encode($response);
                        die;
                    }
                    unset($output);
                    // actually copies files
                    exec("scp -r -o ConnectTimeout=600 $basedir/ezrenderer " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . ":" . $_SESSION['renderer_root_path'], $output, $returncode);
                    if ($returncode) {
                        // an error occured while copying installation files
                        $response['error'] = true;
                        $response['msg'] = "<div class='red'>" . template_get_message('renderer_copy_failed', get_lang()) . "</div>";
                        echo json_encode($response);
                        die;
                    }
                    // EZrenderer has been remotely copied
                    echo json_encode("<div class='green'>" . template_get_message('load_step_4_copy_success', get_lang()) . "</div>"
                            . template_get_message('load_step_4_install', get_lang()));
                    die;
                }

                if ($input['installation_step'] == 2) {
                    // 4.2. Installs ezrenderer on the remote renderer
                    exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " .
                            $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] .
                            " \"" . $_SESSION['renderer_php'] . " " . $_SESSION['renderer_root_path'] . "/renderer_install.php " .
                            $_SESSION['renderer_php'] . " '" .
                            str_replace('"', '\"', serialize($_SESSION['renderer_option'])) . "' " .
                            $_SESSION['renderer_ffmpeg'] . " " .
                            $_SESSION['renderer_ffprobe'] . " " .
                            $_SESSION['renderer_num_threads'] . " " .
                            $_SESSION['renderer_num_jobs'] . "\"", $output, $returncode);

                    if ($returncode || strpos($output[0], "renderer installed") === false) {
                        // an error occured while installing EZrenderer
                        $response['error'] = true;
                        $response['msg'] = "<div class='red'>" . template_get_message('renderer_install_failed', get_lang()) . "</div>";
                        echo json_encode($response);
                        die;
                    }
                    // EZrenderer has been installed
                    echo json_encode("<div class='green'>" . template_get_message('load_step_4_install_success', get_lang()) . "</div>"
                            . template_get_message('load_step_4_update', get_lang()));
                    die;
                }

                if ($input['installation_step'] == 3) {
                    // Adds the renderer to renderers.inc files of EZmanager and EZadmin
                    if (!add_renderer_to_file($_SESSION['renderer_name'], $_SESSION['renderer_address'], $_SESSION['renderer_user'], $_SESSION['renderer_enabled'], $_SESSION['renderer_root_path'], $_SESSION['renderer_php'])) {
                        // an error occured while updating renderers.inc 
                        $response['error'] = true;
                        $response['msg'] = "<div class='red'>" . template_get_message('renderer_update_failed', get_lang()) . "</div>";
                        echo json_encode($response);
                        die;
                    } else {
                        // renderers.inc files have been updated
                        if (push_renderers_to_ezmanager()) {
                            echo json_encode("<div class='green'>" . template_get_message('load_step_4_update_success', get_lang()) . "</div>");
                            die;
                        } else {
                            echo json_encode("<div class='red'>" . template_get_message('load_step_4_update_failed', get_lang()) . "</div>");
                            die;
                        }
                    }
                }
            }
            break;



        // 5. Display the renderers list.  
        case "5" :
            view_renderers();
            break;



        // Display the renderer creation form
        default:
            unset($_SESSION['renderer_root_path']);
            unset($_SESSION['renderer_user']);
            unset($_SESSION['renderer_address']);
            unset($_SESSION['renderer_name']);
            unset($_SESSION['renderer_enabled']);
            unset($_SESSION['renderer_num_jobs']);
            unset($_SESSION['renderer_num_threads']);
            unset($_SESSION['renderer_php']);
            unset($_SESSION['renderer_ffmpeg']);
            unset($_SESSION['renderer_ffprobe']);

            include template_getpath('div_main_header.php');
            include template_getpath('div_create_renderer_step1.php');
            include template_getpath('div_main_footer.php');
    }


    db_log('renderers', 'Created renderer ' . $_SESSION['renderer_name'], $_SESSION['user_login']);
    //   notify_changes();
}

function remove_renderer() {
    global $input;

    if (!renderer_delete(trim($input['name']))) {
        echo json_encode(array('error' => '1'));
    } else {
        echo json_encode(array('success' => '1'));
        db_log("renderers", 'Deleted renderer ' . $input['name'], $_SESSION['user_login']);
        push_renderers_to_ezmanager();
        //    notify_changes();
    }
}

/**
 * Displays job queue
 */
function view_queue() {
    //$jobs = scheduler_queue_get();
    $jobs = array_merge(scheduler_processing_get(), scheduler_queue_get(), scheduler_frozen_get());
    /*
     *  <uid></uid>
     *  <id></id>
     *  <file></file>
     *  <origin></origin>
     *  <sender></sender>
     *  <priority></priority>
     *  <renderer></renderer>
     *  <created></created>
     *  <sent></sent>
     *  <done></done>
     */

    require_once template_getpath('div_main_header.php');
    //require_once template_getpath('div_search_job.php');
    require_once template_getpath('div_list_jobs.php');
    require_once template_getpath('div_main_footer.php');
}

function freeze_unfreeze_job() {
    global $input;

    $infos = scheduler_job_info_get($input['job']);
    if ($infos['status'] == 'frozen') {
        scheduler_unfreeze($input['job']);
    } else if ($infos !== null) { // In queue = not frozen
        scheduler_freeze($input['job']);
    }

    view_queue();
}

function job_priority_up() {
    global $input;

    //if(in_array($input['job'], scheduler_queue_get())) { // In queue = not frozen
    scheduler_job_priority_up($input['job']);
    //}

    view_queue();
}

function job_priority_down() {
    global $input;

    //if(in_array($input['job'], scheduler_queue_get())) { // In queue = not frozen
    scheduler_job_priority_down($input['job']);
    //}

    view_queue();
}

function job_kill() {
    global $input;

    //if(in_array($input['job'], scheduler_queue_get())) { // In queue = not frozen
    scheduler_job_kill($input['job']);
    //}

    view_queue();
}

/**
 * Reloads the whole page
 */
function refresh_page() {
    global $ezadmin_url;
    // reload the page
    echo '<script>window.location.reload();</script>';
    die;
}

//
// "Business logic" functions
//

/**
 * Effectively logs the user in
 * @param string $login
 * @param string $passwd
 */
function user_login($login, $passwd) {
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
        $error = "User not authorized";
        view_login_form();
        die;
    }
    include 'admin.inc'; //file containing an assoc array of admin users
    if (!isset($users[$login_parts[0]])) {
        $error = "User not authorized";
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

/**
 * Logs the user out, i.e. destroys all the data stored about them
 */
function user_logout() {
    global $ezadmin_url;

    // 2) Unsetting session vars
    unset($_SESSION['podcastcours_mode']);
    unset($_SESSION['user_login']);     // User netID
    unset($_SESSION['podcastcours_logged']); // "boolean" stating that we're logged
    session_destroy();
    // 3) Displaying the logout message

    include_once template_getpath('logout.php');
    //include_once "tmpl/fr/logout.php";

    $url = $ezadmin_url;

    unset($_SESSION['lang']);
}

/**
 * Changes have been made but not saved yet: we display an alert
 */
function notify_changes() {
    $_SESSION['changes_to_push'] = true;
}

/**
 * Changes have been saved: we remove the alert.
 */
function remove_changes_alert() {
    unset($_SESSION['changes_to_push']);
}

function sync_externals() {
    
}

?>
