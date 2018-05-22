<?php

/*
 * EZCAST EZadmin
 * Copyright (C) 2016 Université libre de Bruxelles
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
 * @package ezcast.ezadmin.lib.various
 */

require_once(__DIR__."/../commons/lib_database.php");

/**
 * Parses the config file and returns the settings that can be changed in it.
 */
function parse_config_file()
{
    include 'config.inc';

    $res = array(
        'recorders_option' => $classrooms_category_enabled,
        'add_users_option' => $add_users_enabled,
        'recorder_password_storage_option' => $recorder_password_storage_enabled,
        'user_name_option' => $use_user_name
    );

    return $res;
}

/**
 * Updates ezadmin config file
 * @param type $recorder_option
 * @param type $add_users_option
 * @param type $recorder_password_storage_option
 * @param type $use_user_name_option
 */
function update_config_file($recorder_option, $add_users_option, $recorder_password_storage_option, $use_user_name_option)
{
    $config = file_get_contents('config.inc');

    $conf1 = ($recorder_option) ? 'true' : 'false';
    $conf2 = ($add_users_option) ? 'true' : 'false';
    $conf3 = ($recorder_password_storage_option) ? 'true' : 'false';
    $conf4 = ($use_user_name_option) ? 'true' : 'false';

    $config = preg_replace('/\$classrooms_category_enabled = (.+);/', '\$classrooms_category_enabled = ' . $conf1 . ';', $config);
    $config = preg_replace('/\$add_users_enabled = (.+);/', '\$add_users_enabled = ' . $conf2 . ';', $config);
    $config = preg_replace('/\$recorder_password_storage_enabled = (.+);/', '\$recorder_password_storage_enabled = ' . $conf3 . ';', $config);
    $config = preg_replace('/\$use_user_name = (.+);/', '\$use_user_name = ' . $conf4 . ';', $config);
    
    file_put_contents('config.inc', $config);
}

/**
 * Edit commons's config file
 * @param type $php_cli_cmd
 * @param type $rsync_pgm
 * @param type $application_url
 * @param type $repository_basedir
 * @param type $organization_name
 * @param type $copyright
 * @param type $mailto_alert
 * @param type $basedir
 * @param type $db_type
 * @param type $db_host
 * @param type $db_login
 * @param type $db_passwd
 * @param type $db_name
 * @param type $db_prefix
 * @param type $recorder_user
 * @param type $recorder_basedir
 * @param type $ezmanager_host
 * @param type $ezmanager_url
 * @param type $classrooms_category_enabled
 * @param type $add_users_enable
 * @param type $recorder_password_storage_enabled
 * @param type $use_user_name
 * @param type $https_ready
 */
