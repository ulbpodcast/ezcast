<?php

/**
 * @package ezcast.ezmanager.cli
 */

chdir(__DIR__);

include_once __DIR__.'/config.inc';
include_once __DIR__.'/lib_ezmam.php';
include_once __DIR__.'/lib_various.php';

Logger::$print_logs = true;

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

/*
 * This program handles the cam,slide movies and metadata of a classroom recording or user submit:
 * inserts them in the repository (via lib_ezmam)
 *   This means: asset creation, , add metadata
 * calls renderer to process the movies
 */
if ($argc!=2) {
    echo "usage: ".$argv[0].' <full_path_to_directory_of_downloaded_ok_recording>
        Where <directory_of_recording> should point to a directory containing metadata.xml description file and movies
        metadata.xml example:
        <?xml version="1.0" standalone="yes"?>
          <metadata>
          <course_name>COURSE-CODE</course_name>
          <origin>SUBMIT</origin>
          <submitted_filename>mymovie.mov</submitted_filename>
          <title>My movie title</title>
          <description>My movie description</description>
          <record_type>camslide</record_type>
          <moderation>false</moderation>
          <author>Author</author>
          <netid>user id</netid>
          <record_date>YYYY_MM_DD_hh\hmm\m</record_date>
         </metadata>
'   ;
    $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::WARNING, __FILE__ ." called with wrong argc count: $argc. argv: " . json_encode($argv), array("cli_mam_insert"));
    exit(1);
}

$recording_dir = $argv[1]; //first (and only) parameter in command line
$asset = basename($recording_dir);

if (!file_exists($recording_dir)) {
    $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "could not find recording directory: $recording_dir", array("cli_mam_insert"), $asset);
    exit(2);
}
//local log file:
$local_log_file=$recording_dir."/download.log";

//now get metadata about the recording
$recording_metadata = metadata2assoc_array($recording_dir."/metadata.xml");
if ($recording_metadata===false) {
    $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Bad xml or read problem on $recording_dir"."/metadata.xml", array("cli_mam_insert"), $asset);
    // 'course_name' 'origin' 'record_date' 'description' 'record_type' 'moderation'
    exit(3);
}

$record_type=$recording_metadata['record_type'];
$course_name=$recording_metadata['course_name'];
$record_date=$recording_metadata['record_date'];

//check for album existence
if ($recording_metadata['moderation']=="false") {
    $album_name = $course_name."-pub";
} else {
    $album_name = $course_name."-priv";
}

//create album if needed
if (!ezmam_album_exists($album_name)) {
    $courseinfo = db_course_read($course_name);
    if ($courseinfo == false) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Album $album_name did not exist and we did not find this course in db", array("cli_mam_insert"), $asset);
        exit(4);
    }
    if (isset($courseinfo['course_code_public']) && $courseinfo['course_code_public'] != '') {
        $course_code_public = $courseinfo['course_code_public'];
    } else {
        $course_code_public = htmlspecialchars($input['course_code']);
    }
            
    $label = $courseinfo['course_name'];
    $course_id = $courseinfo['course_code'];
     
    $ok = ezmam_course_create_repository($course_id, $course_code_public, $label, $label, 'course');
    if (!$ok) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Album $album_name did not exist and creation failed", array("cli_mam_insert"), $asset);
        exit(4);
    }
}

//initialize asset metadata and media metadata
$asset_meta['author']=$recording_metadata['author'];
$asset_meta['title']=$recording_metadata['title'];
$asset_meta['origin']=$recording_metadata['origin'];
$asset_meta['description']=$recording_metadata['description'];
$asset_meta['record_date']=$recording_metadata['record_date'];
$asset_meta['record_type']=$recording_metadata['record_type'];
$asset_meta['status']='processing';
$asset_meta['tags']="";
$asset_meta['language']="français"; //TODO: this shouldn't be hardcoded
$asset_meta['super_highres'] = $recording_metadata['super_highres'];
if (isset($recording_metadata['submitted_cam'])) {
    $asset_meta['submitted_cam']=$recording_metadata['submitted_cam'];
}
if (isset($recording_metadata['submitted_slide'])) {
    $asset_meta['submitted_slide']=$recording_metadata['submitted_slide'];
}
if (isset($recording_metadata['intro'])) {
    $asset_meta['intro']=$recording_metadata['intro'];
}
if (isset($recording_metadata['credits'])) {
    $asset_meta['credits']=$recording_metadata['credits'];
}
if (isset($recording_metadata['add_title'])) {
    $asset_meta['add_title']=$recording_metadata['add_title'];
}
$asset_meta['ratio']= (isset($recording_metadata['ratio'])) ? $recording_metadata['ratio'] : 'auto';
if (isset($recording_metadata['downloadable'])) { // if the recording has been submitted
    $asset_meta['downloadable']=$recording_metadata['downloadable'];
} else { // the recording comes from EZrecorder
    $album_meta = ezmam_album_metadata_get($album_name);
    $asset_meta['downloadable'] = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable);
}


//create asset if not exists!
$asset_name=$record_date;
if (!ezmam_asset_exists($album_name, $asset_name)) {
    ezmam_asset_new($album_name, $asset_name, $asset_meta);
}

