<?php

/**
 * @package ezcast.ezmanager.cli
 */

include_once __DIR__.'/config.inc';
include_once __DIR__.'/lib_ezmam.php';

Logger::$print_logs = true;

/*
 * This program downloads the cam slide movies and metadata of a recording from 2 minis in a lecture room
 * After downloading, the program calls $recorder_mam_insert_pgm for rendering
 */
if ($argc != 2) {
    echo "usage: " . $argv[0] . " <relative_directory_of_recording>\n Where <directory_of_recording> should point to a directory containing download_data.xml description file (relative to $recorder_upload_dir)\n";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, __FILE__ ." called with wrong arg count ($argc). argv: " .json_encode($argv), array(basename(__FILE__)));
    exit(1);
}

$recording_dir = $argv[1]; //first (and only) parameter in command line
$asset = $recording_dir;

$logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::DEBUG, __FILE__ . " called", array(basename(__FILE__)), $asset);
    
$destrecording_path = $recorder_upload_dir . "/" . $recording_dir;
//did the web application do a good job of creating directory and download (meta)datas?
if (!file_exists($destrecording_path)) {
    //no
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Given folder ($destrecording_path) does not exists", array(basename(__FILE__)), $asset);
    exit(2);
}

//local log file:
$local_log_file = $destrecording_path . "/download.log";
$local_pid_file = $destrecording_path . "/download.pid";
file_put_contents($local_pid_file, getmypid());

//now get download data to start downloading
$download_meta_file = $destrecording_path . "/download_data.xml";
$download_meta = metadata2assoc_array($download_meta_file);
if ($download_meta === false) {
    $error = "Bad xml or read problem on  $destrecording_path" . "/download_data.xml";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "$error", array(basename(__FILE__)));
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

if ($caller_ip == "") {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Caller IP not present in metadata ($download_meta_file)", array(basename(__FILE__)), $asset);
    exit(4);
}

$cam_download_info = null;
$slide_download_info = null;

foreach ($download_meta as $key => $value) {
    if (substr($key, 0, 4) == 'cam_') {
        $key = substr($key, 4);
        $cam_download_info[$key] = $value;
    } elseif (substr($key, 0, 6) == 'slide_') {
        $key = substr($key, 6);
        $slide_download_info[$key] = $value;
    }
}

$podcv_ip = null;
$podcs_ip = null;
if (isset($download_meta['cam_ip'])) {
    $podcv_ip = $download_meta['cam_ip'];
}
if (isset($download_meta['slide_ip'])) {
    $podcs_ip = $download_meta['slide_ip'];
}

var_dump($download_meta);
if ($cam_download_info) {
    var_dump($cam_download_info);
}
if ($slide_download_info) {
    var_dump($slide_download_info);
}

var_dump($recorder_version);

$asset = $record_date . "_" . $course_name;
//download metadata at all times
$meta_ok = true;
$cam_ok = true;
$slide_ok = true;
$repeat = $max_download_retries; //$max_download_retries is a global var

$logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::DEBUG, __FILE__ . " Started download", array(basename(__FILE__)), $asset);

//loop until all downloads are completed or timeout
do {
    switch ($recorder_version) {
        case "2.0":
        default:
        {
            //to improve: do not re download already downloaded files on retry. Rsync fait ça ?
            //todo: validate that user with netid from metadata has access to album
            //todo: check return value from rsync, do not retry in case of file not found
            
            // download metadata
            $res = rsync_fetch_record($caller_ip, $meta_file, $recorder_user, $destrecording_path);
            $meta_ok = $res == 0;
            
            //download cam movie if available/needed
            if ($record_type == "cam" || $record_type == "camslide") {
                $res = fetch_record_from($cam_download_info, $destrecording_path, "cam");
                $cam_ok = !$res;
            }//endif cam
            //download slide movie if available/needed
            if ($record_type == "slide" || $record_type == "camslide") {
                $res = fetch_record_from($slide_download_info, $destrecording_path, "slide");
                $slide_ok = !$res;
            }//endif slide

            $repeat -= 1;

            if (!$meta_ok || !$cam_ok || !$slide_ok) {
                $sleep_time = 600;
                $title = "Error downloading asset $asset from recorder (retrying)";
                $first_try = $repeat == $max_download_retries - 1;
                if ($first_try) {
                    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, "First try rsync failed (meta ok: $meta_ok, cam ok: $cam_ok, slide ok: $slide_ok). Will try again in $sleep_time seconds.", array(basename(__FILE__)), $asset);
                    if (!$meta_ok) {
                        mail($mailto_alert, $title, "Could not rsync file metadata from $podcv_ip (first try)");
                    }
                    if (!$cam_ok) {
                        mail($mailto_alert, $title, "Could not rsync file cam.mov from $podcv_ip (first try)");
                    }
                    if (!$slide_ok) {
                        mail($mailto_alert, $title, "Could not rsync file slide.mov from $podcs_ip (first try)");
                    }
                } else {
                    //$logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, "Rsync failed (meta ok: $meta_ok, cam ok: $cam_ok, slide ok: $slide_ok).", array(basename(__FILE__)), $asset);
                    if (!$meta_ok) {
                        echo "could not rsync file metadata from $podcv_ip";
                    }
                    if (!$cam_ok) {
                        echo "could not rsync file cam.mov from $podcv_ip";
                    }
                    if (!$slide_ok) {
                        echo "could not rsync file slide.mov from $podcs_ip";
                    }
                }
                sleep($sleep_time);
            }
        }
    }
} while ((!$meta_ok || !$cam_ok || !$slide_ok) && $repeat > 0);

