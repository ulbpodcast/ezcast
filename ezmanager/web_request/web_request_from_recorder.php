<?php

/**
 * @package ezcast.ezmanager.download
 */
//This program receives queries from classroom recording agents to tell that a new recorging is ready for download
//IP adresses of machines allowed to submit are stored in a file
require_once __DIR__."/../config.inc";
require_once __DIR__."/../lib_ezmam.php";
require_once "web_request.php";
require_once __DIR__."/../../commons/lib_external_stream_daemon.php";

if(!is_authorized_caller()) {
    print "not talking to you ($caller_ip)";
    die;
}

global $service;
$service = true;

$input = array_merge($_GET, $_POST);

$action = $input['action'];

//$logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::DEBUG, basename(__FILE__) . " called with action $action. Caller: $caller_ip", array("web_request_from_recorder"));
    
switch ($action) {
    case 'download' :
        download_from_recorder();
        break;
    case 'streaming_init':
        $ok = streaming_init();
        if($ok) 
            echo "OK";
        else
             http_response_code(500);
        break;
    case 'streaming_start':
        streaming_start();
        break;
    case 'streaming_content_add':
        streaming_content_add();
        break;
    case 'streaming_stop':
        streaming_stop();
        break;
    case 'streaming_close':
        streaming_close();
        break;
}

/**
 * Prepare data from download from recorder, then call download records (using cli_recorder_download)
 * recorder
 * @global type $input
 * @global type $ssh_pgm
 * @global type $recorder_user
 * @global type $caller_ip
 * @global type $dir_date_format
 * @global type $recorder_upload_dir
 * @global type $podcv_ip
 * @global type $podcs_ip
 * @global type $php_cli_cmd
 * @global type $recorder_download_pgm
 */
function download_from_recorder() {
    global $input;
    global $ssh_pgm;
    global $recorder_user;
    global $caller_ip;
    global $dir_date_format;
    global $recorder_upload_dir;
    global $podcv_ip;
    global $podcs_ip;
    global $php_cli_cmd;
    global $recorder_download_pgm;
    global $logger;
    
            
    //get input parameters
    $record_type = $input['record_type']; // cam|slide|camslide
    $record_date = $input['record_date'];
    $course_name = $input['course_name'];
    $meta_file = $input['metadata_file'];
    $recorder_php_cli = $input['php_cli']; //if empty, we can still recover by using 'php' as default cli command line
    $recorder_version = $input['recorder_version']; //if empty, we can still recover by using default protocol version
    
    if(!$record_type || !$record_date || !$course_name || !$meta_file) {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::CRITICAL, __FILE__ . " called invalid input (missing parameters). input dump:" . json_encode($input), array(__FUNCTION__));
        exit(1);
    }
    
    if (!isset($recorder_php_cli) || $recorder_php_cli == '') {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::WARNING, "Recorder did not provide its php_cli, trying to get it (or default to 'php')", array(__FUNCTION__));

        $cmd = "$ssh_pgm -o BatchMode=yes $recorder_user@$caller_ip \"which php\"";
        $recorder_php_cli = exec($cmd);
        if ($recorder_php_cli == '') {
            $recorder_php_cli = "php";
        }
    }

    // get info for file downloading
    $cam_info = null;
    $slide_info = null;
    if(isset($input['cam_info']))
        $cam_info = unserialize($input['cam_info']);
    if(isset($input['slide_info']))
        $slide_info = unserialize($input['slide_info']);

    if(!$cam_info && !$slide_info) {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::CRITICAL, "Neither cam_info or slide_info provided, nothing we can do.", array(__FUNCTION__));
        exit(2);
    }
    
    $download_info_xml = "";
    if($cam_info) {
        foreach ($cam_info as $key => $value) {
            $download_info_xml .= "<cam_$key>$value</cam_$key>" . PHP_EOL;
        }
    }
    if($slide_info) {
        foreach ($slide_info as $key => $value) {
            $download_info_xml .= "<slide_$key>$value</slide_$key>" . PHP_EOL;
        }
    }
    
    if(    (strpos($record_type, "cam" != false) && strpos($download_info_xml, "cam_protocol") == false) 
        || (strpos($record_type, "slide" != false) && strpos($download_info_xml, "slide_protocol") == false) 
        ) {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::CRITICAL, "Download data has record type $record_type but cam or slide infos are missing.", array(__FUNCTION__));
        exit(3);
    }

    $record_name_sanitized = str_to_safedir($record_date . "_" . $course_name);
    $request_date = date($dir_date_format);
    //creates a directory that will contain slide, camera and record metadata
    $record_dir = $recorder_upload_dir . "/" . $record_name_sanitized;
    if (!file_exists($record_dir))
        mkdir($record_dir);

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
            <recorder_php_cli>$recorder_php_cli</recorder_php_cli>
            $download_info_xml
        </download_metadata>
        ";

    $ok = file_put_contents($record_dir . "/download_data.xml", $downloadxml);
    if(!$ok) {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::CRITICAL, "Could not write download_data.xml file to $record_dir, can't recover.", array(__FUNCTION__), $record_name_sanitized);
        exit(4);
    }
    
    //start download in background. Can't check return value with at, replace by background process ?
    $cmd = "echo '$php_cli_cmd $recorder_download_pgm $record_name_sanitized >> $record_dir/download.log 2>&1 '| at now";
    $pid = system($cmd, $return_val);
    if($return_val != 0) {
        $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::CRITICAL, "Received valid download request, but download failed to start. Cmd: $cmd", array(__FUNCTION__), $record_name_sanitized);
        exit(1);
    }
