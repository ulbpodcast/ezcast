<?php

/*
* EZCAST Commons 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*
* This softare is free; you can redistribute it and/or
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
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
 * This config file contains global config for the server
 * to run. It can be edited following your particular needs.
 * 
 * Those variables are required by EZcast to run. 
 * DO NOT REMOVE ANY OF THEM
 */

// Name of the organization hosting EZcast or providing the service
$organization_name = "© Your organization name";
$organization_url = "http://ulb.ac.be";
$copyright = 'Your copyright';

// URL's to EZcast applications
$application_url = "http://localhost/";
$https_ready = false;
$application_safe_url = $https_ready ? str_replace('http', 'https', $application_url) : $application_url;
// Adapt these URL's according to the name of each application in your webspace
// Warning ! If you modify the name of an application, you should also
// edit its location in the files (index.php, ...) located in your webspace
$ezadmin_url = "$application_url/ezadmin";
$ezadmin_safe_url = "$application_safe_url/ezadmin";
$ezmanager_url = "$application_url/ezmanager";
$ezmanager_safe_url = "$application_safe_url/ezmanager";
$ezplayer_url = "$application_url/ezplayer";
$ezplayer_safe_url = "$application_safe_url/ezplayer";
$distribute_url = "$ezmanager_url/distribute.php";

// Absolute paths 
$repository_basedir = "/var/lib/ezcast";
$repository_path="$repository_basedir/repository";
$basedir = "/usr/local/ezcast";

$ezmanager_basedir = $basedir . '/ezmanager/'; // Path from the root to the source code
$ezplayer_basedir = $basedir . '/ezplayer/'; // Path from the root to the source code

// Database info
$db_type = 'mysql';
$db_host = 'localhost';
$db_login= "ezcast";
$db_passwd = "";
$db_name = "ezcast";
$db_prefix = "ezcast_";

// Paths for the remote recorder
$recorder_user = "ezrecorder";
$recorder_basedir = '/Library/ezrecorder/'; // Path from the root to the recorder
$recorder_subdir = '/etc/users/'; // Path from basedir to the admin.inc file
$recorder_recorddir = "~$recorder_user/"; // Path from the root to the recorder local repository 

$php_cli_cmd = "/opt/csw/bin/php";//locate php cli to use
$rsync_pgm = "/opt/sfw/bin/rsync";
$ssh_pgm = "/usr/bin/ssh";
$apache_username = "www-data"; // used by ezadmin to retrieve public SSH key for renderer creation
$apache_documentroot = "/www/htdocs/"; // used by ezadmin to copy organization logo in the webspace
$ssh_pub_key_location = ""; // leave empty for default location or enter absolute path to Apache user's ssh public key 

$mailto_alert = "mail@your.organization"; //who to call in case of problem in the processing chain



$auth_methods = array("file", "ldap");

# JSON FILES FOR LDAP
/*
 * For each ldap_server:
 *  - hostname: the address of the server
 *  - rdn     : the rdn (Relative Distinguished Name) used to bind the ldap sever
 *    --> !LOGIN will be replaced by the user who tries to connect to the server
 *  - base_dn : the base directory for ldap_search
 *  - filter  : the filter for ldap_search
 *    --> !LOGIN will be replaced by the user who tries to connect to the server
 *  - password: the password for user
 */

/* ldap_servers_auth : contains the list of LDAP servers used to authenticate 
 * a user that has access to the ldap server and can retrieve information about
 * himself
 */
$ldap_servers_auth_json_file = "$basedir/commons/ldap_servers_auth.json";

/* ldap_servers_cred : contains the list of LDAP servers used to search and 
 * retrieve information about a given user
 */
$ldap_servers_cred_json_file = "$basedir/commons/ldap_servers_cred.json";


/*******************************************************/
/** S C H E D U L I N G     C O N F I G U R A T I O N **/
/*******************************************************/

$config['paths']['scheduler'] = "$repository_basedir/queues/scheduling/";
$config['paths']['queue'] = $config['paths']['scheduler'] . 'queue/';
$config['paths']['processing'] = $config['paths']['scheduler'] . 'processing/';
$config['paths']['processed'] = $config['paths']['scheduler'] . 'processed/';
$config['paths']['failed'] = $config['paths']['scheduler'] . 'failed/';
$config['paths']['frozen'] = $config['paths']['scheduler'] . 'frozen/';
$config['paths']['logs'] = "$repository_basedir/queues/scheduling/logs.log";
$config['paths']['var'] = "$ezmanager_basedir/var/";
$config['keys']['sem'] = 82397779218067476;
$config['scheduler']['priority'] = 10;
$config['ssh']['timeout'] = 15;


?>
