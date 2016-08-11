<?php

/** All static variable common to all ezcast projects go here
 *  */

include_once('config.inc');
require_once('logger_server.php');

if(isset($timezone))
    date_default_timezone_set($timezone);

//Uncomment this to print all log message to screen (may be useful to debug services or such)
//Logger::$print_logs = true;

if($debug_mode) {
    error_reporting(E_ALL); 
 //   ini_set("display_errors", 1);
}

$logger = new ServerLogger();

require_once("custom_error_handling.php");
