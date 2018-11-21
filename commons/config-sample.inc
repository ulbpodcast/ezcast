<?php

require_once('logger_server.php');

// Get default configuration (if not already defined)
if(basename(__FILE__) != "config-sample.inc" && !isset($sample_config_version)) {
    // Require "config-sample.inc" in commons and not in other folder (ezadmin, ezplayer, ezmanager)
    require_once(__DIR__.'/config-sample.inc');
    // Keep old config
    $sample_config_version = $config_version;
}

/*
 * This config file contains global config for the server
 * to run. It can be edited following your particular needs.
 * 
 * Those variables are required by EZcast to run. 
 * DO NOT REMOVE ANY OF THEM
 */

// Dev: Change this value everytime you add an option to this file, as well as the value in common.inc. When version in 
// config and common.inc don't match, a warning is triggered.
$config_version = "20170824"; 

// Name of the organization hosting EZcast or providing the service
$organization_name = "© Your organization name"; //this will also affect choosen translations when generating templates
$organization_url = "http://ulb.ac.be";
$copyright = "Your copyright";
$movie_keywords = 'Movie,keywords';
$timezone = 'Europe/Brussels';

// URL's to EZcast applications
$application_url = "http://localhost/";
$https_ready = false;
$application_safe_url = $https_ready ? preg_replace("/^(http:\/\/)?/", "https://", $application_url) : $application_url;
// Adapt these URL's according to the name of each application in your webspace
// Warning ! If you modify the name of an application, you should also
// edit its location in the files (index.php, ...) located in your webspace
$ezadmin_url = "$application_url/ezadmin";
$ezadmin_safe_url = "$application_safe_url/ezadmin";
$ezmanager_url = "$application_url/ezmanager";
$ezmanager_safe_url = "$application_safe_url/ezmanager";
$ezplayer_url = "$application_url/ezplayer";
$ezplayer_safe_url = "$application_safe_url/ezplayer";
$distribute_url = "$ezmanager_safe_url/distribute.php";

// Absolute paths 
$repository_basedir = "/var/lib/ezcast";
$basedir = "/usr/local/ezcast";
$php_exec_path="/usr/bin/php";
// Database info
$db_type = 'mysql';
$db_host = 'localhost';
$db_port = "";
                                            
$db_login = "ezcast";
$db_passwd = "";
$db_name = "ezcast";
$db_prefix = "ezcast_";

// Paths for the remote recorder
$recorder_user = "ezrecorder";
$recorder_basedir = '/Library/ezrecorder/'; // Path from the root to the recorder
$recorder_subdir = '/etc/users/'; // Path from basedir to the admin.inc file

$php_cli_cmd = "/opt/csw/bin/php"; //locate php cli to use
$rsync_pgm = "/opt/sfw/bin/rsync";
$ssh_pgm = "/usr/bin/ssh";
$apache_username = "www-data"; // used by ezadmin to retrieve public SSH key for renderer creation
$apache_documentroot = "/www/htdocs/"; // used by ezadmin to copy organization logo in the webspace
$ssh_pub_key_location = ""; // leave empty for default location or enter absolute path to Apache user's ssh public key 

$mailto_alert = "mail@your.organization"; //who to call in case of problem in the processing chain

/*
 * Available identification type
 * - file
 * - ldap
 * - sso : SimpleSAMLphp
 */
$auth_methods = array("file", "ldap");
$sso_only=false; 
$forbidden_users=array();                             

$ldap_institution="";	//empty by default   "ucl" for ucl.					
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



/* Setup for SSO Use
 * 
 * SimpleSAML must be installed and setup first
 * update db for sso with: php cli_install_sso.php
 * 
 * Parameter for SimpleSAMLphp 1.4.6
 * - lib           : localization of SSP library
 * - sp            : name of Service Provider profil name
 * 
 * Mapping between sso attribute and userinfo
 * - att_name      : user name
 * - att_firstname : user first name
 * - att_email     : user email
 * - login         : user uid
 */


$sso_ssp_lib = "/var/simplesamlphp/lib";
$sso_ssp_sp = "default-sp";
$sso_ssp_att_name = "sn";
$sso_ssp_att_firstname = "givenName";
$sso_ssp_att_email = "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress";
$sso_ssp_att_email = "mail";
$sso_ssp_att_login = "uid";

// -- Traces -- //
$trace_on = false; // determines whether we want to enable traces on actions or not
$ezplayer_trace_path = $repository_basedir . '/ezplayer/ezplayer_traces/';
$video_split_time = 30; // Time in second to send information that user watch a video
$display_trace_stats = false; // Display or not stat on ezmanager

//Transcoding streaming video live  !!!!WARNING $repository_basedir has to be shared between the two servers && remote server must have right to write in "$repository_basedir/streamEncode"
$transcodeStreaming=false;                                            
$externalClients=array();        // use : $externalClients=array("user1@ip", "user2@ip")        !!!! ssh link must be establish between ezcast && those clients                                                                                            

// -- Alternate server as streaming distribution source -- //

//enable file syncing (does not redirect user)
$streaming_video_alternate_server_enable_sync = false;
// redirect users to given adress (see next options). Requires $streaming_video_alternate_server_enable_sync
$streaming_video_alternate_server_enable_redirect = false;
// when redirect is enabled, redirect with X percent chance (default to 100%)
$streaming_video_alternate_server_redirect_chance = 100; //%
// -- -- //
    
$streaming_video_alternate_server_user = "user";
$streaming_video_alternate_server_keyfile_private = "";
$streaming_video_alternate_server_keyfile_pub = "";
$streaming_video_alternate_server_keyfile_password = "";
$streaming_video_alternate_server_address = "127.0.0.1";
$streaming_video_alternate_server_document_root = "/var/www/stream/";
$streaming_video_alternate_server_uri = "stream/";
//!! The remote server must have crossdomain allowed

// -- debugging -- //
$debug_mode = false; //debug mode print recorder events and php errors to console
// -- -- //

$common_config_defined = true; //config and common include each other... apparently php does not consider a file already 
// included until he completely finished including it. This variable is a workaround for this problem.

//define size of course_code
$max_course_code_size=20; //!!! WARNING => Linked to the DB
//define size of album_label
$max_album_label_size=50;

$enable_anon_access_control = false; //Enable the user to choose if the album is accessible from anonym or not
$default_anon_access = 'false'; //default value when a video is uploaded
//dysplay the terms of use at the first connection or anonym
$enable_termsOfUse=false;                      

// If not in config simple
if(basename(__FILE__) != "config-sample.inc") {
    // load static variables
    require_once(__DIR__ . '/common.inc');
    require_once('logger_server.php');
}
// Various parameters
$accepted_languages = array('fr', 'en'); // Accepted parameters for the language to use
$accepted_file_extensions = array('.html', '.xhtml', '.xml', '.htm', '.php'); // File with these extensions will be considered as template files

//Parameters for inclusion of audio files
$enable_audio_submit=false;                 //Accept audio files in EZmanager submit form
$enable_ezplayer_audio_download=false;      // enable the viewer to download mp3 files if exist

$matomoScript="";
