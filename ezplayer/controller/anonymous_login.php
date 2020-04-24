<?php

/**
 * logs in an anonymous user
 * The previously anonymous user now authenticates
 * @global array $input
 * @global type $template_folder
 * @global type $login_error
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $ezplayer_url
 */
function index($param = array())
{
    global $input;
    global $login_error;
    global $repository_path;
    global $user_files_path;
    global $ezplayer_url;

    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $login_error = '';
    $login = $input['login'];
    $passwd = $input['passwd'];
    unset($input['login']);
    unset($input['passwd']);
    $input['action'] = $_SESSION['ezplayer_mode'];
    $album_tokens = $_SESSION['acl_album_tokens'];
    unset($input['click']);

    // 0) Sanity checks
    if (!isset($login) || !isset($passwd) || empty($login) || empty($passwd)) {
        $login_error = template_get_message('empty_username_password', get_lang());
        load_page();
        die;
    }

    // checks if runas
    if (count($login_parts) == 2) {
        if (!file_exists('admin.inc')) {
            $error = "Not admin. runas login failed";
            view_login_form();
            die;
        }
        include 'admin.inc'; //file containing an assoc array of admin users
        if (!isset($admin[$login_parts[0]])) {
            $error = "Not admin. runas login failed";
            view_login_form();
            die;
        }
        $_SESSION['user_is_admin'] = true;
        $_SESSION['user_runas'] = true;
    } else {
        if (file_exists('admin.inc')) {
            include 'admin.inc'; //file containing an assoc array of admin users
            if (isset($admin[$login])) {
                $_SESSION['user_is_admin'] = true;
            }
        }
    }

    $res = checkauth($login, $passwd);
    if (!$res) {
        $login_error = checkauth_last_error();
        load_page();
        die;
    }
    // 1) Initializing session vars
    $_SESSION['ezplayer_logged'] = "user_logged"; // "boolean" stating that we're logged
    unset($_SESSION['ezplayer_anonymous']); // "boolean" stating that we're logged
    $_SESSION['user_login'] = $res['login'];
    $_SESSION['user_real_login'] = $res['real_login'];
    $_SESSION['user_full_name'] = $res['full_name'];
    $_SESSION['user_email'] = $res['email'];
    $_SESSION['admin_enabled'] = false;

    // 2) Initializing the ACLs
    acl_init($login);

    // 3) adds album tokens that have been consulted as anonymous
    if (isset($album_tokens)) {
        if (user_prefs_tokens_add($_SESSION['user_login'], $album_tokens) !== false) {
            ezplayer_acl_update_permissions_list();
        }
    }

    // 4) Logging the login operation
    log_append("anonymous user logged in");
    // lvl, action, browser_name, browser_version, user_os, browser_full_info
    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl, "login_from_anonymous", $_SESSION['browser_name'], $_SESSION['browser_version'],
        $_SESSION['user_os'], $_SESSION['browser_full'], session_id()));

    if (count($input) > 0) {
        $ezplayer_url .= '/index.php?';
    }
    foreach ($input as $key => $value) {
        $ezplayer_url .= "$key=$value&";
    }
    // 5) Displays the previous page
    header("Location: " . $ezplayer_url);
    load_page();
}