function edit_config_file(
    $php_cli_cmd,
    $rsync_pgm,
    $application_url,
    $repository_basedir,
    $organization_name,
    $organization_url,
    $copyright,
    $mailto_alert,
    $basedir,
    $db_type,
    $db_host,
    $db_login,
    $db_passwd,
    $db_name,
    $db_prefix,
    $recorder_user,
    $recorder_basedir,
    $ezmanager_host,
    $ezmanager_url,
    $classrooms_category_enabled,
    $add_users_enable,
    $recorder_password_storage_enabled,
    $use_user_name,
    $https_ready
) {
    $global_config = (file_exists('../commons/config.inc')) ?
            file_get_contents('../commons/config.inc') :
            file_get_contents('../commons/config-sample.inc');

    $conf = ($https_ready) ? 'true' : 'false';

    $global_config = preg_replace('/\$php_cli_cmd = (.+);/', '\$php_cli_cmd = "' . $php_cli_cmd . '";', $global_config);
    $global_config = preg_replace('/\$rsync_pgm = (.+);/', '\$rsync_pgm = "' . $rsync_pgm . '";', $global_config);
    $global_config = preg_replace('/\$https_ready = (.+);/', '\$https_ready = ' . $conf . ';', $global_config);
    $global_config = preg_replace('/\$organization_name = (.+);/', '\$organization_name = "' . $organization_name . '";', $global_config);
    $global_config = preg_replace('/\$organization_url = (.+);/', '\$organization_url = "' . $organization_url . '";', $global_config);
    $global_config = preg_replace('/\$copyright = (.+);/', '\$copyright = "' . $copyright . '";', $global_config);
    $global_config = preg_replace('/\$mailto_alert = (.+);/', '\$mailto_alert = "' . $mailto_alert . '";', $global_config);
    $global_config = preg_replace('/\$application_url = (.+);/', '\$application_url = "' . $application_url . '";', $global_config);
    $global_config = preg_replace('/\$repository_basedir = (.+);/', '\$repository_basedir = "' . $repository_basedir . '";', $global_config);
    $global_config = preg_replace('/\$basedir = (.+);/', '\$basedir = "' . $basedir . '";', $global_config);
    $global_config = preg_replace('/\$db_type = (.+);/', '\$db_type = "' . $db_type . '";', $global_config);
    $global_config = preg_replace('/\$db_host = (.+);/', '\$db_host = "' . $db_host . '";', $global_config);
    $global_config = preg_replace('/\$db_login = (.+);/', '\$db_login = "' . $db_login . '";', $global_config);
    $global_config = preg_replace('/\$db_passwd = (.+);/', '\$db_passwd = "' . $db_passwd . '";', $global_config);
    $global_config = preg_replace('/\$db_name = (.+);/', '\$db_name = "' . $db_name . '";', $global_config);
    $global_config = preg_replace('/\$db_prefix = (.+);/', '\$db_prefix = "' . $db_prefix . '";', $global_config);
    $global_config = preg_replace('/\$recorder_user = (.+);/', '\$recorder_user = "' . $recorder_user . '";', $global_config);
    $global_config = preg_replace('/\$recorder_basedir = (.+);/', '\$recorder_basedir = "' . $recorder_basedir . '";', $global_config);

    file_put_contents('../commons/config.inc', $global_config);

    $config = file_get_contents('config-sample.inc');

    $conf1 = ($classrooms_category_enabled) ? 'true' : 'false';
    $conf2 = ($add_users_enable) ? 'true' : 'false';
    $conf3 = ($recorder_password_storage_enabled) ? 'true' : 'false';
    $conf4 = ($use_user_name) ? 'true' : 'false';

    $config = preg_replace('/\$classrooms_category_enabled = (.+);/', '\$classrooms_category_enabled = ' . $conf1 . ';', $config);
    $config = preg_replace('/\$add_users_enabled = (.+);/', '\$add_users_enabled = ' . $conf2 . ';', $config);
    $config = preg_replace('/\$recorder_password_storage_enabled = (.+);/', '\$recorder_password_storage_enabled = ' . $conf3 . ';', $config);
    $config = preg_replace('/\$use_user_name = (.+);/', '\$use_user_name = ' . $conf4 . ';', $config);

    file_put_contents('config.inc', $config);
    copy("../ezmanager/config-sample.inc", "../ezmanager/config.inc");
    copy("../ezplayer/config-sample.inc", "../ezplayer/config.inc");
}

/**
 * Returns the list of admins
 * @return array
 */
function parse_admin_file()
{
    include 'admin.inc';

    $admins = array();
    foreach ($users as $u => $whocares) {
        $userinfos = db_user_read($u);
        $admins[] = array(
            'user_ID' => $u,
            'forename' => $userinfos['forename'],
            'surname' => $userinfos['surname']
        );
    }

    return $admins;
}

function renderer_exists($name)
{
    if (file_exists('renderers.inc')) {
        $renderers = require_once 'renderers.inc';
        foreach ($renderers as $index => $renderer) {
            if (strtoupper($renderer['name']) == strtoupper($name)) {
                return $index;
            }
        }
    } else {
        return false;
    }
    return false;
}

function renderer_get($name)
{
    if (file_exists('renderers.inc')) {
        $renderers = require_once 'renderers.inc';
        foreach ($renderers as $renderer) {
            if (strtoupper($renderer['name']) == strtoupper($name)) {
                return $renderer;
            }
        }
    }
    return false;
}

