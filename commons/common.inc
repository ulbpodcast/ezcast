<?php
/** All static variable common to all ezcast projects go here
 *  */

if(!isset($common_config_defined))
    include_once('config.inc');
require_once('logger_server.php');

if(isset($timezone))
    date_default_timezone_set($timezone);

$ezmanager_basedir = $basedir . '/ezmanager/'; // Path from the root to the source code
$ezplayer_basedir = $basedir . '/ezplayer/'; // Path from the root to the source code
$ezadmin_basedir = $basedir . '/ezadmin/'; 

if($debug_mode) {
    error_reporting(E_ALL); 
 //   ini_set("display_errors", 1);
}

if($config_version != "20161003")
    trigger_error("global_config.inc file is not up to date", E_USER_WARNING);

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

if($streaming_video_alternate_server_enable_redirect && !$streaming_video_alternate_server_enable_sync) {
    // this should not happen except if you're using another solution to sync the files
    trigger_error('Possible wrong configuration ($streaming_video_alternate_server_enable_redirect enabled while $streaming_video_alternate_server_enable_sync disabled)', E_USER_WARNING);
}

require_once("custom_error_handling.php");