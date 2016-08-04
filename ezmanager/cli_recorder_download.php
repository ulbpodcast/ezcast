<?php

/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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

/**
 * @package ezcast.ezmanager.cli
 */

include_once 'config.inc';
include_once 'lib_ezmam.php';

Logger::$print_logs = true;

/*
 * This program downloads the cam  slide movies and metadata of a recording from 2 minis in a lecture room
 * After downloading, the program calls $recorder_mam_insert_pgm for rendering
 */
if ($argc != 2) {
    echo "usage: " . $argv[0] . " <directory_of_recording>\n Where <directory_of_recording> should point to a directory containing download_data.xml description file (relative to $recorder_upload_dir)\n";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, __FILE__ ." called with wrong arg count ($argc). argv: " .json_encode($argv), array("cli_recorder_download"));
    exit(1);
}

$logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::DEBUG, __FILE__ . " called", array("cli_recorder_download"));
    
$recording_dir = $argv[1]; //first (and only) parameter in command line
$asset = $recording_dir;

$destrecording_path = $recorder_upload_dir . "/" . $recording_dir;
//did the web application do a good job of creating directory and download (meta)datas?
if (!file_exists($destrecording_path)) {
    //no
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Given folder ($$destrecording_path) does not exists", array("cli_recorder_download"), $asset);
    exit(2);
}

//local log file:
$local_log_file = $destrecording_path . "/download.log";

//now get download data to start downloading
$download_meta_file = $destrecording_path . "/download_data.xml";
$download_meta = metadata2assoc_array($download_meta_file);
if ($download_meta === false) {
    $error = "Bad xml or read problem on  $destrecording_path" . "/download_data.xml";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "$error", array("cli_recorder_download"));
    exit(3);
}

$request_date = $download_meta['request_date'];
$course_name = $download_meta['course_name'];
$record_date = $download_meta['record_date'];
$download_complete = $download_meta['download_complete'];
$record_type = $download_meta['record_type'];
$recorder_version = $download_meta['recorder_version'];
$recorder_php_cli = $download_meta['recorder_php_cli'];

$caller_ip = $download_meta['caller_ip']; // Caller contains the metadata
$meta_file = $download_meta['metadata_file'];

if($caller_ip == "") {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Caller IP not present in metadata ($download_meta_file)", array("cli_recorder_download"), $asset);
    exit(4);
}

$cam_download_info = null;
$slide_download_info = null;

foreach ($download_meta as $key => $value) {
    if (substr($key, 0, 4) == 'cam_') {
        $key = substr($key, 4);
        $cam_download_info[$key] = $value;
    } else if (substr($key, 0, 6) == 'slide_') {
        $key = substr($key, 6);
        $slide_download_info[$key] = $value;
    }
}

$podcv_ip = null;
$podcs_ip = null;
if(isset($download_meta['cam_ip']))
    $podcv_ip = $download_meta['cam_ip'];
if(isset($download_meta['slide_ip']))
    $podcs_ip = $download_meta['slide_ip'];

var_dump($download_meta);
if($cam_download_info)
    var_dump($cam_download_info);
if($slide_download_info)
    var_dump($slide_download_info);

var_dump($recorder_version);

$asset = $record_date . "_" . $course_name;
$download_dir = $recorder_upload_to_server . "/" . $asset;
//download metadata at all times
$meta_ok = true;
$cam_ok = true;
$slide_ok = true;
$repeat = $max_download_retries; //$max_download_retries is a global var

//loop until all downloads are completed or timeout
do {
    switch($recorder_version) {
        case "2.0":
        default:
        {
            // download metadata
            $res = rsync_fetch_record($caller_ip, $meta_file, $recorder_user, $destrecording_path);
            $meta_ok = !$res;

            //download cam movie if available/needed
            if ($record_type == "cam" || $record_type == "camslide") {
                $res = fetch_record_from_v2($cam_download_info, $destrecording_path, "cam");
                $cam_ok = !$res;
            }//endif cam
            //download slide movie if available/needed
            if ($record_type == "slide" || $record_type == "camslide") {
                $res = fetch_record_from_v2($slide_download_info, $destrecording_path, "slide");
                $slide_ok = !$res;
            }//endif slide

            $repeat -= 1;

            if (!$meta_ok || !$cam_ok || !$slide_ok) {
                $title = "Error downloading from recorder (retrying)";
                $first_try = $repeat == $max_download_retries - 1;
                if ($first_try) {
                    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, "First try rsync failed ($meta_ok, $cam_ok, $slide_ok) ", array("cli_recorder_download"), $asset);
                    if (!$meta_ok) 
                        mail($mailto_alert, $title, "Could not rsync file metadata from $podcv_ip (first try)");
                    if (!$cam_ok)
                        mail($mailto_alert, $title, "Could not rsync file cam.mov from $podcv_ip (first try)");
                    if (!$slide_ok)
                        mail($mailto_alert, $title, "Could not rsync file slide.mov from $podcs_ip (first try)");
                } else {
                    if (!$meta_ok)
                        echo "could not rsync file metadata from $podcv_ip";
                    if (!$cam_ok)
                        echo "could not rsync file cam.mov from $podcv_ip";
                    if (!$slide_ok)
                        echo "could not rsync file slide.mov from $podcs_ip";
                }
                sleep(600);
            }
        }
    }
} while ((!$meta_ok || !$cam_ok || !$slide_ok ) && $repeat > 0);