function renderer_update_enabled($name, $enable, &$error)
{
    $renderer_index = renderer_exists($name);

    if ($renderer_index === false) {
        $error = "renderer_not_found";
        return false;
    }

    $renderers = include 'renderers.inc';
    $renderer = $renderers[$renderer_index];

    if ($enable) {
        $remote_test = dirname($renderer['launch']) . "/cli_renderer_test.php";
        if (test_over_ssh($renderer['client'], $renderer['host'], 30, $renderer['php'], $remote_test, $error)) {
            $renderers[$renderer_index]["status"] = "enabled";
        } else {
            return false;
        }
    } else {
        $renderers[$renderer_index]["status"] = "disabled";
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($renderers, true) . ';';
    $string .= PHP_EOL . "?>";

    $res = file_put_contents('renderers_tmp.inc', $string);
    if ($res === false) {
        $error = "renderer_file_error";
        return false;
    }

    if (file_exists('./renderers.inc.old')) {
        unlink('./renderers.inc.old');
    }
    copy('./renderers.inc', './renderers.inc.old');
    rename('./renderers_tmp.inc', './renderers.inc');
    $res=renderers_push();
    return $res;
}

/**
 * copies renderers.inc file from ezadmin to common (to be used by ezmanager scheduler)
 * @return bool true on success
 */
function renderers_push()
{   
    $res=copy('./renderers.inc', '../commons/renderers.inc');
    return $res;
}

function renderer_delete($name)
{
    $renderer_index = renderer_exists($name);

    if ($renderer_index === false) {
        $error = "renderer_not_found";
        return false;
    }

    $renderers = include 'renderers.inc';
    unset($renderers[$renderer_index]);


    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($renderers, true) . ';';
    $string .= PHP_EOL . "?>";

    $res = file_put_contents('renderers_tmp.inc', $string);
    if ($res === false) {
        $error = "renderer_file_error";
        return false;
    }

    if (file_exists('./renderers.inc.old')) {
        unlink('./renderers.inc.old');
    }
    copy('./renderers.inc', './renderers.inc.old');
    rename('./renderers_tmp.inc', './renderers.inc');
    renderers_push();
    return true;
}

function renderer_add($name, $address, $user, $status, $root_path, $php_cli)
{
    if (!file_exists('renderers.inc')) {
        $renderers = array();
    } else {
        $renderers = require_once 'renderers.inc';
    }

    array_push($renderers, array(
        "name" => $name,
        "host" => $address,
        "client" => $user,
        "status" => ($status) ? 'enabled' : 'disabled',
        "home" => "$root_path/bin",
        "downloading_dir" => "$root_path/queues/downloading",
        "downloaded_dir" => "$root_path/queues/downloaded",
        "processed_dir" => "$root_path/queues/processed",
        "statistics" => "$root_path/bin/cli_statistics_get.php",
        "php" => $php_cli,
        "launch" => "$root_path/bin/intro_title_movie.bash",
        "kill" => "$root_path/bin/cli_job_kill.php"
    ));
    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($renderers, true) . ';';
    $string .= PHP_EOL . "?>";

    $res = file_put_contents('renderers_tmp.inc', $string);
    if ($res === false) {
        return false;
    }

    if (file_exists('./renderers.inc.old')) {
        unlink('./renderers.inc.old');
    }
    copy('./renderers.inc', './renderers.inc.old');
    rename('./renderers_tmp.inc', './renderers.inc');
    $res=renderers_push();
    return $res;
}

/**
 * Save a new admin
 * @param type $netid
 * @return boolean
 */
function add_admin_to_file($netid)
{
    if (!file_exists('admin.inc')) {
        file_put_contents('admin.inc', "<?php" . PHP_EOL);
        file_put_contents('admin.inc', "?>", FILE_APPEND);
    }
    $admins = file('admin.inc');
    $size = count($admins);

    // Moving the PHP tag down one line
    $admins[] = $admins[$size - 1];
    // Adding new admin at second-to-last line
    $admins[$size - 1] = '$users["' . $netid . '"]=1;' . PHP_EOL;

    $res = file_put_contents('admin_tmp.inc', implode('', $admins));
    if ($res === false) {
        return false;
    }

    if (file_exists('./admin.inc.old')) {
        unlink('./admin.inc.old');
    }
    rename('./admin.inc', './admin.inc.old');
    rename('./admin_tmp.inc', './admin.inc');
}

/**
 * Remove an admin from the list
 * @param type $netid
 * @return boolean
 */
function remove_admin_from_file($netid)
{
    if (!file_exists('admin.inc')) {
        return false;
    }
    $new_admins = preg_grep('/' . $netid . '"]/', file('admin.inc'), true);

    $res = file_put_contents('admin_tmp.inc', implode('', $new_admins));
    if ($res === false) {
        return false;
    }

    if (file_exists('./admin.inc.old')) {
        unlink('./admin.inc.old');
    }
    rename('./admin.inc', './admin.inc.old');
    rename('./admin_tmp.inc', './admin.inc');
}

/**
 * Checks either the ip adress syntax is correct or not
 * @return error_string
 * @param string $ipstr
 * @desc ckeck ip syntax
 */
function checkipsyntax($ipstr)
{
    $res = ipstr2num($ipstr, $net1, $net2, $subnet, $node);
    if ($res > 0) {
        return "Not a valid IP address ($res)";
    } else {
        return "";
    }
}

/**
 * Converts ip as string to numbers
 * @return if>0error
 * @param $ipstr string ip
 * @param $net1 int ip1
 * @param $net2 int ip2
 * @param $subnet ip3
 * @param $nodenum ip4
 * @desc converts ip string to 4 numbers
 */
function ipstr2num($ipstr, &$net1, &$net2, &$subnet, &$node)
{
    $res = preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $ipstr, $regs);
    if (!$res) {
        return 1;
    }
    $returncode = 0;
    $net1 = $regs[1];
    if ($net1 + 0 > 255) {
        $returncode+=2;
    }
    $net2 = $regs[2];
    if ($net2 + 0 > 255) {
        $returncode+=4;
    }
    $subnet = $regs[3];
    if ($subnet + 0 > 255) {
        $returncode+=8;
    }
    $node = $regs[4];
    if ($node + 0 > 255) {
        $returncode+=16;
    }
    return $returncode;
}

