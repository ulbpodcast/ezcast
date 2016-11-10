<?php

require_once('logger_server.php');

/*
 * This config file contains global config for the server
 * to run. It can be edited following your particular needs.
 * 
 * Those variables are required by EZcast to run. 
 * DO NOT REMOVE ANY OF THEM
 */

// Dev: Change this value everytime you add an option to this file, as well as the value in common.inc. When version in config and common.inc don't match, a warning is triggered.
$config_version = "20161003"; 

// Name of the organization hosting EZcast or providing the service
$organization_name = "© Your organization name";
$organization_url = "http://ulb.ac.be";
$copyright = "Your copyright";
$movie_keywords = 'Movie,keywords';
$timezone = 'Europe/Brussels';

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
$basedir = "/usr/local/ezcast";

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

// -- Alternate server as streaming distribution source -- //

//enable file syncing (does not redirect user)
$streaming_video_alternate_server_enable_sync = false;
// redirect users to given adress (see next options). Requires $streaming_video_alternate_server_enable_sync
$streaming_video_alternate_server_enable_redirect = false;
// -- -- //
    
$streaming_video_alternate_server_user = "user";
$streaming_video_alternate_server_keyfile = "";
$streaming_video_alternate_server_address = "127.0.0.1";
$streaming_video_alternate_server_document_root = "/var/www/stream/";
$streaming_video_alternate_server_uri = "stream/";

// -- debugging -- //
$debug_mode = false; //debug mode print recorder events and php errors to console
// -- -- //

$common_config_defined = true; //config and common include each other... apparently php does not consider a file already included until he completely finished including it. This variable is a workaround for this problem.

// load static variables
require_once(__DIR__ . '/common.inc');
require_once('logger_server.php');