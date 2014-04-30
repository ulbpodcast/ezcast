<?php

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

/**
 * This file is aimed to install EZcast and its components.
 * It creates the tables of the database and sets up the configuration files 
 * according to the user's preferences
 */

require_once '../commons/lib_template.php';
require_once '../commons/lib_database.php';
require_once 'lib_various.php';
require_once 'lib_error.php';

if (file_exists('config.inc'))
    die;

session_name("ezcast_installer");
session_start();

$_SESSION['install'] = true;
$errors = array();
$input = array_merge($_GET, $_POST);

$template_folder = 'tmpl/';
template_repository_path($template_folder . get_lang());
template_load_dictionnary('translations.xml');

if (!isset($_SESSION['user_logged'])) {
    if (isset($input['action']) && $input['action'] == 'login') {
        if (!isset($input['login']) || !isset($input['passwd'])) {
            error_print_message(template_get_message('empty_username_password', get_lang()));
            die;
        }
        $login = $input['login'];
        $passwd = $input['passwd'];

        // 0) Sanity checks
        if (empty($login) || empty($passwd)) {
            $error = template_get_message('empty_username_password', get_lang());
            view_login_form();
            die;
        }

        $user_passwd = file_get_contents("../first_user");
        $user_passwd = explode(" , ", $user_passwd);
        $salt = substr($user_passwd[1], 0, 2);
        $cpasswd = crypt($passwd, $salt);
        $user_passwd[1] = rtrim($user_passwd[1]);
        $res = ($login == $user_passwd[0] && $user_passwd[1] == $cpasswd);

        if (!$res) {
            $error = "Authentication failed";
            view_login_form();
            die;
        }

        $_SESSION['user_login'] = $login;
        $_SESSION['user_logged'] = true;
    } else {
        view_login_form();
        die;
    }
}