function ssh_connection_test($username, $hostname, $timeout, $update_known_hosts = true)
{
    include 'config.inc';
    //check if hostname is real
    $ip_int=  ip2long($hostname); //is it an IP address?
    if($ip_int===false){
      //its not an IP, check if hostname exists  
      $ip =  gethostbyname($hostname);
      if($ip==$hostname){
        //hostname doesn't resolve
        return "hostname_error";
      }
    }
    // test the SSH connection
    exec(
  
        "ssh -o ConnectTimeout=$timeout -o BatchMode=yes " . $username . "@" . $hostname . " \"echo ok\"",
            $output,
  
        $returncode
  
    );

    if ($update_known_hosts && $returncode) {
        // SSH connection failed so we verify that the remote renderer is in
        // the 'known_hosts' file, if not we add it and retry to connect via SSH
        // find .ssh/known_hosts file

        if ($ssh_pub_key_location == "") {
            $ssh_known_hosts = "~$apache_username/.ssh/known_hosts";
        } else {
            $ssh_known_hosts = dirname($ssh_pub_key_location) . "/known_hosts";
        }

        // changes user's relative path in absolute path (required for file_exists(...))
        $ssh_known_hosts = exec("echo `echo $ssh_known_hosts`");

        // retrieves SSH public key of remote renderer
        exec("ssh-keyscan -T $timeout " . $hostname, $output, $returncode);
        if (!$returncode) {
            // adds renderer SSH key to known_hosts file (if not there yet)
            if (!file_exists($ssh_known_hosts) || strpos(file_get_contents($ssh_known_hosts), $output[0]) === false) {
                exec("echo $output[0] >> $ssh_known_hosts", $output, $returncode);
                if ($returncode) {
                    // could not add SSH public key from remote renderer to known_hosts file
                    return "known_hosts_error";
                }
            }
        } else {
            // could not retrieve SSH public key from renderer
            if (!file_exists($ssh_known_hosts) || strpos(file_get_contents($ssh_known_hosts), $hostname) === false) {
                // SSH public key from remote renderer is not in known_hosts yet
                return "keyscan_error";
            }
        }
        // tests the SSH connection
        exec(
            "ssh -o ConnectTimeout=$timeout -o BatchMode=yes " . $username . "@" . $hostname . " \"echo ok\"",
                $output,
            $returncode
        );
        return $returncode ? false : true;
    } elseif ($returncode) {
        return false;
    } else {
        return true;
    }
}

