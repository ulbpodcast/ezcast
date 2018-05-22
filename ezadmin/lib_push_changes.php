<?php

require_once(__DIR__ . '/../commons/config.inc');
require_once(__DIR__ . '/../commons/lib_sql_management.php');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//return errors in array
function push_changes() 
{
    global $logger;
    $failed_cmd = array();
    
    // Save additional users into ezmanager
    push_users_to_ezmanager($failed_cmd);
    // Save changes to classroom into ezmanager
    push_classrooms_to_ezmanager($failed_cmd);
    // Save users & courses into recorder
    push_users_courses_to_recorder($failed_cmd);

    // Save admins into ezmanager & recorders
    push_admins_to_recorders_ezmanager($failed_cmd);
    
    $logger->log(EventType::TEST, LogLevel::NOTICE, "Pushed changes to manager & recorders", array(basename(__FILE__)));
    return $failed_cmd;
}

/**
 * Push users to ezmager
 * @global type $ezmanager_host
 * @global type $ezmanager_user
 * @global type $ezmanager_basedir
 * @global type $ezmanager_subdir
 * @global type $basedir
 * @return boolean
 */
function push_users_to_ezmanager(&$errors = array())
{
    global $ezmanager_host;
    global $ezmanager_user;
    global $ezmanager_basedir;
    global $ezmanager_subdir;
    global $basedir;

    $users = db_users_internal_get();

    $pwfile = '<?php' . PHP_EOL;
    foreach ($users as $u) {
        $pwfile .= '$users[\'' . $u['user_ID'] . '\'][\'password\']=\'' . addslashes($u['recorder_passwd']) . '\';' . PHP_EOL;
        $pwfile .= '$users[\'' . $u['user_ID'] . '\'][\'full_name\']=\'' . $u['forename'] . ' ' . $u['surname'] . '\';' . PHP_EOL;
        $pwfile .= '$users[\'' . $u['user_ID'] . '\'][\'email\']=\'\';' . PHP_EOL . PHP_EOL;
    }

    $ok = file_put_contents(__DIR__.'/var/pwfile.inc', $pwfile);
    if(!$ok) {
        array_push($errors, "Failed to write var/pwfile.inc ");
        return false;
    }
        

    // Copying on ezmanager
    if (empty($ezmanager_host) || !isset($ezmanager_host)) {
        // Local copy
        $filepath = $basedir . '/commons/pwfile.inc';
        $res = file_put_contents($filepath, $pwfile);
        if ($res === false) {
            array_push($errors, "Failed to write $filepath ");
            return false;
        }
    } else {
        // Remote copy
        $cmd = 'scp -o ConnectTimeout=10 -o BatchMode=yes '.__DIR__.'/var/pwfile.inc ' . $ezmanager_user . '@' . $ezmanager_host .
                ':' . $ezmanager_basedir . $ezmanager_subdir;
        exec($cmd, $output, $return_var);

        if ($return_var != 0) {
            array_push($errors, "Faled to scp to Manager. Command $cmd");
            return false;
        }
    }
    return true;
}

/**
 * Overwrites classroom_recorder_ip.inc in ezmanager
 */
function push_classrooms_to_ezmanager(&$errors = array())
{
    global $ezmanager_host;
    global $ezmanager_user;
    global $ezmanager_basedir;
    global $ezmanager_subdir;

    $classrooms = db_classrooms_list();

    $incfile = '<?php' . PHP_EOL;
    $incfile .= '$idx=0;' . PHP_EOL . PHP_EOL;

    foreach ($classrooms as $c) {
        if (!empty($c['name'])) {
            $incfile .= '//' . $c['name'] . PHP_EOL;
        } else {
            $incfile .= '//' . $c['room_ID'] . PHP_EOL;
        }

        $incfile .= '$podcv_ip[$idx]="' . $c['IP'] . '";' . PHP_EOL;
        $incfile .= '$podcs_ip[$idx]="' . $c['IP_remote'] . '";' . PHP_EOL;
        $incfile .= '$idx+=1;' . PHP_EOL . PHP_EOL;
    }

    $incfile .= '?>';

    file_put_contents(__DIR__.'/var/classroom_recorder_ip.inc', $incfile);

    // Copying on ezmanager
    if (empty($ezmanager_host) || !isset($ezmanager_host)) {
        // Local copy
        $res = file_put_contents($ezmanager_basedir . $ezmanager_subdir . '/classroom_recorder_ip.inc', $incfile);
        if ($res === false) {
            array_push($errors, "Failed to write classroom_recorder_ip");
            return false;
        }
    } else {
        // Remote copy
        exec('ping -c 1 ' . $ezmanager_host, $output, $return_val);
        if ($return_val == 0) {
            $cmd = 'scp -o ConnectTimeout=10 -o BatchMode=yes '.__DIR__.'/var/classroom_recorder_ip.inc ' . $ezmanager_user .
                    '@' . $ezmanager_host . ':' . $ezmanager_basedir . $ezmanager_subdir;
            exec($cmd, $output, $return_var);
        }

        if ($return_var != 0) {
            array_push($errors, "Failed to push classroom_recorder_ip. Cmd: $cmd");
            return false;
        }
    }

    return true;
}