//print "will execute command: '$cmd'\n<br>";
    print "OK:$pid";
    
    $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::DEBUG, "Received valid download request, download has been succesfully started in background. Cmd: $cmd", array(__FUNCTION__), $record_name_sanitized);
            
    exit(0);
}

/**
 * Inits the streaming on EZmanager
 * @global type $input
 * @global type $caller_ip
 * @global type $ezmanager_basedir
 * @global type $repository_path
 */
function streaming_init() {
    global $input;
    global $caller_ip;
    global $ezmanager_basedir;
    global $repository_path;
    
    global $logger;
    
    ezmam_repository_path($repository_path);

    $course = $input['course']; // current $course
    $asset = $input['asset']; // current asset
    $record_type = $input['record_type']; // camslide | cam | slide
    $module_type = $input['module_type']; // cam | slide
    $protocol = $input['protocol'];
    $classroom = $input['classroom'];
    $netid = $input['netid']; // user's netid
    //
    $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::INFO, "Received streaming init request for course $course, asset $asset, classroom $classroom, author $netid", array(__FUNCTION__));
    
    // gets information about current streams
    if (file_exists("$ezmanager_basedir/var/streams.php")) {
        $streams_array = require_once "$ezmanager_basedir/var/streams.php";
    } else {
        $streams_array = array();
    }

    if (!isset($streams_array[$course][$asset])) {
        // creates a new entry in the streams array for the current stream
        $streams_array[$course][$asset]['classroom'] = $classroom;
        $streams_array[$course][$asset]['netid'] = $netid;
        // at first, we consider the record_type is the module_type 
        // (ex: record_type = camslide / module_type = slide)
        // This way, if the cam module is not set for streaming, EZplayer 
        // knows that the streaming video is of type slide only
        $streams_array[$course][$asset]['record_type'] = $module_type;
        $streams_array[$course][$asset]['stream_name'] = sprintf('3000_%03d', count($streams_array[$course]));

        // prepares asset metadata
        $asset_meta = $streams_array[$course][$asset];
        $asset_meta['origin'] = 'streaming';
        $asset_meta['record_date'] = $asset;
        $asset_meta['status'] = 'open';
        $asset_meta['author'] = $input['author'];
        $asset_meta['title'] = $input['title'];

        // creates a new (streaming) asset in the public album
        $token = ezmam_asset_new($course . '-pub', $streams_array[$course][$asset]['stream_name'], $asset_meta);
        if($token == false) {
             $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::ERROR, "Failed to create asset.", array(__FUNCTION__));
             return false;
        }
        
        $streams_array[$course][$asset]['token'] = $token;
    }

    switch ($protocol) {
        case 'udp':
            // the streaming has already been init, return the info for the current asset
            if (isset($streams_array[$course][$asset][$module_type])) {
                $port = $streams_array[$course][$asset][$module_type]['port'];
                $server = $streams_array[$course][$asset][$module_type]['server'];
            } else {
                // we save information about the current stream and return server and port for EZrecorder
                $streams_array[$course][$asset][$module_type] = array();
                $streams_array[$course][$asset][$module_type]['ip'] = $caller_ip;
                $streams_array[$course][$asset][$module_type]['status'] = 'open';
                $streams_array[$course][$asset][$module_type]['protocol'] = $protocol;
                $streams_array[$course][$asset][$module_type]['server'] = server_get();
                $streams_array[$course][$asset][$module_type]['port'] = port_get();

                $server = $streams_array[$course][$asset][$module_type]['server'];
                $port = $streams_array[$course][$asset][$module_type]['port'];
            }

            $result = array("port" => $port, "server" => $server);
            print serialize($result);
            break;

        case 'http':
            if (!isset($streams_array[$course][$asset][$module_type])) {
                // we save information about the current stream 
                $streams_array[$course][$asset][$module_type] = array();
                $streams_array[$course][$asset][$module_type]['ip'] = $caller_ip;
                $streams_array[$course][$asset][$module_type]['status'] = 'open';
                $streams_array[$course][$asset][$module_type]['quality'] = $input['module_quality'];
                $streams_array[$course][$asset][$module_type]['protocol'] = $protocol;
            }
            $other_type = ($module_type == 'cam') ? 'slide' : 'cam';
            if (isset($streams_array[$course][$asset][$other_type])) {
                // record_type is camslide and we know that both modules
                // are set for streaming
                $streams_array[$course][$asset]['record_type'] = $record_type;
                $asset_meta = ezmam_asset_metadata_get($course . '-pub', $streams_array[$course][$asset]['stream_name']);
                $asset_meta['record_type'] = $record_type;
                ezmam_asset_metadata_set($course . '-pub', $streams_array[$course][$asset]['stream_name'], $asset_meta);
            }
            break;
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($streams_array, true) . ';';
    $string .= PHP_EOL . "?>";

    file_put_contents("$ezmanager_basedir/var/streams.php", $string);
    
    $logger->log(EventType::MANAGER_REQUEST_FROM_RECORDER, LogLevel::DEBUG, "Successfully processed init request for course $course, asset $asset, classroom $classroom, author $netid", array(__FUNCTION__));
    
    return true;
}