function test_php_over_ssh($ssh_user, $ssh_host, $ssh_timeout, $remote_php)
{
    if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
            " \"if [ -e " . $remote_php . " ]; then echo 'exists'; fi;\"") != 'exists') {
        // PHP binary doesn't exist on remote renderer
        return "php_not_found";
    } else {
        exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                " \"$remote_php -v\"", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'PHP') === false) {
            // PHP not found on remote renderer
            return "php_not_found";
        } else {
            // Test PHP version
            $php_version = substr($output[0], 4, 3);
            if (is_nan($php_version) || (double) $php_version < 5.3) {
                // PHP is deprecated
                return "php_deprecated";
            } else {
                // Test PHP modules
                exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                        " \"$remote_php -m\"", $output, $returncode);
                if (!in_array("SimpleXML", $output)) {
                    return "php_missing_xml";
                }
                if (!in_array("gd", $output)) {
                    return "php_missing_gd";
                }
                unset($output);
                exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes $ssh_user@$ssh_host \"$remote_php -r 'echo serialize(gd_info());'\"", $output, $returncode);
                $gd_info = unserialize(implode($output));
                if (!$gd_info['FreeType Support']) {
                    return "gd_missing_freetype";
                }
            }
        }
        return "";
    }
}

function test_ffmpeg_over_ssh($ssh_user, $ssh_host, $ssh_timeout, $remote_ffmpeg, $built_in_aac = true)
{
    if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host . " \"if [ -e " .
            $remote_ffmpeg . " ]; then echo 'exists'; fi;\"") != 'exists') {
        // FFMPEG binary doesn't exist on remote renderer
        return "ffmpeg_not_found";
    } else {
        exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                " \"$remote_ffmpeg -version\"", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'FFMPEG') === false) {
            // FFMPEG not found on remote renderer
            return "ffmpeg_not_found";
        } else {
            // Test FFMPEG codecs
            $aac_codec = ($built_in_aac) ? 'aac' : 'libfdk_aac';
            $output = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                    " \"$remote_ffmpeg -codecs | grep '$aac_codec'\"");
            if (strpos(strtoupper($output), 'AAC') === false) {
                return "missing_codec_aac";
            }
            $output = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                    " \"$remote_ffmpeg -codecs | grep 'h264'\"");
            if (strpos(strtoupper($output), 'H.264') === false) {
                return "missing_codec_h264";
            }
            return "";
        }
    }
}

function test_ffprobe_over_ssh($ssh_user, $ssh_host, $ssh_timeout, $remote_ffprobe)
{
    if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
            " \"if [ -e " . $remote_ffprobe . " ]; then echo 'exists'; fi;\"") != 'exists') {
        // FFPROBE binary doesn't exist on remote renderer
        return "ffprobe_not_found";
    } else {
        exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                " \"$remote_ffprobe -version\"", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'FFPROBE') === false) {
            // FFMPEG not found on remote renderer
            return "ffprobe_not_found";
        }
        return "";
    }
}

function test_over_ssh($ssh_user, $ssh_host, $ssh_timeout, $remote_php, $remote_test_script, &$error)
{
    if (ssh_connection_test($ssh_user, $ssh_host, $ssh_timeout)) {
        exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $ssh_user . "@" . $ssh_host .
                " \"$remote_php $remote_test_script\"", $output, $returncode);
        if ($returncode || in_array("test ok", $output) === false) {
            $error = $output[0];
            return false;
        }
        return true;
    } else {
        $error = "ssh_error";
        return false;
    }
}

/**
 * Check if a key is in the array
 *
 * @param String $key to test
 * @param Array $array to test
 * @return mixed the value of the array key or empty string if not exist
 */
function empty_str_if_not_def($key, $array)
{
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }
    return "";
}


function array_increment_or_init_static(&$array, $key)
{
    if (array_key_exists($key, $array)) {
        ++$array[$key];
    } else {
        $array[$key] = 1;
    }
}

function array_increment_or_init(&$array, &$key)
{
    if (array_key_exists($key, $array)) {
        ++$array[$key];
    } else {
        $array[$key] = 1;
    }
}