if (!$meta_ok || !$cam_ok || !$slide_ok) {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Record download failed after $max_download_retries retries. Sending alert mail.", array("cli_recorder_download"), $asset);

    $title = "FINAL Error downloading from recorder";
    if (!$meta_ok)
        mail($mailto_alert, $title, "could not rsync file metadata from $podcv_ip");
    if (!$cam_ok)
        mail($mailto_alert, $title, "could not rsync file cam.mov from $podcv_ip");
    if (!$slide_ok)
        mail($mailto_alert, $title, "could not rsync file slide.mov from $podcs_ip");

    //Move  recording directory to the failed downloads folder
    rename($destrecording_path, $recorder_upload_failed_dir . "/" . $recording_dir);
}//endif !download ok
else {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::NOTICE, "Asset download succesfully finished", array("cli_recorder_download"), $asset);

    // Finalize recording on the remote recorder
    $cmd = "$ssh_pgm -oBatchMode=yes $recorder_user@$caller_ip \"$recorder_php_cli $recorder_basedir/cli_upload_finished.php $asset\"";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::DEBUG, "Calling recorder for assset finalization: $cmd", array("cli_recorder_download"), $asset);
    $output = system($cmd, $return_val);
    if($return_val != 0) {
        $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Failed asset finalization on the recorder. Not critical, but files will be left over on the recorder. Command: $cmd. Output: $output", array("cli_recorder_download"), $asset);
    }

    //Move server's recording directory to the downloaded folder
    $record_ok_asset_dir = $recorder_upload_ok_dir . "/" . $recording_dir;
    rename($destrecording_path, $record_ok_asset_dir);

    //now we need to create an asset in an album and add the 2 originals and metadata
    // then ask to process the 2 medias into a low & high .mov files
    $cmd = "$php_cli_cmd $recorder_mam_insert_pgm $record_ok_asset_dir >> $record_ok_asset_dir/maminsert.log 2>&1";
    system($cmd, $return_val);
    if($return_val != 0) {
        $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Call to $recorder_mam_insert_pgm failed with error $return_val", array("cli_recorder_download"), $asset);
    }
}//end else

/**
 * tries to fetch a file from a remote recording agent using the downloading 
 * information given
 * @global type $dir_date_format
 * @param type $download_info_array an associative array containing all information
 * required to download the file from the remote agent
 * @param type $dest_dir the directory where the downloaded file should be saved
 * @return type
 */
function fetch_record_from_v2($download_info_array, $dest_dir, $camslide) {
    global $dir_date_format;
    global $asset;
    global $logger;

    $download_protocol = $download_info_array['protocol'];
    $src_file = $download_info_array['filename'];

    print date($dir_date_format) . "\n";
    switch ($download_protocol) {
        case "rsync" :
            $remote_ip = $download_info_array['ip'];
            $remote_username = $download_info_array['username'];
            if (!isset($remote_ip) || $remote_ip == '' || !isset($remote_username) || $remote_username == '') {
                $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Error : missing required param : rsync -e ssh -tv --partial-dir=<DIR> <REMOTE_USERNAME>@<REMOTE_IP>:<FILENAME> <DEST_DIR>", array("cli_recorder_download"), $asset);
                exit(5);
            }
            return rsync_fetch_record($remote_ip, $src_file, $remote_username, $dest_dir, $camslide);
        default :
            $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Unrecognized protocol $download_protocol", array("cli_recorder_download"), $asset);
            exit(6);
    }
    print date($dir_date_format) . "\n";
}

/**
 *
 * @param <ipaddress> $ip ip address of mini (where to download from)
 * @param <string> $source_filename absolute path to the file to download
 * @param <string> $remote_username
 * @param <path> $dest_dir
 * @return <bool> try to fetch a file from a mini recording agent using rsync
 *
 *
 */
function rsync_fetch_record($ip, $source_filename, $remote_username, $dest_dir, $camslide = "") {
    global $rsync_pgm;

    $ext = pathinfo($source_filename, PATHINFO_EXTENSION);
    if ($camslide != "" && basename($source_filename) != "$camslide.mov")
        $dest_dir .= "/$camslide.$ext";

    $cmd = "$rsync_pgm -e ssh -tv  --partial-dir=$dest_dir/downloading/ $remote_username@$ip:$source_filename $dest_dir 2>&1";
    exec($cmd, $cmdoutput, $returncode);
    print "rsync cmd: $cmd\n";
    print "rsync done returncode:$returncode stdout&stderr: " . join("\n", $cmdoutput) . "\n";
    return $returncode;
}
