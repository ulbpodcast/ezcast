<?php
/** All static variable common to all ezcast projects go here
 *  */

if(!isset($common_config_defined))
    include_once(__DIR__.'.config.inc');

require_once(__DIR__.'/logger_server.php');
require_once(__DIR__.'/common_static.inc');

if(isset($timezone))
    date_default_timezone_set($timezone);

$ezmanager_basedir = $basedir . '/ezmanager/'; // Path from the root to the source code
$ezplayer_basedir = $basedir . '/ezplayer/'; // Path from the root to the source code
$ezadmin_basedir = $basedir . '/ezadmin/'; 

if($debug_mode) {
    error_reporting(E_ALL); 
    ini_set("display_errors", 1);
}

$logger = new ServerLogger();
//Uncomment this to print all log message to screen (may be useful to debug services or such)
//Logger::$print_logs = true;


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

/*******************************************************/

$repository_path="$repository_basedir/repository";

//file to indicate changes have been made in ezrecorder passwd or course file (by an admin in ezadmin or a user in ezmanager)
$ezrecorder_need_files_pushed_path="$repository_basedir/ezrecorder_need_files_pushed";

if($streaming_video_alternate_server_enable_redirect && !$streaming_video_alternate_server_enable_sync) {
    // this should not happen except if you're using another solution to sync the files
    trigger_error('Possible wrong configuration ($streaming_video_alternate_server_enable_redirect enabled while $streaming_video_alternate_server_enable_sync disabled)', E_USER_WARNING);
}

$m3u8_master_filename = "master.m3u8";
$m3u8_external_master_filename = "external_master.m3u8";
$m3u8_quality_filename = "live.m3u8";

$course_id_validation_regex = "#[^A-Za-z0-9._-]#"; //all remaining characters must be form a valid unix dir name

require_once("custom_error_handling.php");