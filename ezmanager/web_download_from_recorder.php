<?php

/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	   Arnaud Wijns <awijns@ulb.ac.be>
 *         Antoine Dewilde
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//This program receives queries from classroom recording agents to tell that a new recorging is ready for download
//IP adresses of machines allowed to submit is store in a file
include_once "config.inc";
include_once "classroom_recorder_ip.inc"; //valid ip file
$input = array_merge($_GET, $_POST);
//look for caller's ip in config files
$caller_ip = trim($_SERVER["REMOTE_ADDR"]);

$key = array_search($caller_ip, $podcv_ip);
if ($key === false) {
    //ip not found
    print "not talking to you";
    die;
}
//get input parameters
$record_type = $input['record_type']; // cam|slide|camslide
$record_date = $input['record_date'];
$course_name = $input['course_name'];
$recorder_version = (isset($input['recorder_version']) && !empty($input['recorder_version'])) ? $input['recorder_version'] : "1.0";

// get the file that contains metadata relative to the recording
$meta_file = $input['metadata_file'];

// get info for file downloading
$cam_info = unserialize($input['cam_info']);
$slide_info = unserialize($input['slide_info']);


$download_info_xml = "";
foreach ($cam_info as $key => $value) {
    $download_info_xml .= "<cam_$key>$value</cam_$key>" . PHP_EOL;
}
foreach ($slide_info as $key => $value) {
    $download_info_xml .= "<slide_$key>$value</slide_$key>" . PHP_EOL;
}

$record_name_sanitized = str_to_safedir($record_date . "_" . $course_name);
$request_date = date($dir_date_format);
//creates a directory that will contain slide, camera and record metadata
$record_dir = $recorder_upload_dir . "/" . $record_name_sanitized;
if (!file_exists($record_dir))
    mkdir($record_dir);

// Bqckwards compatibility with v1
$additional_tags = "";
if($recorder_version == "1.0") {
    //look for caller's ip in config files
    $caller_ip=trim($_SERVER["REMOTE_ADDR"]);

    $key=array_search($caller_ip, $podcv_ip);
    if($key===false){
        //ip not found
        print "not talking to you";
        die;
    }
    //deduce podcs ip:
    $cur_podcs_ip=$podcs_ip[$key];
    $cur_podcv_ip=$caller_ip;
    
    $additional_tags .= "<cam_ip>$cur_podcv_ip</cam_ip>";
    $additional_tags .= "<slide_ip>$cur_podcs_ip</slide_ip>";
}

//now we need to call the recording download cli program (outside of web environment execution to avoid timeout
//this process will contact cam and slide modules and download video files and metadata
$downloadxml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone='yes'?>
<download_metadata>
<metadata_file>$meta_file</metadata_file>
<request_date>$request_date</request_date>
<course_name>$course_name</course_name>
<record_date>$record_date</record_date>
<download_complete>false</download_complete>
<record_type>$record_type</record_type>
<caller_ip>$caller_ip</caller_ip>
<recorder_version>$recorder_version</recorder_version>
$download_info_xml
$additional_tags
</download_metadata>
";

file_put_contents($record_dir . "/download_data.xml", $downloadxml);
$cmd = "$php_cli_cmd $recorder_download_pgm $record_name_sanitized >>$record_dir/download.log 2>&1 |at now";
$pid = shell_exec($cmd);
//print "will execute command: '$cmd'\n<br>";
print "OK:$pid";

/**
 *
 * @param string $string
 * @return string
 * @desc returns a directory safe
 */
function str_to_safedir($string) {
    $toalnum = "";
    for ($idx = 0; $idx < strlen($string); $idx++)
        if (ctype_alnum($string[$idx]) || $string[$idx] == "-")
            $toalnum.=$string[$idx];
        else
            $toalnum.="_";
    return $toalnum;
}

?>
