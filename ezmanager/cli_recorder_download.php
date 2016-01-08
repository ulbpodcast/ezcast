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

/**
 * @package ezcast.ezmanager.cli
 */

include_once 'config.inc';
include_once 'lib_ezmam.php';
/*
 * This program downloads the cam  slide movies and metadata of a recording from 2 minis in a lecture room
 * After downloading, the program calls cli_mam_insert.php for rendering
 */
if ($argc != 2) {
    echo "usage: " . $argv[0] . " <directory_of_recording>\n Where <directory_of_recording> should point to a directory containing download_data.xml description file (relative to $recorder_upload_dir)\n";
    die;
}


$recording_dir = $argv[1]; //first (and only) parameter in command line
$destrecording_path = $recorder_upload_dir . "/" . $recording_dir;
//did the web application do a good job of creating directory and download (meta)datas?
if (!file_exists($destrecording_path)) {
    //no
    myerror("could not find recording directory: $destrecording_path"); //log error
}
//local log file:
$local_log_file = $destrecording_path . "/download.log";

//now get download data to start downloading
$download_meta = metadata2assoc_array($destrecording_path . "/download_data.xml");
if ($download_meta === false)
    myerror("bad xml or read problem on  $destrecording_path" . "/download_data.xml"); //log to file

$request_date = $download_meta['request_date'];
$course_name = $download_meta['course_name'];
$record_date = $download_meta['record_date'];
$download_complete = $download_meta['download_complete'];
$record_type = $download_meta['record_type'];
$recorder_version = $download_meta['recorder_version'];
$recorder_php_cli = $download_meta['recorder_php_cli'];

$caller_ip = $download_meta['caller_ip']; // Caller contains the metadata
$meta_file = $download_meta['metadata_file'];

$cam_protocol = $download_meta['cam_protocol'];
$slide_protocol = $download_meta['slide_protocol'];

foreach ($download_meta as $key => $value) {
    if (substr($key, 0, 4) == 'cam_') {
        $key = substr($key, 4);
        $cam_download_info[$key] = $value;
    } else if (substr($key, 0, 6) == 'slide_') {
        $key = substr($key, 6);
        $slide_download_info[$key] = $value;
    }
}

$podcv_ip = $download_meta['cam_ip'];
$podcs_ip = $download_meta['slide_ip'];

var_dump($download_meta);
var_dump($cam_download_info);
var_dump($slide_download_info);
var_dump($recorder_version);

$download_dir = $recorder_upload_to_server . "/" . $record_date . "_" . $course_name;
//download metadata at all times
$meta_ok = true;
$cam_ok = true;
$slide_ok = true;
//$idx=$max_download_retries;
$repeat = $max_download_retries;

//loop until all downloads are completed or timeout
do {

    if ($recorder_version == "2.0") {
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

        $repeat-=1;

        if (!$meta_ok || !$cam_ok || !$slide_ok) {
            if ($repeat == $max_download_retries - 1) {
                if (!$meta_ok)
                    mail($mailto_alert, "Error downloading from recorder (retrying)", "could not rsync file metadata from $podcv_ip");
                if (!$cam_ok)
                    mail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file cam.mov from $podcv_ip");
                if (!$slide_ok)
                    mail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file slide.mov from $podcs_ip");
            } else {
                if (!$meta_ok)
                    sendmail($mailto_alert, "Error downloading from recorder (retrying)", "could not rsync file metadata from $podcv_ip");
                if (!$cam_ok)
                    sendmail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file cam.mov from $podcv_ip");
                if (!$slide_ok)
                    sendmail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file slide.mov from $podcs_ip");
            }
            sleep(600);
        }
    }
    else if ($recorder_version == "1.0") {

        // download metadata
        $res = fetch_record_from_v1($podcv_ip, "metadata.xml", $download_dir, $recorder_user, $destrecording_path);
        $meta_ok = !$res;

        //download cam movie if available/needed
        if ($record_type == "cam" || $record_type == "camslide") {
            $res = fetch_record_from_v1($podcv_ip, "cam.mov", $download_dir, $recorder_user, $destrecording_path);
            $cam_ok = !$res;
        }//endif cam
        //download slide movie if available/needed
        if ($record_type == "slide" || $record_type == "camslide") {
            $res = fetch_record_from_v1($podcs_ip, "slide.mov", $download_dir, $recorder_user, $destrecording_path);
            $slide_ok = !$res;
        }//endif slide

        $repeat-=1;
        if (!$meta_ok || !$cam_ok || !$slide_ok) {
            if ($repeat == $max_download_retries - 1) {
                if (!$meta_ok)
                    mail($mailto_alert, "Error downloading from recorder (retrying)", "could not rsync file metadata from $podcv_ip");
                if (!$cam_ok)
                    mail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file cam.mov from $podcv_ip");
                if (!$slide_ok)
                    mail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file slide.mov from $podcs_ip");
            } else {
                if (!$meta_ok)
                    sendmail($mailto_alert, "Error downloading from recorder (retrying)", "could not rsync file metadata from $podcv_ip");
                if (!$cam_ok)
                    sendmail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file cam.mov from $podcv_ip");
                if (!$slide_ok)
                    sendmail($mailto_alert, "Error downloading from recorder(retrying)", "could not rsync file slide.mov from $podcs_ip");
            }
            sleep(600);
        }
    }
} while ((!$meta_ok || !$cam_ok || !$slide_ok ) && $repeat > 0);