/**
 * Starts the live stream on EZmanager
 * @global type $input
 * @global type $ezmanager_basedir
 * @global type $repository_path
 * @return boolean
 */
function streaming_start() {
    global $input;
    global $ezmanager_basedir;
    global $repository_path;

    ezmam_repository_path($repository_path);

    $course = $input['$course'];
    $asset = $input['asset'];
    $protocol = $input['protocol'];
    $module_type = $input['module_type'];

    // gets information about current streams
    if (file_exists("$ezmanager_basedir/var/streams.php")) {
        $streams_array = require_once "$ezmanager_basedir/var/streams.php";
    } else {
        print 'error - streams array not found';
        return false;
    }

    $asset_meta = ezmam_asset_metadata_get($course . '-pub', $streams_array[$course][$asset]['stream_name']);
    $asset_meta['status'] = 'streaming';
    ezmam_asset_metadata_set($course . '-pub', $streams_array[$course][$asset]['stream_name'], $asset_meta);

    switch ($protocol) {
        case 'udp':
            // the streaming has already been init, return the info for the current asset
            if (isset($streams_array[$course][$asset][$module_type])) {
                $port = $streams_array[$course][$asset][$module_type]['port'];
                $server = $streams_array[$course][$asset][$module_type]['server'];

                // experimental: launches FFMPEG on remote EZrenderer to stream in EZmanager's webspace and saves the pid
                $cmd = "ssh ezrenderer@$server '/usr/local/sbin/ffmpeg -i udp://localhost:$port -threads 0 -s 960x540 -f hls -hls_time 3 -hls_list_size 0 -hls_wrap 5 -y /var/www/hls/video/demo.m3u8 </dev/null >/dev/null 2> /var/www/hls/video/ffmpeg.log & echo $! ' > $ezmanager_basedir/var/pid_${$course}_$asset & ";
                exec($cmd);
                $stream_pid = '';
                $count = 0;
                while ($stream_pid == '' && $count < 10) {
                    $stream_pid = file_get_contents("$ezmanager_basedir/var/pid_${$course}_$asset");
                    $count++;
                    sleep(1);
                }
                // saves the pid in the streams array
                $streams_array[$course][$asset][$module_type]['pid'] = $stream_pid;
                $streams_array[$course][$asset][$module_type]['status'] = 'streaming';
                unlink("$ezmanager_basedir/var/pid_${$course}_$asset");
            } else {
                // no information found for the stream
                print 'error - no information found for the current stream';
                return false;
            }

            print "OK: $stream_pid";
            break;
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($streams_array, true) . ';';
    $string .= PHP_EOL . "?>";

    file_put_contents("$ezmanager_basedir/var/streams.php", $string);
}

function create_m3u8_master($targetDir, $quality) {
   
    $master_m3u8 = '#EXTM3U' . PHP_EOL .
            '#EXT-X-VERSION:3' . PHP_EOL;
    
    // module_quality can be high | low | highlow (according to the module configuration file on EZrecorder)
    if (strpos($quality, 'low') !== false) {
        $master_m3u8 .= '#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=256000,CODECS="avc1.66.30,mp4a.40.2"' . PHP_EOL .
                'low/live.m3u8' . PHP_EOL;
    }
    if (strpos($quality, 'high') !== false) {
        $master_m3u8 .= '#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=1000000,CODECS="avc1.66.30,mp4a.40.2"' . PHP_EOL .
                'high/live.m3u8' . PHP_EOL;
    }

    file_put_contents($targetDir . 'live.m3u8', $master_m3u8);
}

function create_m3u8_external($targetDir, $type, $asset_token) {
    global $streaming_video_alternate_server_address;
    global $streaming_video_alternate_server_uri;
    
    $external_m3u8 = '#EXTM3U' . PHP_EOL .
            '#EXT-X-VERSION:3' . PHP_EOL . 
            '#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=256000,CODECS="avc1.66.30,mp4a.40.2"' . PHP_EOL .
           // 'http://' . $streaming_video_alternate_server_address . '/' . $streaming_video_alternate_server_uri . '/' . $asset_token . '/' .  $type . '/live.m3u8';
		//temp hack instead, redirect to the low
            'http://' . $streaming_video_alternate_server_address . '/' . $streaming_video_alternate_server_uri . '/' . $asset_token . '/' . $type . '/high/live.m3u8';
    
    file_put_contents($targetDir . '/external_live.m3u8', $external_m3u8);
}

/**
 * Add video contents to the current streaming.
 * This function is used for HTTP streaming and called each time a new
 * .ts segment is available on EZrecorder.
 * @global type $input
 * @global type $ezmanager_basedir
 * @global type $repository_path
 * @global type $apache_documentroot
 * @return boolean
 */
function streaming_content_add() {
    global $input;
    global $ezmanager_basedir;
    global $repository_path;
    global $apache_documentroot;
    global $streaming_video_alternate_server_enable_sync;
    global $streaming_video_alternate_server_enable_redirect;
    global $logger;
     
    ezmam_repository_path($repository_path);

    $course = $input['course'];
    $asset = $input['asset'];
    $protocol = $input['protocol'];
    $module_type = $input['module_type'];

    // gets information about current streams
    if (file_exists("$ezmanager_basedir/var/streams.php")) {
        $streams_array = require_once "$ezmanager_basedir/var/streams.php";
    } else {
        print 'error - streams array not found';
        return false;
    }

    $stream_name = $streams_array[$course][$asset]['stream_name'];

    switch ($protocol) {
        case 'http':
            /*
            $logger->log(EventType::MANAGER_STREAMING, LogLevel::DEBUG, print_r($input, true), array(__FUNCTION__));
            $logger->log(EventType::MANAGER_STREAMING, LogLevel::DEBUG, "$course : $asset : $module_type", array(__FUNCTION__));
              */  
            // the streaming has already been init, saves the m3u8 file and segments
            if (!isset($streams_array[$course][$asset][$module_type])) {
                 // no information found for the stream
                print 'error - no information found for the current stream';
                return false;
            }

	    $asset_token = $streams_array[$course][$asset]['token'];
            $streams_array[$course][$asset][$module_type]['status'] = $input['status'];
            $upload_root_dir = $apache_documentroot . '/ezplayer/videos/' . $course . '/' . $stream_name . '_' . $asset_token . '/';
            if(!is_file($upload_root_dir))
                mkdir($upload_root_dir, 0755, true); // creates the directories if needed

            if($streaming_video_alternate_server_enable_sync)
            {
               ExternalStreamDaemon::pause($asset_token);
               ensure_external_stream_daemon_is_running($upload_root_dir, $asset_token);
            }

            $upload_type_dir = $upload_root_dir . $input['module_type'] . '/';

            if(!is_file($upload_type_dir))
                mkdir($upload_type_dir, 0755, true); // creates the directories if needed

            // master playlist file doesn't exist yet
            if (!is_file($upload_type_dir . 'live.m3u8')) {
                create_m3u8_master($upload_type_dir, $streams_array[$course][$asset][$module_type]['quality']);
            }
            //also create external source if needed
            if($streaming_video_alternate_server_enable_redirect) {
                if (!is_file($upload_type_dir . 'external_live.m3u8')) {
                    create_m3u8_external($upload_type_dir, $input['module_type'], $asset_token);
                }
            } else { //else make sure it's removed
                if(is_file($upload_type_dir . 'external_live.m3u8'))
                    unlink($upload_type_dir . 'external_live.m3u8');    
            }

            $upload_quality_dir = $upload_type_dir . $input['quality'] . '/';
            // for instance : /www2/htdocs/dev/ezplayer/videos/ALBUM-NAME/3000_001/cam/high/
            if(!is_file($upload_quality_dir))
                mkdir($upload_quality_dir, 0755, true); // creates the directories if needed

            $uploadfile = $upload_quality_dir . $input['filename'];
            // places the file (.ts segment from HTTP request) in the webspace
            if (move_uploaded_file($_FILES['m3u8_segment']['tmp_name'], $uploadfile)) {
                echo "File is valid, and was successfully uploaded.\n";
            }

            // appends the m3u8 file
            if (!is_file("$upload_quality_dir/live.m3u8")) {
                $m3u8_header = explode(PHP_EOL, $input['m3u8_string']);
                // array_splice($m3u8_header, 4, 0, array('#EXT-X-PLAYLIST-TYPE:EVENT'));
                // Adds an extra line in m3u8 header: #EXT-X-PLAYLIST-TYPE:EVENT
                // This type of playlist allows the players to navigate freely (backward and forward) from the beginning of the program
                file_put_contents("$upload_quality_dir/live.m3u8", implode(PHP_EOL, $m3u8_header));
            } else {
                file_put_contents("$upload_quality_dir/live.m3u8", $input['m3u8_string'], FILE_APPEND);
            }
            
            if($streaming_video_alternate_server_enable_sync)
                ExternalStreamDaemon::resume($asset_token);
            
            print "OK";
            break;
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($streams_array, true) . ';';
    $string .= PHP_EOL . "?>";

    file_put_contents("$ezmanager_basedir/var/streams.php", $string);
}

/**
 * Stops the streaming on EZmanager
 * @global type $input
 * @global type $ezmanager_basedir
 * @global type $repository_path
 * @return boolean
 */
function streaming_stop() {
    global $input;
    global $ezmanager_basedir;
    global $repository_path;

    //stop external stream daemon if it's enabled
    ExternalStreamDaemon::stop();
    
    ezmam_repository_path($repository_path);

    $course = $input['$course'];
    $asset = $input['asset'];
    $protocol = $input['protocol'];
    $module_type = $input['module_type'];

    // gets information about current streams
    if (file_exists("$ezmanager_basedir/var/streams.php")) {
        $streams_array = require_once "$ezmanager_basedir/var/streams.php";
    } else {
        print 'error - streams array not found';
        return false;
    }

    $asset_meta = ezmam_asset_metadata_get($course . '-pub', $streams_array[$course][$asset]['stream_name']);
    $asset_meta['status'] = 'stopped';
    ezmam_asset_metadata_set($course . '-pub', $streams_array[$course][$asset]['stream_name'], $asset_meta);

    switch ($protocol) {
        case 'udp':
            // the streaming has already been init, return the info for the current asset
            if (isset($streams_array[$course][$asset][$module_type])) {
                $server = $streams_array[$course][$asset][$module_type]['server'];
                $pid = $streams_array[$course][$asset][$module_type]['pid'];
                // kills the ffmpeg process on EZrenderer
                $cmd = "ssh ezrenderer@$server 'kill $pid' &";
                exec($cmd);
                // deletes the HLS video files in the webspace
                system("rm -rf /www2/htdocs/dev/ezplayer/videos/" . $streams_array[$course][$asset]['stream_name']);
                $streams_array[$course][$asset][$module_type]['pid'] = '';
                $streams_array[$course][$asset][$module_type]['status'] = 'stopped';
            } else {
                // no information found for the stream
                print 'error - no information found for the current stream';
                return false;
            }

            print "OK";
            break;
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($streams_array, true) . ';';
    $string .= PHP_EOL . "?>";

    file_put_contents("$ezmanager_basedir/var/streams.php", $string);
}

/**
 * Closes the streaming on EZmanager and removes entries from the streams array
 * @global type $input
 * @global type $ezmanager_basedir
 * @global type $repository_path
 * @global type $apache_documentroot
 * @return boolean
 */
function streaming_close() {
    global $input;
    global $ezmanager_basedir;
    global $repository_path;
    global $php_cli_cmd;
    global $streaming_asset_delete_pgm;

    ezmam_repository_path($repository_path);

    $course = $input['$course'];
    $asset = $input['asset'];
    $protocol = $input['protocol'];
    $module_type = $input['module_type'];

    // gets information about current streams
    if (file_exists("$ezmanager_basedir/var/streams.php")) {
        $streams_array = require_once "$ezmanager_basedir/var/streams.php";
    } else {
        print 'error - streams array not found';
        return false;
    }

    switch ($protocol) {
        case 'udp':
            // removes the stream from the streams files
            if (isset($streams_array[$course][$asset][$module_type])) {
                $server = $streams_array[$course][$asset][$module_type]['server'];
                $status = $streams_array[$course][$asset][$module_type]['status'];
                if ($status === 'streaming') {
                    $pid = $streams_array[$course][$asset][$module_type]['pid'];
                    $cmd = "ssh ezrenderer@$server 'kill $pid' &";
                    exec($cmd);
                }
                $cmd = "ssh ezrenderer@$server 'rm -f /var/www/hls/video/demo*' &";
                exec($cmd);
                system("rm -rf /www2/htdocs/dev/ezplayer/videos/" . $streams_array[$course][$asset]['stream_name']);

                ezmam_asset_delete($course . '-pub', $streams_array[$course][$asset]['stream_name']);
                if ($streams_array[$course][$asset]['record_type'] == 'camslide') {
                    $other_type = ($module_type == 'cam') ? 'slide' : 'cam';
                    if (isset($streams_array[$course][$asset][$other_type])) {
                        unset($streams_array[$course][$asset][$module_type]);
                    } else {
                        // other module has already been closed
                        unset($streams_array[$course][$asset]);
                    }
                } else {
                    unset($streams_array[$course][$asset]);
                }
                if (count($streams_array[$course]) == 0) {
                    unset($streams_array[$course]);
                }
            } else {
                // no information found for the stream
                print 'error - no information found for the current stream';
                return false;
            }

            print "OK";
            break;

        case 'http':
            // removes the stream from the streams files
            if (isset($streams_array[$course][$asset])) {

                $stream_name = $streams_array[$course][$asset]['stream_name'];
                $token = $streams_array[$course][$asset]['token'];

                ezmam_asset_delete($course . '-pub', $stream_name);
                unset($streams_array[$course][$asset]);
                if (count($streams_array[$course]) == 0) {
                    unset($streams_array[$course]);
                }

                $cmd = "echo \"$php_cli_cmd $streaming_asset_delete_pgm $course ${stream_name}_$token\" | at now + 1 hour";
                $pid = shell_exec($cmd);
            }

            print "OK";
            break;
    }

    $string = "<?php" . PHP_EOL . "return ";
    $string .= var_export($streams_array, true) . ';';
    $string .= PHP_EOL . "?>";

    file_put_contents("$ezmanager_basedir/var/streams.php", $string);
}

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

function port_get() {
    return "5000";
}

function server_get() {
    return "164.15.128.144";
}
