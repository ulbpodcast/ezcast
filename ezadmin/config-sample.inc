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
$add_users_enabled = false; // set to true to have a userlist in ezmanager
$recorder_password_storage_enabled = false; // Set to true to save passwords on the recorders
$use_user_name = false; // Set to true to use user's full name instead of user ID
//Send mail informations about recorder
$adress_mail_information_recorder = "";

// Regular expression
$input_validation_regex = "/^[a-zA-Z0-9éè_\s-]+$/i"; //Check a string, it must contain letters, numbers and blank spaces

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

//control panel
/* 
	this URL is used to call the proxy which contains a header (image/JPEG) and which returns us an image. we have established this proxy for the problems of authentification involved in google chromium for the recovery of image.

	The camera display is divided into two parts: a page that displays all activated cameras and another page that displays a previously selected camera with different options (Zoom,...).

	When the proxy is called, different parameters are sent it to detect which page requests an image.
	 => div_controller_camera.php different parameters are room IP, room name and action_cam="displayImg"
			- Example of the url : 'https://example.be/ezadmin/cli_get_camera.php?room=ROOM_NAME&action_cam=displayImg&room_ip=x.x.x.x'
	 => div_display_camera.php different parameters are room IP, room name and action_cam="displayImgOptions"
	 		- Example of the url : 'https://example.be/ezadmin/cli_get_camera.php?room=ROOM_NAME&action_cam=displayImgOptions&room_ip=x.x.x.x'
		And when we click on an option, a parameter is added 'param ='
			- Example of the url : 'https://example.be/ezadmin/cli_get_camera.php?room=ROOM_NAME&action_cam=displayImgOptions&room_ip=x.x.x.x&param=PLUS'
				- param : 
							- left => The camera moves to the left
							- right => The camera moves to the right
							- top => The camera moves to the top
							- bottom => The camera moves to the bottom
							- plus => ZOOM + 1
							- minus => ZOOM - 1
*/

$path_url_proxy = ""; //url for the proxy => https://example.be/ezadmin/cli_get_camera.php
$enable_control_panel = false; // Enables the control panel by displaying all the cameras that are activated.
$enable_control_panel_options = false; // Enables the page that will display a previously selected camera with these options.
$ip_prefix = ""; // Contains username and password for connect remotely to camera
$ip_suffixe = ""; // Contains the port and different parameters of the cameraa