/**
 * Overwrites renderers.inc in ezmanager MB 8/4/2018 OBSOLETE
 */
function push_renderers_to_ezmanager()
{
    global $ezmanager_host;
    global $ezmanager_user;
    global $ezmanager_basedir;
    global $ezmanager_subdir;


    // Copying on ezmanager
    if (empty($ezmanager_host) || !isset($ezmanager_host)) {
        // Local copy
        $res = copy(__DIR__.'/renderers.inc', $ezmanager_basedir . $ezmanager_subdir . '/renderers.inc');
        if ($res === false) {
            return false;
        }
    } else {
        // Remote copy
        exec('ping -c 1 ' . $ezmanager_host, $output, $return_val);
        if ($return_val == 0) {
            $cmd = 'scp -o ConnectTimeout=10 -o BatchMode=yes '.__DIR__.'/renderers.inc ' . $ezmanager_user . '@' .
                    $ezmanager_host . ':' . $ezmanager_basedir . $ezmanager_subdir;
            exec($cmd, $output, $return_var);
        }

        if ($return_var != 0) {
            return false;
        }
    }

    return true;
}

/**
 * Overwrites the admin.inc files on the recorders and ezmanager
 * with the "new" admins as set in the DB
 */
function push_admins_to_recorders_ezmanager(&$failed_cmd = array())
{
    global $recorder_user;
    global $recorder_basedir;
    global $recorder_subdir;
    global $ezmanager_host;
    global $ezmanager_user;
    global $ezmanager_basedir;
    global $ezmanager_subdir;
    global $ezplayer_basedir;
    global $ezplayer_subdir;

    $success = true;
    
    if (!db_ready()) {
        require_once __DIR__ . '/../commons/lib_sql_management.php';
        $stmts = statements_get();
        db_prepare($stmts);
    }

    $classrooms = db_classrooms_list_enabled();
    $admins = db_admins_list();

    // Writing admin.inc file
    $admins_str = '<?php' . PHP_EOL;
    foreach ($admins as $a) {
        $admins_str .= '$admin[\'' . $a['user_ID'] . '\']=true;' . PHP_EOL;
    }
    
    file_put_contents(__DIR__.'/var/admin.inc', $admins_str);

    // Copying on recorders
    foreach ($classrooms as $c) {
        exec('ping -c 1 ' . $c['IP'], $output, $return_val);
        if ($return_val == 0) {
            $cmd = 'scp -o ConnectTimeout=10 '.__DIR__.'/var/admin.inc ' . $recorder_user . '@' . $c['IP'] . ':' .
                    $recorder_basedir . $recorder_subdir;
            exec($cmd, $output, $return_var);
        }
    }

    // Copying on ezmanager
    if (empty($ezmanager_host) || !isset($ezmanager_host)) {
        // Local copy
        $filepath = $ezmanager_basedir . $ezmanager_subdir . '/admin.inc';
        $res = file_put_contents($filepath, $admins_str);
        if ($res === false) {
            array_push($failed_cmd , "Failed to write to $filepath");
            $success = false;
        }
    } else {
        // Remote copy
        exec('ping -c 1 ' . $ezmanager_host, $output, $return_val);
        if ($return_val == 0) {
            $cmd = 'scp -o ConnectTimeout=10 '.__DIR__.'/var/admin.inc ' . $ezmanager_user . '@' . $ezmanager_host . ':' .
                    $ezmanager_basedir . $ezmanager_subdir;
            exec($cmd, $output, $return_var);
            if ($return_val == 0) {
                array_push($failed_cmd , "Failed to copy to remote manager. Cmd: $cmd ");
                $success = false;
            }
        }
    }

    // Copying on ezplayer
    $player_filepath = $ezplayer_basedir . $ezplayer_subdir . '/admin.inc';
    $res = file_put_contents($ezplayer_basedir . $ezplayer_subdir . '/admin.inc', $admins_str);
    if ($res === false) {
        array_push($failed_cmd , "Failed to copy to player. Cmd: $player_filepath ");
        $success = false;
    }

    return $success;
}