if (!$meta_ok || !$cam_ok || !$slide_ok) {
    if (!$meta_ok)
        mail($mailto_alert, "FINAL Error downloading from recorder", "could not rsync file metadata from $podcv_ip");
    if (!$cam_ok)
        mail($mailto_alert, "FINAL Error downloading from recorder", "could not rsync file cam.mov from $podcv_ip");
    if (!$slide_ok)
        mail($mailto_alert, "FINAL Error downloading from recorder", "could not rsync file slide.mov from $podcs_ip");

    //Move  recording directory to the failed downloads folder
    rename($destrecording_path, $recorder_upload_failed_dir . "/" . $recording_dir);
}//endif !download ok
else {
    // the download went well. We then finalize recording on the remote recorder
    $asset = $record_date . '_' . $course_name;
    $cmd = "$ssh_pgm $recorder_user@$caller_ip \"$recorder_php_cli $recorder_basedir/cli_upload_finished.php $asset\"";
    exec($cmd, $cmdoutput, $returncode);

    //Move server's recording directory to the downloaded folder
    rename($destrecording_path, $recorder_upload_ok_dir . "/" . $recording_dir);

    //now we need to create an asset in an album and add the 2 originals and metadata
    // then ask to process the 2 medias into a low & high .mov files
    $cmd = "$php_cli_cmd $recorder_mam_insert_pgm $recorder_upload_ok_dir/$recording_dir >>$recorder_upload_ok_dir/$recording_dir/maminsert.log 2>&1";
    $pid = shell_exec($cmd);
    print "shell_exec $cmd\npid=$pid\n";
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

    $download_protocol = $download_info_array['protocol'];
    $src_file = $download_info_array['filename'];

    print date($dir_date_format) . "\n";
    switch ($download_protocol) {
        case "rsync" :
            $remote_ip = $download_info_array['ip'];
            $remote_username = $download_info_array['username'];
            if (!isset($remote_ip) || $remote_ip == '' || !isset($remote_username) || $remote_username == '')
                myerror("Error : missing required param : 
                    rsync -e ssh -tv --partial-dir=<DIR> <REMOTE_USERNAME>@<REMOTE_IP>:<FILENAME> <DEST_DIR>");
            return rsync_fetch_record($remote_ip, $src_file, $remote_username, $dest_dir, $camslide);
            break;
        default :
            myerror("Can't handle that protocol");
            break;
    }
    print date($dir_date_format) . "\n";
}

/**
 *
 * @param <ipaddress> $ip ip address of mini (where to download from)
 * @param <string> $source_filename
 * @param <path> $source_path
 * @param <string> $remote_username
 * @param <path> $dest_dir
 * @return <bool> try to fetch a file from a mini recording agent
 *
 *
 */
function fetch_record_from_v1($ip, $source_filename, $source_path, $remote_username, $dest_dir) {
    global $rsync_pgm, $dir_date_format;

    print date($dir_date_format) . "\n";
    $cmd = "$rsync_pgm -e ssh -tv  --partial-dir=$dest_dir/downloading/ $remote_username@$ip:$source_path/$source_filename $dest_dir 2>&1";
    exec($cmd, $cmdoutput, $returncode);
    print date($dir_date_format) . "\n";
    print "rsync cmd: $cmd\n";
    print "rsync done returncode:$returncode stdout&stderr: " . join("\n", $cmdoutput) . "\n";
    return $returncode;
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

function myerror($msg) {
    print $msg . "\n";
    die;
}

/**
 *
 * @param <string> $to
 * @param <string> $subjet
 * @param <string> $msg
 * @desc Sendmail wrapper
 */
function sendmail($to, $subjet, $msg) {
    print "Download Error would send mail TO: $to SUBJECT:$subjet MESSAGE:$msg\n";
}

?>
