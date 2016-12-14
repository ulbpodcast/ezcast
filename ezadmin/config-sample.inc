<?php

require_once "../commons/config.inc";

// General stuff
$appname="EZadmin";

$ezmanager_host = ''; // Leave blank if both ezmanager and ezadmin are on the same machine
$ezmanager_user = ''; // Only useful when ezmanager is on another machine than ezadmin
$ezmanager_subdir = '/'; // Path from the basedir to admin.inc
$ezplayer_subdir = '/'; // Path from the basedir to admin.inc

// User preferences
$recorders_category_enabled = false; // Set to false to hide the "recorders" category
$classrooms_category_enabled = false; // Set to false to hide the "classrooms" category
$add_users_enabled = true; // set to true to have a userlist in ezmanager
$recorder_password_storage_enabled = true; // Set to true to save passwords on the Mac Minis
$use_course_name = false; // Set to true to use course name instead of course code
$use_user_name = false; // Set to true to use user's full name instead of user ID

// Technical stuff
$template_folder = 'tmpl/';

// Renderers settings
$renderers_options = array(
    // <name> the name of the library to use (used in tmpl_sources/div_create_renderer_step3.php as class name to display input fields)
    // <description> the description shown in a combo list in tmpl_sources/div_create_renderer_step3.php
    // <file> file name of the library
        'ffmpeg' => array('name' => 'ffmpeg', 'description' => 'FFMPEG command line', 'file' => 'lib_ffmpeg.php'),
        'ffmpeg_exp' => array('name' => 'ffmpeg_exp', 'description' => 'FFMPEG command line experimental mode (AAC)', 'file' => 'lib_ffmpeg.php'),
);

//cannot be empty
$courses_excluded_from_stats = array(
    "PODC-I-000",
    "AUTO-TESTS",
);