/**
 * Pushes users (htpasswd) and associations between users and courses (courselist.php)
 */
function push_users_courses_to_recorder(&$failed_cmd = array())
{
    global $recorder_user;
    global $recorder_basedir;
    global $recorder_subdir;
    global $recorder_password_storage_enabled;
        
    if (!db_ready()) {
        $statements = statements_get();
        db_prepare($statements);
    }

    $users = db_users_in_recorder_get();
    $classrooms = db_classrooms_list_enabled();

    //htpasswd
    $htpasswd = '';
    $previous_user = "";
    $user_added=array();
    foreach ($users as $u) {
        if (!isset($user_added[$u['user_ID']])) {
            $htpasswd .= $u['user_ID'] . ':' . $u['recorder_passwd'] . PHP_EOL;
            $user_added[$u['user_ID']]=true;//prevent duplicate entries
        }
    }
    file_put_contents(__DIR__.'/var/htpasswd', $htpasswd);

    //courselist.php
    $courselist = '<?php' . PHP_EOL;
    
    foreach ($users as $u) {
        $courseCode = (isset($u['course_code_public']) && !empty($u['course_code_public'])) ? $u['course_code_public'] : $u['course_code'];
        $title = (isset($u['shortname']) && !empty($u['shortname'])) ? $u['shortname'] : $u['course_name'];
        $courselist .= '$course[\'' . $u['user_ID'] . '\'][\'' . $u['course_code'] . '\'] = "' . $courseCode.'-'.$title . '";' . PHP_EOL;
        $courselist .= '$users[\'' . $u['user_ID'] . '\'][\'full_name\']="' . $u['forename'] . ' ' . $u['surname'] . '";' . PHP_EOL;
        $courselist .= '$users[\'' . $u['user_ID'] . '\'][\'email\']="";' . PHP_EOL;
    }
    
    $courselist .= '?>';
    file_put_contents(__DIR__.'/var/courselist.php', $courselist);
    
    // Upload all this on server
    $return_var = 0;
    $error = false;
    foreach ($classrooms as $c) {
        exec('ping -c 1 ' . $c['IP'], $output, $return_val);
        if ($return_val == 0) {
            $cmd = 'scp -o ConnectTimeout=10 -o BatchMode=yes '.__DIR__.'/var/htpasswd ' . $recorder_user . '@' . $c['IP'] . ':' .
                    $recorder_basedir . $recorder_subdir;
            exec($cmd, $output, $return_var);
            if ($return_var != 0) {
                array_push($failed_cmd, $cmd);
                $error = true;
            }
            $cmd = 'scp -o ConnectTimeout=10 -o BatchMode=yes '.__DIR__.'/var/courselist.php ' . $recorder_user . '@' . $c['IP'] .
                    ':' . $recorder_basedir . $recorder_subdir;
            exec($cmd, $output, $return_var);
            if ($return_var != 0) {
                array_push($failed_cmd, $cmd);
                $error = true;
            }
        } else {
            array_push($failed_cmd, "Failed to ping ". $c['IP']);
            $error = true;
        }
    }
    
    return $error === false;
}

///// NOTIFICATION ALERT /////

/**
 * indicate/clear Changes have been made but not saved yet and should be pushed to ezrecorders
 * @global string $ezrecorder_need_files_pushed_path
 * @param boolean $enable
 */
function notify_changes($enable = true)
{
    global $ezrecorder_need_files_pushed_path;
    if ($enable) {
        if(isset($_SESSION)) $_SESSION['changes_to_push'] = true;
        //create file whose presence will trigger push (by a cron)
        touch($ezrecorder_need_files_pushed_path);
    } else {
        if(isset($_SESSION)) unset($_SESSION['changes_to_push']);
        //remove file whose presence will trigger push (by a cron)
        if(file_exists($ezrecorder_need_files_pushed_path))unlink($ezrecorder_need_files_pushed_path);
    }
}
/**
 * indicate/clear Changes have been made but not saved yet and should be pushed to ezrecorders
 * @global string $ezrecorder_need_files_pushed_path
 * @param boolean $enable
 */
function notify_changes_isset()
{
    global $ezrecorder_need_files_pushed_path;
     
    return file_exists($ezrecorder_need_files_pushed_path);//if file exists return true
     
}