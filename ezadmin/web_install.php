<?php

/**
 * @package ezcast.ezadmin.installer
 */
/**
 * This file is aimed to install EZcast and its components.
 * It creates the tables of the database and sets up the configuration files 
 * according to the user's preferences
 */
$in_install = true;

if (file_exists('config.inc')) {
    echo "Config file already exists, nothing to do here ;-)";
    die;
}

require_once __DIR__ . '/../commons/lib_template.php';
require_once __DIR__ . '/../commons/lib_sql_management.php';
require_once __DIR__ . '/lib_various.php';
require_once __DIR__ . '/lib_error.php';
require_once __DIR__ . '/../commons/common.inc'; //for logger access

session_name("ezcast_installer");
session_start();

if(isset($_GET['lang'])) {
    set_lang($_GET['lang']);
}

$template_folder = __DIR__. '/tmpl/' . get_lang();
date_default_timezone_set("Europe/Brussels"); //TODO: allow to change this
template_repository_path($template_folder);
if(template_repository_path() == "") {
    trigger_error("Install could not get repository path at path: $template_folder . "
            . "This folder is generated by the tmpl_install.sh script and should be readable.", E_USER_ERROR);
    die;
}
template_load_dictionnary('translations.xml');

$_SESSION['install'] = true;
$errors = array();
$input = array_merge($_GET, $_POST);

//if user is not loged, show loggin form and exit
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
  
