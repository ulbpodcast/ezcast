<?php

/** All static variable common to all ezcast projects go here
 * 
 *  */

require_once('logger.php');
include_once('config.inc');

$logger = new logger($log_level_threshold);

if($debug_mode)
{
    ini_set("display_errors", 1);
    error_reporting(E_ALL); 
}