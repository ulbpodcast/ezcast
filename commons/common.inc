<?php

/** All static variable common to all ezcast projects go here
 * 
 *  */

require_once('logger_server.php');
include_once('config.inc');

$logger = new ServerLogger();
//Uncomment this to print all log message to screen (may be useful to debug services or such)
//Logger::$print_logs = true;

if($debug_mode)
{
    ini_set("display_errors", 1);
    error_reporting(E_ALL); 
}