//if 'install' input is set, a form was submitted
if (isset($input['install']) && !empty($input['install'])) {
    // saves organization logo if it exists
    save_logo();
    // installation form has been submitted and we verify the db to create the tables
    validate_form();
    create_config_files();
    add_first_user();
// submitted form but database already exists
} else if (isset($input['db_choice_submit']) && !empty($input['db_choice_submit'])) {
    // db already contains EZcast tables. The user has choosen whether he wants to
    // replace the existing tables, change the tables prefix or use the existing tables
    $db_choice = $input['db_choice'];
    $new_prefix = $input['new_prefix'];
    $input = $_SESSION['user_inputs'];

    switch ($db_choice) {
        case 'replace' :
            // user wants to replace the existing tables with new ones
            create_tables();
            break;
        case 'use' :
            // user wants to use the existing tables
            // noting to do
            break;
        default :
            // user wants to use another table prefix
            $input['db_prefix'] = $new_prefix;
            validate_form();
            break;
    }
    create_config_files();
    add_first_user();
} else {
    // display the installation form
    if (file_exists("../commons/config.inc")) {
        include_once '../commons/config.inc';
    } else {
        include_once '../commons/config-sample.inc';
    }

    if (!(isset($input['skip_ext']) && $input['skip_ext']) && !(isset($input['skip_srv']) && $input['skip_srv']))
        check_php_extensions();

    if (!(isset($input['skip_srv']) && $input['skip_srv']))
        check_server_config();


    //get configs from already existing config if any to fill the form
    $input['php_cli_cmd'] = $php_cli_cmd;
    $input['rsync_pgm'] = $rsync_pgm;
    $input['https_ready'] = $_SERVER['SERVER_PORT'] == '443' ? true : false;
    $input['application_url'] = "http://" . $_SERVER['SERVER_NAME'];
    $input['organization_name'] = $organization_name;
    $input['organization_url'] = $organization_url;
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

function check_php_extensions() {
    // extension name => php version
    //      POSITIVE: extension must be here if php have this version or grreter
    //      NEGATIVE: extension could miss if php have this version or greeter
    $php_extensions = array(
            'ldap' => '*', 
            'curl' => '*', 
            'PDO' => '*', 
            'pdo_mysql' => '*', 
            'mysql' => '-7', 
            'json' => '*'); 
            // , 'apc' => '*');

    $all_dependences = true;

    $display = "";
    foreach ($php_extensions as $extension => $phpVersion) {
        if (extension_loaded($extension) || ($phpVersion != '*' && 
                version_compare(PHP_VERSION, $phpVersion, ($phpVersion > 0) ? "<=" : ">="))) {
            $display .= "<div class=\"green\">$extension extension loaded ...</div>";
            $all_dependences = $all_dependences && true;
        } else {
            $display .= "<div class=\"red\">$extension extension NOT loaded ...</div>";
            $all_dependences = $all_dependences && false;
        }
    }

    if (!$all_dependences) {
        print "<!DOCTYPE html>
                <html><head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>Check PHP modules</title>
        <link href=\"css/style.css\" rel=\"stylesheet\">
     </head>
     <body>";
        template_display("div_header.php");
        print "<div id='login_form'>" . $display;
        print "</div><div style='width: 400px; margin: auto;'><br/>Load the missing PHP extensions 
            for Apache, restart the web server and reconnect to this web installer.
         <br/><br/>If you want to continue anyway, click on the following button.
         <br/><br/><br/><a class='button' style='float: right;' href='install.php?skip_ext=true'>Continue</a></div>";
        template_display("div_footer.php");
        print "</body></html>";
        die;
    }
}

function check_server_config() {

    $upload_max_filesize = ini_get("upload_max_filesize");
    $post_max_size = ini_get("post_max_size");
    $max_execution_time = ini_get("max_execution_time");
    $max_input_time = ini_get("max_input_time");


    $all_good = true;

    $display = "";
    if (convert_size($upload_max_filesize) < 2000000000) {
        $display .= "<div style='line-height: 14px;'><span class=\"red\">upload_max_filesize = $upload_max_filesize</span> "
                . "<-- Determines the max size of the files that a user can upload in EZmanager. We recommend <b>2G</b><br/><br/></div>";
        $all_good = $all_good & false;
    } else {
        $display .= "<div class=\"green\">upload_max_filesize = $upload_max_filesize</div>";
        $all_good = $all_good & true;
    }

    if (convert_size($post_max_size) < 2000000000) {
        $display .= "<div style='line-height: 14px;'><span class=\"red\">post_max_size = $post_max_size</span> "
                . "<-- Determines the max size of post data allowed. This should be at least the value of 'upload_max_filesize'. "
                . "We recommend <b>2G</b><br/><br/></div>";
        $all_good = $all_good & false;
    } else {
        $display .= "<div class=\"green\">post_max_size = $post_max_size</div>";
        $all_good = $all_good & true;
    }

    if ((int) $max_execution_time < 300) {
        $display .= "<div style='line-height: 14px;'><span class=\"red\">max_execution_time = $max_execution_time</span> "
                . "<-- Determines the maximum time in seconds a script is allowed to run before it is terminated by the parser. "
                . "We recommend <b>300</b><br/><br/></div>";
        $all_good = $all_good & false;
    } else {
        $display .= "<div class=\"green\">max_execution_time = $max_execution_time </div>";
        $all_good = $all_good & true;
    }

    if ((int) $max_input_time < 300) {
        $display .= "<div style='line-height: 14px;'><span class=\"red\">max_input_time = $max_input_time</span> "
                . "<-- Determines the maximum time in seconds a script is allowed to parse input data, like POST and GET. "
                . "We recommend <b>300</b><br/><br/></div>";
        $all_good = $all_good & false;
    } else {
        $display .= "<div class=\"green\">max_input_time = $max_input_time </div>";
        $all_good = $all_good & true;
    }

    if (!$all_good) {
        print "<!DOCTYPE html>
                <html><head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>Check PHP modules</title>
        <link href=\"css/style.css\" rel=\"stylesheet\">
     </head>
     <body>";
        template_display("div_header.php");
        print "<div id='login_form'>" . $display;
        print "</div><div style='width: 400px; margin: auto;'><br/>Edit the '<b>" . php_ini_loaded_file() . "</b>' file to match your own needs.
         <br/><br/>If you want to continue anyway, click on the following button.
         <br/><br/><br/><a class='button' style='float: right;' href='install.php?skip_srv=true'>Continue</a></div>";
        template_display("div_footer.php");
        print "</body></html>";
        die;
    }
}

function convert_size($string) {

    switch (substr($string, -1)) {
        case 'G':
            $int = (int) substr_replace($string, "", -1);
            $int *= 1000000000;
            break;
        case 'M':
            $int = (int) substr_replace($string, "", -1);
            $int *= 1000000;
            break;
        case 'K':
            $int = (int) substr_replace($string, "", -1);
            $int *= 1000;
            break;
        default:
            if (is_numeric($string))
                $int = (int) $string;
            else
                $int = 0;
    }
    return $int;
}

function save_logo() {
    global $input;

    if (file_exists("../commons/config.inc")) {
        include_once '../commons/config.inc';
    } else {
        include_once '../commons/config-sample.inc';
    }

    $target_dir = "./htdocs/img/organization-logo.png";
    $errors = array();

    if (!isset($_FILES['organization_logo']) || $_FILES['organization_logo']['name'] == "") {
        return true;
    }

    if ($_FILES['organization_logo']['error'] > 0) {
        $errors['file_error'] = "Error while uploading logo file";
        require template_getpath('install.php');
        die;
    }

    if ($_FILES['organization_logo']['size'] > 1048576) {
        $errors['file_error'] = "Logo file bigger than 1Mo";
    }

    $filename_info = file_get_extension(basename($_FILES['organization_logo']['name']));
    if (strtolower($filename_info['ext']) != 'png') {
        $errors['file_error'] = "Bad extension for logo file (expected 'png' found '" . $filename_info['ext'] . "')";
    }

    if (count($errors) > 0) {
        require template_getpath('install.php');
        die;
    }

    $res = move_uploaded_file($_FILES['organization_logo']['tmp_name'], $target_dir);
    if (!$res) {
        $errors['file_error'] = "Error while saving logo file";
        require template_getpath('install.php');
        die;
    }

    copy($target_dir, "../ezmanager/htdocs/images/Header/organization-logo.png");
    copy($target_dir, "../ezplayer/htdocs/images/Header/organization-logo.png");
    copy($target_dir, $apache_documentroot . "/ezmanager/images/Header/organization-logo.png");
    copy($target_dir, $apache_documentroot . "/ezplayer/images/Header/organization-logo.png");
    copy($target_dir, $apache_documentroot . "/ezadmin/img/organization-logo.png");

    return true;
}

function file_get_extension($filename) {
    //search last dot in filename
    $pos_dot = strrpos($filename, '.');
    if ($pos_dot === false)
        return array('name' => $filename, 'ext' => "");

    $ext_part = substr($filename, $pos_dot + 1);
    $name_part = substr($filename, 0, $pos_dot);
    $result_assoc['name'] = $name_part;
    $result_assoc['ext'] = $ext_part;
    return $result_assoc;
}

function validate_form() {
    global $input;

    // Test connection to DB
    $errors = array();
    $res = db_ping($input['db_type'], $input['db_host'], $input['db_login'], $input['db_passwd'], $input['db_name']);
    if (!$res) {
        $errors['db_error'] = 'Could not connect to '. $input['db_type'] .' database ' . $input['db_host'] . ' ' .
                        'with user: '.$input['db_login'];
    }

    if (count($errors) > 0) {
        require template_getpath('install.php');
        die;
    }

    try {
        // used for the verification of existing tables and columns 
        $tables = array(
            escapeshellarg($input['db_prefix'] . 'classrooms') => array(
                escapeshellarg('room_id'), escapeshellarg('name'), 
                escapeshellarg('IP'), escapeshellarg('enabled')),
            
            escapeshellarg($input['db_prefix'] . 'courses') => array(
                escapeshellarg('course_code'), escapeshellarg('course_name'), 
                escapeshellarg('shortname'), escapeshellarg('in_recorders'), 
                escapeshellarg('has_albums'), escapeshellarg('date_created'), 
                escapeshellarg('origin')),
            
            escapeshellarg($input['db_prefix'] . 'logs') => array(
                escapeshellarg('ID'), escapeshellarg('time'), 
                escapeshellarg('table'), escapeshellarg('message'), escapeshellarg('author')),
            
            escapeshellarg($input['db_prefix'] . 'users') => array(
                escapeshellarg('user_ID'), escapeshellarg('surname'), 
                escapeshellarg('forename'), escapeshellarg('passwd'), 
                escapeshellarg('recorder_passwd'), escapeshellarg('permissions'), 
                escapeshellarg('origin')),
            
            escapeshellarg($input['db_prefix'] . 'users_courses') => array(
                escapeshellarg('ID'), escapeshellarg('course_code'), 
                escapeshellarg('user_ID'), escapeshellarg('origin')),
            
            escapeshellarg($input['db_prefix'] . 'threads') => array(
                escapeshellarg('id'), escapeshellarg('authorId'), 
                escapeshellarg('authorFullName'), escapeshellarg('title'), 
                escapeshellarg('message'), escapeshellarg('timecode'), 
                escapeshellarg('creationDate'), escapeshellarg('studentOnly'), 
                escapeshellarg('albumName'), escapeshellarg('assetName'), 
                escapeshellarg('assetTitle'), escapeshellarg('lastEditDate'), 
                escapeshellarg('lastEditAuthor'), escapeshellarg('nbComments'), escapeshellarg('deleted')),
            
            escapeshellarg($input['db_prefix'] . 'comments') => array(
                escapeshellarg('id'), escapeshellarg('authorId'), 
                escapeshellarg('authorFullName'), escapeshellarg('message'), 
                escapeshellarg('creationDate'), escapeshellarg('thread'), 
                escapeshellarg('parent'), escapeshellarg('nbChilds'), 
                escapeshellarg('lastEditDate'), escapeshellarg('approval'), 
                escapeshellarg('score'), escapeshellarg('upvoteScore'), 
                escapeshellarg('downvoteScore'), escapeshellarg('deleted')),
            
            escapeshellarg($input['db_prefix'] . 'votes') => array(
                escapeshellarg('login'), escapeshellarg('comment'), escapeshellarg('voteType')),
            
            escapeshellarg($input['db_prefix'] . 'messages') => array(
                escapeshellarg('id'), escapeshellarg('authorId'), 
                escapeshellarg('authorFullName'), escapeshellarg('message'), 
                escapeshellarg('timecode'), escapeshellarg('creationDate'), 
                escapeshellarg('albumName'), escapeshellarg('assetName')),
            
        );

        $db = new PDO($input['db_type'] . ':host=' . $input['db_host'] . ';dbname=' . $input['db_name'], $input['db_login'], $input['db_passwd']);

        // checks if tables already exist
        $data = $db->query(
                'SELECT table_name FROM information_schema.tables ' .
                'WHERE table_schema = ' . escapeshellarg($input['db_name']) . ' ' .
                'AND table_name IN (' . implode(', ', array_keys($tables)) . ')');
        $result = $data->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) <= 0) {
            // tables don't exist yet, we can create them
            create_tables();
        } else {
            // saves values from user
            $_SESSION['user_inputs'] = $input;
            // prepare radio buttons for next view
            $radio_buttons = array(
                'replace' => '<b>Replace</b> the existing tables. <b style="color:red">All contents of the existing tables will be erased.</b>',
                'prefix' => 'Choose another prefix for the tables of EZcast. This will create new tables for EZcast. <br/><input type="text" name="new_prefix"/>',
            );
            if (count($result) >= count(array_keys($tables))) {
                // all tables already exist
                $all_columns = true;
                foreach ($tables as $table => $columns) {
                    // checks if table contains all required columns
                    $data = $db->query(
                            'SELECT * FROM information_schema.columns ' .
                            'WHERE table_schema = ' . escapeshellarg($input['db_name']) . ' ' .
                            'AND table_name = ' . $table . ' ' .
                            'AND column_name IN (' . implode(', ', array_keys($columns)) . ')');
                    $result = $data->fetchAll(PDO::FETCH_ASSOC);
                    if (count($result) < count($columns)) {
                        $all_columns = false;
                        break;
                    }
                }
                if ($all_columns) {
                    $radio_buttons['use'] = 'Use the existing tables for EZcast. None table will be created.';
                }
            }

            require template_getpath('install_db_choice.php');
            die;
        }
    } catch (PDOException $e) {
        $errors['db_error'] = $e->getMessage();
        require template_getpath('install.php');
        die;
    }
}