if (isset($input['install']) && !empty($input['install'])) {

    // Test connection to DB
    $res = db_ping($input['db_type'], $input['db_host'], $input['db_login'], $input['db_passwd'], $input['db_name']);
    if (!$res) {
        $errors['db_error'] = 'Could not connect to database ' . $input['db_host'];
    }

    if (count($errors) > 0)
        require template_getpath('install.php');

    // Create tables in DB
    try {
        $db = new PDO($input['db_type'] . ':host=' . $input['db_host'] . ';dbname=' . $input['db_name'], $input['db_login'], $input['db_passwd']);
        $db->beginTransaction();

        $db->exec('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');
        $db->exec('SET time_zone = "+00:00"');

        $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'classrooms`');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'classrooms` (' .
                '`room_ID` varchar(20) NOT NULL COMMENT \'Room nr (e.g. at ULB: R42-5-503)\',' .
                '`name` varchar(255) DEFAULT NULL COMMENT \'Room name (e.g. "Auditoire K")\',' .
                '`IP` varchar(100) NOT NULL COMMENT \'IP to recorder in classroom\',' .
                '`enabled` tinyint(1) NOT NULL,' .
                'PRIMARY KEY (`room_ID`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'courses`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'courses` (' .
                '`course_code` varchar(50) NOT NULL COMMENT \'At ULB: mnémonique\',' .
                '`course_name` varchar(255) DEFAULT NULL,' .
                '`shortname` varchar(100) DEFAULT NULL COMMENT \'Optional, shorter name displayed in recorders\',' .
                '`in_recorders` tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'Set to FALSE to disable classroom recording\',' .
                '`has_albums` int(11) NOT NULL DEFAULT \'0\' COMMENT \'Number of assets in the album (or 0/1 value for now)\',' .
                '`date_created` date NOT NULL,' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'"external" or "internal"\',' .
                'PRIMARY KEY (`course_code`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'logs`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'logs` (   ' .
                '`ID` int(11) NOT NULL AUTO_INCREMENT,' .
                '`time` datetime NOT NULL,' .
                '`table` varchar(100) NOT NULL,' .
                '`message` varchar(255) DEFAULT NULL,' .
                '`author` varchar(20) NOT NULL,' .
                'PRIMARY KEY (`ID`)' .
                ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;');

        $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'users`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'users` (' .
                '`user_ID` varchar(50) NOT NULL COMMENT \'For ULB: netid\',' .
                '`surname` varchar(255) DEFAULT NULL,' .
                '`forename` varchar(255) DEFAULT NULL,' .
                '`passwd` varchar(255) NOT NULL,' .
                '`recorder_passwd` varchar(255) DEFAULT NULL COMMENT \'Password as saved in the recorders, if different from global passwd\',' .
                '`permissions` int(11) NOT NULL DEFAULT \'0\' COMMENT \'1 for admin, 0 for non-admin\',' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'"external" or "internal"\',' .
                'PRIMARY KEY (`user_ID`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'users_courses`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'users_courses` (' .
                '`ID` int(11) NOT NULL AUTO_INCREMENT,' .
                '`course_code` varchar(50) NOT NULL COMMENT \'Course code as referenced in ezcast_courses\',' .
                '`user_ID` varchar(50) NOT NULL COMMENT \'user ID as referred in ezcast_users\',' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'Either "external" or "internal"\',' .
                'PRIMARY KEY (`ID`)' .
                ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=\'Joint of Courses and Users\' AUTO_INCREMENT=45013 ;');
        $db->commit();
    } catch (PDOException $e) {
        $errors['db_error'] = $e->getMessage();
        require template_getpath('install.php');
    }

    // Write config file
    edit_config_file(
            $input['php_cli_cmd'], $input['rsync_pgm'], $input['application_url'], $input['repository_basedir'], $input['organization_name'], $input['copyright'], $input['mailto_alert'], $input['ezcast_basedir'], $input['db_type'], $input['db_host'], $input['db_login'], $input['db_passwd'], $input['db_name'], $input['db_prefix'], $input['recorder_user'], $input['recorder_basedir'], $input['ezmanager_host'], $input['ezmanager_user'], !empty($input['classrooms_category_enabled']) ? true : false, !empty($input['add_users_enabled']) ? true : false, !empty($input['recorder_password_storage_enabled']) ? true : false, !empty($input['use_course_name']) ? true : false, !empty($input['use_user_name']) ? true : false, !empty($input['https_ready']) ? true : false
    );

    // Add the first user in database 
    $first_user = file_get_contents("../first_user");
    $first_user = explode(" , ", $first_user);

    $user_ID = $first_user[0];
    $surname = $first_user[3];
    $forename = $first_user[2];
    $passwd = $first_user[1];
    $permissions = 1;

    include 'config.inc'; 
    if (!db_ready())
        db_prepare();
    db_user_create($user_ID, $surname, $forename, $passwd, $permissions);
    add_admin_to_file($user_ID);
    push_users_to_ezmanager();
    db_log(db_gettable('users'), 'Created user ' . $user_ID, $_SESSION['user_login']);
    db_close();

    /* require template_getpath('div_header.php');
      echo '<div class="alert alert-success">Install successful. For improved security, we advise you to delete or rename the "install.php" file.</div>';
      require template_getpath('div_footer.php'); */

    session_destroy();
    unlink("../first_user");

    echo "Installation has been completed !\n" .
    "Please install EZrenderer and configure it before using EZcast.";
} else {
    if (file_exists("../commons/config.inc")) {
        include_once '../commons/config.inc';
    } else {
        include_once '../commons/config-sample.inc';
    }

    $input['php_cli_cmd'] = $php_cli_cmd;
    $input['rsync_pgm'] = $rsync_pgm;
    $input['https_ready'] = $https_ready;
    $input['application_url'] = $application_url;
    $input['organization_name'] = $organization_name;
    $input['copyright'] = $copyright;
    $input['mailto_alert'] = $mailto_alert;
    $input['repository_basedir'] = $repository_basedir;
    $input['ezcast_basedir'] = $basedir;
    $input['db_type'] = $db_type;
    $input['db_host'] = $db_host;
    $input['db_login'] = $db_login;
    $input['db_passwd'] = $db_passwd;
    $input['db_name'] = $db_name;
    $input['db_prefix'] = $db_prefix;
    $input['recorder_user'] = $recorder_user;
    $input['recorder_basedir'] = $recorder_basedir;

    include_once './config-sample.inc';

    $input['ezmanager_host'] = $ezmanager_host;
    $input['ezmanager_user'] = $ezmanager_user;

    $input['classrooms_category_enabled'] = $classrooms_category_enabled;
    $input['add_users_enabled'] = $add_users_enabled;
    $input['recorder_password_storage_enabled'] = $recorder_password_storage_enabled;
    $input['use_course_name'] = $use_course_name;
    $input['use_user_name'] = $use_user_name;

    require template_getpath('install.php');
}

function view_login_form() {
    global $installer_mode;
    global $error, $input;

    // template include goes here
    include_once template_getpath('login.php');
}

?>
