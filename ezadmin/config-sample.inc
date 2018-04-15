<?php

require_once "../commons/config.inc";

// General stuff
$appname="EZadmin";

$ezmanager_host = ''; // Leave blank if both ezmanager and ezadmin are on the same machine
$ezmanager_user = ''; // Only useful when ezmanager is on another machine than ezadmin
$ezmanager_subdir = '/'; // Path from the basedir to admin.inc
$ezplayer_subdir = '/'; // Path from the basedir to admin.inc

// User preferences
$recorders_category_enabled = true; // Set to false to hide the "recorders" category
$classrooms_category_enabled = true; // Set to false to hide the "classrooms" category
$add_users_enabled = true; // set to true to have a userlist in ezmanager
$recorder_password_storage_enabled = true; // Set to true to save passwords on the recorders
$use_user_name = false; // Set to true to use user's full name instead of user ID

// Renderers settings
$renderers_options = array(
    // <name> the name of the library to use (used in tmpl_sources/div_create_renderer_step3.php as class name to display input fields)
    // <description> the description shown in a combo list in tmpl_sources/div_create_renderer_step3.php
    // <file> file name of the library
        
        'ffmpeg_built_in_aac' => array('name' => 'ffmpeg_built_in_aac', 'description' => 'FFMPEG command line with Built-in AAC codec', 'file' => 'lib_ffmpeg.php'),
        'ffmpeg_fdk_aac' => array('name' => 'ffmpeg_fdk_aac', 'description' => 'FFMPEG command line built with external lib_fdk_aac codec', 'file' => 'lib_ffmpeg.php'),
);

//cannot be empty
$courses_excluded_from_stats = array(
    "AUTO-TESTS",
);

//path to a custom ezcadmin logo, or false to use default logo. Custom logo must be in webspace in /ezadmin/img/custom directory
$ezadmin_custom_logo = false;

//$ssh_pub_key_location = "/home/ezcast/.ssh/id_rsa.pub"; // leave empty for default location or enter absolute path to Apache user's ssh public key 