function create_tables($drop = true) {
    global $input;

    // Create tables in DB
    try {
        $db = new PDO($input['db_type'] . ':host=' . $input['db_host'] . ';dbname=' . $input['db_name'], $input['db_login'], $input['db_passwd']);
        $db->beginTransaction();

        $db->exec('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');
        $db->exec('SET time_zone = "+00:00"');


        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'classrooms`');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'classrooms` (' .
                '`room_ID` varchar(20) NOT NULL COMMENT \'Room nr (e.g. at ULB: R42-5-503)\',' .
                '`name` varchar(255) DEFAULT NULL COMMENT \'Room name (e.g. "Auditoire K")\',' .
                '`IP` varchar(100) NOT NULL COMMENT \'IP to recorder in classroom\',' .
                '`IP_remote` varchar(100) DEFAULT NULL COMMENT \'IP to remote recorder in classroom\',' .
                '`enabled` tinyint(1) NOT NULL,' .
                'PRIMARY KEY (`room_ID`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'courses`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'courses` (' .
                '`course_code` varchar(50) NOT NULL COMMENT \'At ULB: mnémonique\',' .
                '`course_code_public` varchar(50) NOT NULL,' .
                '`course_name` varchar(255) DEFAULT NULL,' .
                '`shortname` varchar(100) DEFAULT NULL COMMENT \'Optional, shorter name displayed in recorders\',' .
                '`in_recorders` tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'Set to FALSE to disable classroom recording\',' .
                '`has_albums` int(11) NOT NULL DEFAULT \'0\' COMMENT \'Number of assets in the album (or 0/1 value for now)\',' .
                '`date_created` date NOT NULL,' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'"external" or "internal"\',' .
                'PRIMARY KEY (`course_code`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'admin_logs`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'admin_logs` (   ' .
                '`ID` int(11) NOT NULL AUTO_INCREMENT,' .
                '`time` datetime NOT NULL,' .
                '`table` varchar(100) NOT NULL,' .
                '`message` varchar(255) DEFAULT NULL,' .
                '`author` varchar(20) NOT NULL,' .
                'PRIMARY KEY (`ID`)' .
                ') ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'users`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'users` (' .
                '`user_ID` varchar(50) NOT NULL COMMENT \'For ULB: netid\',' .
                '`surname` varchar(255) DEFAULT NULL,' .
                '`forename` varchar(255) DEFAULT NULL,' .
                '`passwd` varchar(255) NOT NULL DEFAULT \'\',' .
                '`recorder_passwd` varchar(255) DEFAULT NULL COMMENT \'Password as saved in the recorders, if different from global passwd\',' .
                '`permissions` int(11) NOT NULL DEFAULT \'0\' COMMENT \'NYI. 1 for admin, 0 for non-admin\',' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'"external" or "internal"\',' .
                'PRIMARY KEY (`user_ID`)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'users_courses`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'users_courses` (' .
                '`ID` int(11) NOT NULL AUTO_INCREMENT,' .
                '`course_code` varchar(50) NOT NULL COMMENT \'Course code as referenced in ezcast_courses\',' .
                '`user_ID` varchar(50) NOT NULL COMMENT \'user ID as referred in ezcast_users\',' .
                '`origin` varchar(255) NOT NULL DEFAULT \'external\' COMMENT \'Either "external" or "internal"\',' .
                'PRIMARY KEY (`ID`)' .
                ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=\'Joint of Courses and Users\';');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'threads`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'threads` (' .
                '`id` bigint(20) NOT NULL AUTO_INCREMENT,' .
                '`authorId` varchar(50) NOT NULL COMMENT \'netid of the author of the discussion\',' .
                '`authorFullName` varchar(255) NOT NULL COMMENT \'full name of the author of the discussion\',' .
                '`title` varchar(140) NOT NULL,' .
                '`message` varchar(8192) NOT NULL,' .
                '`timecode` int(10) NOT NULL,' .
                '`creationDate` datetime NOT NULL,' .
                '`studentOnly` char NOT NULL,' .
                '`albumName` varchar(255) NOT NULL,' .
                '`assetName` varchar(255) NOT NULL,' .
                '`assetTitle` varchar(255) NOT NULL,' .
                '`lastEditDate` datetime NOT NULL,' .
                '`lastEditAuthor` varchar(255) NOT NULL,' .
                '`nbComments` int(5) DEFAULT "0",' .
                '`deleted` char DEFAULT "0",' .
                'PRIMARY KEY (`id`),' .
                'FULLTEXT (`title`, `message`)' .
                ') ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'comments`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'comments` (' .
                '`id` bigint(20) NOT NULL AUTO_INCREMENT,' .
                '`authorId` varchar(50) NOT NULL COMMENT \'netid of the author of the discussion\',' .
                '`authorFullName` varchar(255) NOT NULL COMMENT \'full name of the author of the discussion\',' .
                '`message` varchar(8192) NOT NULL,' .
                '`creationDate` datetime NOT NULL,' .
                '`thread` bigint(20) NOT NULL,' .
                '`parent` bigint(20),' .
                '`nbChilds` int(5) DEFAULT "0",' .
                '`lastEditDate` datetime NOT NULL,' .
                '`approval` char DEFAULT "0",' .
                '`score` int(5) DEFAULT "0",' .
                '`upvoteScore` int(5) DEFAULT "0",' .
                '`downvoteScore` int(5) DEFAULT "0",' .
                '`deleted` char DEFAULT "0",' .
                'PRIMARY KEY (`id`),' .
                'FOREIGN KEY (`thread`) REFERENCES ' . $input['db_prefix'] . 'threads(`id`),' .
                'FOREIGN KEY (`parent`) REFERENCES ' . $input['db_prefix'] . 'comments(`id`),' .
                'FULLTEXT (`message`)' .
                ') ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'votes`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'votes` (' .
                '`login` varchar(50) NOT NULL,' .
                '`comment` bigint(20) NOT NULL,' .
                '`voteType` tinyint(1) NOT NULL,' .
                'PRIMARY KEY (`login`,`comment`),' .
                'FOREIGN KEY (`comment`) REFERENCES ' . $input['db_prefix'] . 'comments(`id`)' .
                ') ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'messages`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'messages` (' .
                '`id` bigint(20) NOT NULL AUTO_INCREMENT,' .
                '`authorId` varchar(50) NOT NULL,' .
                '`authorFullName` varchar(255) NOT NULL,' .
                '`message` varchar(8192) NOT NULL,' .
                '`timecode` int(10) DEFAULT "0",' .
                '`creationDate` datetime NOT NULL,' .
                '`albumName` varchar(255) NOT NULL,' .
                '`assetName` varchar(255) NOT NULL,' .
                'PRIMARY KEY (`id`),' .
                'FULLTEXT (`message`)' .
                ') ENGINE=MyISAM  DEFAULT CHARSET=utf8;');
        
        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_TABLE_NAME .'`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_TABLE_NAME . '` (' .
                "`asset` varchar(50) NOT NULL,".
                "`origin` enum('ezmanager','ezadmin','ezrecorder','ezrenderer','other') NOT NULL,".
                "`classroom_id` varchar(50) DEFAULT NULL," .
                "`classroom_event_id` int(10) unsigned DEFAULT NULL,".
                "`event_time` datetime NOT NULL,".
                "`type_id` INT(10) UNSIGNED NOT NULL,".
                "`context` varchar(30) NOT NULL,".
                "`loglevel` tinyint(1) UNSIGNED NOT NULL COMMENT 'See logger.php for levels',".
                "`message` text NOT NULL,".
                "KEY `asset` (`asset`),".
                "KEY `event_time` (`event_time`)".
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_STATUS_TABLE_NAME .'`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_STATUS_TABLE_NAME . '` (' .
                "`asset` varchar(50) NOT NULL," .
                "`status` enum('auto_success', 'auto_success_errors', 'auto_success_warnings', 'auto_failure', 'auto_ignore', 'manual_ok', 'manual_partial_ok', 'manual_failure', 'manual_ignore') NOT NULL," .
                "`author` varchar(50) DEFAULT 'system'," .
                "`status_time` datetime DEFAULT NULL," .
                "`description` text," .
                "KEY `asset` (`asset`)" .
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_LAST_INDEXES_TABLE_NAME .'`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_LAST_INDEXES_TABLE_NAME . '` (' .
                "`source` varchar(20) NOT NULL," .
                "`id` int(10) unsigned NOT NULL," .
                " PRIMARY KEY (`source`) " .
                " ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_ASSET_PARENT_TABLE_NAME .'`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_ASSET_PARENT_TABLE_NAME . '` (' .
                "`asset` varchar(50) NOT NULL," .
                "`parent_asset` varchar(50) NOT NULL," .
                " UNIQUE KEY `asset` (`asset`) " .
                " ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
        if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_ASSET_INFO_TABLE_NAME .'`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . ServerLogger::EVENT_ASSET_INFO_TABLE_NAME . '` (' .
                "`asset` varchar(50) NOT NULL," .
                "`start_time` datetime NOT NULL," .
                "`end_time` datetime DEFAULT NULL," .
                "`classroom_id` varchar(50) NOT NULL," .
                "`course` varchar(50) NOT NULL," .
                "`author` varchar(50) NOT NULL," .
                "`cam_slide` enum('cam','slide','camslide') NOT NULL," .
                "PRIMARY KEY (`asset`)" .
                " ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
         if ($drop)
            $db->exec('DROP TABLE IF EXISTS `' . $input['db_prefix'] . 'streams`;');
        $db->exec('CREATE TABLE IF NOT EXISTS `' . $input['db_prefix'] . 'streams` (' .
            "`id` int(11) NOT NULL," .
            "`cours_id` varchar(50) NOT NULL," .
            "`asset` varchar(50) NOT NULL," .
            "`module_type` varchar(15) NOT NULL," .
            "`classroom` varchar(50) NOT NULL," .
            "`record_type` varchar(10) NOT NULL COMMENT 'cam/slide'," .
            "`netid` varchar(50) NOT NULL," .
            "`stream_name` varchar(255) NOT NULL," .
            "`token` varchar(50) NOT NULL," .
            "`ip` varchar(50) NOT NULL," .
            "`status` varchar(20) NOT NULL," .
            "`quality` varchar(10) NOT NULL," .
            "`protocol` varchar(10) NOT NULL," .
            "`server` varchar(50)," .
            "`port` int(5)" .
            " ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");	
        
        // Creation of the indexes
        $db->exec('CREATE INDEX `albumname_ndx` ' .
                'ON ' . $input['db_prefix'] . 'threads(`albumName`);');
        $db->exec('CREATE INDEX `assetname_ndx` ' .
                'ON ' . $input['db_prefix'] . 'threads(`assetName`);');
        $db->exec('CREATE INDEX `comment_thread_ndx` ' .
                'ON ' . $input['db_prefix'] . 'comments(`thread`);');
        $db->exec('CREATE INDEX `msg_albumname_ndx` ' .
                'ON ' . $input['db_prefix'] . 'messages(`albumName`);');
        $db->exec('CREATE INDEX `msg_assetname_ndx` ' .
                'ON ' . $input['db_prefix'] . 'messages(`assetName`);');
        $db->commit();
    } catch (PDOException $e) {
        $errors['db_error'] = $e->getMessage();
        require template_getpath('install.php');
        die;
    }
}

function create_config_files() {
    global $input;

    // Write config file
    edit_config_file(
            $input['php_cli_cmd'], $input['rsync_pgm'], $input['application_url'], $input['repository_basedir'], $input['organization_name'], 
            $input['organization_url'], $input['copyright'], $input['mailto_alert'], $input['ezcast_basedir'], $input['db_type'], $input['db_host'], 
            $input['db_login'], $input['db_passwd'], $input['db_name'], $input['db_prefix'], $input['recorder_user'], $input['recorder_basedir'], 
            $input['ezmanager_host'], $input['ezmanager_user'], !empty($input['classrooms_category_enabled']) ? true : false, 
            !empty($input['add_users_enabled']) ? true : false, !empty($input['recorder_password_storage_enabled']) ? true : false, 
            !empty($input['use_course_name']) ? true : false, !empty($input['use_user_name']) ? true : false, !empty($input['https_ready']) ? true : false
    );
}

function add_first_user() {
    global $input;
    global $db_type;
    global $db_host;
    global $db_login;
    global $db_passwd;
    global $db_name;
    global $db_prefix;

    $db_type = $input['db_type'];
    $db_host = $input['db_host'];
    $db_login = $input['db_login'];
    $db_passwd = $input['db_passwd'];
    $db_name = $input['db_name'];
    $db_prefix = $input['db_prefix'];

    // Add the first user in database 
    $first_user = file_get_contents("../first_user");
    $first_user = explode(" , ", $first_user);

    $user_ID = $first_user[0];
    $surname = $first_user[3];
    $forename = $first_user[2];
    $passwd = $first_user[1];
    $permissions = 1;

    //   try {
    if (!db_ready()) {
        $stmts = statements_get();
        db_prepare($stmts);
    }
    db_user_create($user_ID, $surname, $forename, $passwd, $permissions);
    add_admin_to_file($user_ID);
    push_users_to_ezmanager();
    db_log(db_gettable('users'), 'Created user ' . $user_ID, $_SESSION['user_login']);
    db_close();
    //  } catch (PDOException $e) {
    //      $errors['db_error'] = $e->getMessage();
    //      require template_getpath('install.php');
    //      die;
    //  }


    session_destroy();
    unlink("../first_user");

    require template_getpath('install_success.php');
}