//do we have a cam movie?
if (strpos($record_type, "cam")!==false) {
    //insert original cam media in asset with attention to file extension/mimetype
    $ok = originals_mam_insert_media($album_name, $asset_name, 'cam', $recording_metadata, $recording_dir);
    if (!$ok) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Cam media insertion failed for album $album_name", array("cli_mam_insert"), $asset);
        set_asset_status_to_failure();
        exit(6);
    }
}
//do we have a slide movie?
if (strpos($record_type, "slide")!==false) {
    //insert original slide media in asset with attention to file extension/mimetype
    $ok = originals_mam_insert_media($album_name, $asset_name, 'slide', $recording_metadata, $recording_dir);
    if (!$ok) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Slide media insertion failed for album $album_name", array("cli_mam_insert"), $asset);
        set_asset_status_to_failure();
        exit(6);
    }
}
//media(s) inserted into mam, so move the processing directory to mam_inserted
$inserted_recording_dir=dirname(dirname($recording_dir)).'/mam_inserted/'.basename($recording_dir);
if (file_exists($inserted_recording_dir)) { //This may happen if we re process an already processed asset
    $new_name = $inserted_recording_dir .'.'.time();
    $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::WARNING, "mam_inserted folder already existed, rename old one to $new_name", array("cli_mam_insert"), $asset);
    $ok = rename($inserted_recording_dir, $inserted_recording_dir.'.'.time());
    if (!$ok) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::ERROR, "Could not move old mam_inserted folder to $new_name, the folder will be left over in its current location ($recording_dir)", array("cli_mam_insert"), $asset);
    } else {
        rename($recording_dir, $inserted_recording_dir);
    }
} else {
    rename($recording_dir, $inserted_recording_dir);
}

//now launch cam and/or slide video processing


//infos neccessaires: quelle intro, info titre , input movie , closing credits
// intro donnees dans les metadata de l'album? ou global semeur?
// info titre tirees des meta de l'asset
//input movie donnee par le media
$cmd="$php_cli_cmd $submit_intro_title_movie_pgm $album_name $asset_name";
system($cmd, $returncode);
if ($returncode) {
    $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Command $cmd failed with result $returncode", array("cli_mam_insert"), $asset);
    set_asset_status_to_failure();
    exit(7);
}
    
//
function set_asset_status_to_failure()
{
    global $album_name;
    global $asset_name;
    ezmam_asset_status_set_properties($album_name, $asset_name, 'status', 'failed');
}

/**
 * insert original cam or slide in repository and if user submit, checks submitted file's extension
 * @param string $camslide
 * @param assoc_array $recording_metadata
 * @param string $recording_dir
 * @return bool
 */
function originals_mam_insert_media($album_name, $asset_name, $camslide, &$recording_metadata, $recording_dir)
{
    global $logger;
    global $asset;
      
    $media_name='original_'.$camslide;
    //initialize media metadata and media metadata
    $media_meta['author']=$recording_metadata['author'];
    $media_meta['title']=$recording_metadata['title'];
    $media_meta['description']=$recording_metadata['description'];
    $media_meta['record_date']=$recording_metadata['record_date'];
    $media_meta['width']="N/A";
    $media_meta['height']="N/A";
    $media_meta['duration']="N/A";
    $media_meta['video_codec']="N/A";
    $media_meta['audio_codec']="N/A";
    $media_meta['disposition']='file';
    //check the name of submited file to keep the extension (pcastaction is quite sensitive to extension type)
    if (isset($recording_metadata['submitted_' . $camslide])) {
        $res=file_get_extension($recording_metadata['submitted_' . $camslide]);
        $ext=$res['ext'];
        if ($ext=='') {
            //if there wasn't an extension, then check mimetype
            $mimetype=$recording_metadata['submitted_mimetype'];
            list($type, $subtype)=explode('/', $mimetype);
            $mimetypes2ext=array('mpeg'=>'mpg','quicktime'=>'mov','x-msvideo'=>'avi');
            if (isset($mimetypes2ext[$subtype])) {
                $ext=$mimetypes2ext[$subtype];
            } else {
                $ext='mov';
            }//default extension
        }
        if (ctype_alnum($ext)) { //an extension should not have bad chars
            $mov_filepath=$recording_dir.'/'.$camslide.'.mov';
            $goodext_filepath=$recording_dir.'/'.$camslide.'.'.$ext;
            //rename cam.mov into cam.mpg if submit was an .mpg file
            rename($mov_filepath, $goodext_filepath);
            $media_file_path=$goodext_filepath;
            $media_meta['filename']="$camslide.$ext";
        } else {
            $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Submitted filename has bad extension: $ext", array("cli_mam_insert"), $asset);
            return false;
        }
    } else {
        $return_val = 0;
        $media_file_path = system("ls $recording_dir/$camslide.*", $return_val);
        if ($return_val != 0) {
            $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Could not find media file in $recording_dir/$camslide.*", array("cli_mam_insert"), $asset);
            return false;
        }
        $media_meta['filename']= basename($media_file_path);
        if ($media_meta['filename'] == "") {
            $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Invalid file path '$media_file_path' found for media $camslide", array("cli_mam_insert"), $asset);
            return false;
        }
    }
  
    $asset_name_with_course = basename($recording_dir);
  
    $filesize = round(filesize($media_file_path)/1048576);
    $media_meta['file_size'] = $filesize;
    $ok = ezmam_media_new($album_name, $asset_name, $media_name, $media_meta, $media_file_path);
    if (!$ok) {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::CRITICAL, "Error adding original_$camslide:".  ezmam_last_error(), array("cli_mam_insert"), $asset);
        return false;
    } else {
        $logger->log(EventType::MANAGER_MAM_INSERT, LogLevel::NOTICE, "$camslide media inserted in repository in $album_name $asset_name $media_name. Last error:" . ezmam_last_error(), array("cli_mam_insert"), $asset_name_with_course);
    }
      
    return true;
}