if (!$meta_ok || !$cam_ok || !$slide_ok) {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Record download failed after $max_download_retries retries. Sending alert mail.", array(basename(__FILE__)), $asset);

    $title = "FINAL Error downloading asset $asset from recorder";
    if (!$meta_ok) {
        mail($mailto_alert, $title, "could not rsync file metadata from $podcv_ip");
    }
    if (!$cam_ok) {
        mail($mailto_alert, $title, "could not rsync file cam.mov from $podcv_ip");
    }
    if (!$slide_ok) {
        mail($mailto_alert, $title, "could not rsync file slide.mov from $podcs_ip");
    }

    //Move  recording directory to the failed downloads folder
    rename($destrecording_path, $recorder_upload_failed_dir . "/" . $recording_dir);
}//endif !download ok
else {
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::NOTICE, "Asset download successfully finished", array(basename(__FILE__)), $asset);

    // Finalize recording on the remote recorder
    $cmd = "$ssh_pgm -oBatchMode=yes $recorder_user@$caller_ip \"$recorder_php_cli $recorder_basedir/cli_upload_finished.php $asset\"";
    $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::DEBUG, "Calling recorder for assset finalization: $cmd", array(basename(__FILE__)), $asset);
    $output = system($cmd, $return_val);
    if ($return_val != 0) {
        $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Failed asset finalization on the recorder. Not critical, but files will be left over on the recorder. Command: $cmd. Output: $output", array(basename(__FILE__)), $asset);
    }

    //Move server's recording directory to the downloaded folder
    $record_ok_asset_dir = $recorder_upload_ok_dir . "/" . $recording_dir;
    rename($destrecording_path, $record_ok_asset_dir);

    //now we need to create an asset in an album and add the 2 originals and metadata
    // then ask to process the 2 medias into a low & high .mov files
    $cmd = "$php_cli_cmd $recorder_mam_insert_pgm $record_ok_asset_dir >> $record_ok_asset_dir/maminsert.log 2>&1";
    system($cmd, $return_val);
    if ($return_val != 0) {
        $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::ERROR, "Call to $recorder_mam_insert_pgm failed with error $return_val. Command $cmd.", array(basename(__FILE__)), $asset);
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
function fetch_record_from($download_info_array, $dest_dir, $camslide)
{
    global $dir_date_format;
    global $asset;
    global $logger;

    $download_protocol = $download_info_array['protocol'];
    $src_file = $download_info_array['filename'];

    print date($dir_date_format) . "\n";
    switch ($download_protocol) {
        case "rsync":
            $remote_ip = $download_info_array['ip'];
            $remote_username = $download_info_array['username'];
            if (!isset($remote_ip) || $remote_ip == '' || !isset($remote_username) || $remote_username == '') {
                $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Error : missing required param : rsync -e ssh -tv --partial-dir=<DIR> <REMOTE_USERNAME>@<REMOTE_IP>:<FILENAME> <DEST_DIR>", array(basename(__FILE__)), $asset);
                exit(5);
            }
            return rsync_fetch_record($remote_ip, $src_file, $remote_username, $dest_dir, $camslide);
        default:
            $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::CRITICAL, "Unrecognized protocol $download_protocol", array(basename(__FILE__)), $asset);
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
function rsync_fetch_record($ip, $source_filename, $remote_username, $dest_dir, $camslide = "")
{
    global $rsync_pgm;
    global $ssh_pgm;
    global $logger;

    $ext = pathinfo($source_filename, PATHINFO_EXTENSION);
    if ($camslide != "" && basename($source_filename) != "$camslide.mov") {
        $dest_dir .= "/$camslide.$ext";
    }

    $ssh_options = "";
    if (PHP_OS != "SunOS") {
        $ssh_options = "-o 'BatchMode yes'";
    }
    $cmd = "$rsync_pgm -e \"$ssh_pgm -o StrictHostKeyChecking=no $ssh_options\" -tv  --partial-dir=$dest_dir/downloading/ $remote_username@$ip:$source_filename $dest_dir 2>&1";
    echo $cmd;
    $returncode = 0;
    exec($cmd, $cmdoutput, $returncode);
    if ($returncode != 0) {
        static $first_time = true;
        if ($first_time) {
            $logger->log(EventType::MANAGER_UPLOAD_TO_EZCAST, LogLevel::WARNING, "Rsync fetch failed with return val '$returncode' for command: $cmd ||| Output: " . var_dump($cmdoutput, true), array(__FUNCTION__));
        }
        $first_time = false;
    }
    print "rsync cmd: $cmd\n";
    print "rsync done returncode:$returncode stdout&stderr: " . join("\n", $cmdoutput) . "\n";
    return $returncode;
}
