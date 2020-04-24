<?php

/**
 * @package ezcast.ezmanager.cli
 */

/*
 * This program handles media cam,slide,upload movies and asset's metadata an ask a remote machine to render the video
 * as intro - title - movie
 * then inserts the result as a media  in the repository (via lib_ezmam)
 *   This means: build a work directory, send it via ssh to the render client (XServe), get info  back and finally add/update media
 */

include_once 'config.inc';
include_once 'lib_ezmam.php';
include_once 'lib_various.php';
require_once __DIR__.'/../commons/lib_scheduling.php';

Logger::$print_logs = true;

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

if ($argc!=3) {
    echo "Usage: ".$argv[0]." <album_name> <asset_time>" . PHP_EOL;
    echo "Example php cli_submit_intro_title_movie.php MOOC-G3-pub 2016_09_26_10h40" . PHP_EOL;

    $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::WARNING, __FILE__ ." called with wrong argc count: $argc. argv: " . json_encode($argv), array("cli_submit_intro_title_movie"));
    exit(1);
}

$album=$argv[1];
$asset=$argv[2];

//check if given asset exists
if (!ezmam_asset_exists($album, $asset)) {
    $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::CRITICAL, "ezmam did not find referenced asset (album: $album, asset: $asset)", array("cli_submit_intro_title_movie"), $asset);
    exit(2);
}

//create directory used to transmit the (video) rendering/processing work to one of the renderers
$processing_dir_name=$asset."_".$album."_intro_title_movie";
$render_dir=$render_root_path."/processing/".$processing_dir_name;
if (!file_exists($render_dir)) {
    mkdir($render_dir);
    chmod($render_dir, 0777);
}

$path_to_videos = $repository_path.'/'.$album.'/'.$asset;
$medias = submit_itm_get_medias($album, $asset);

foreach ($medias as $media_name => $media_path) {
    exec('ln -s '.$repository_path.'/'.$media_path.' '.$render_dir.'/', $output, $val);
}

$asset_meta = ezmam_asset_metadata_get($album, $asset);
//lets get the name of intro movie
$album_meta = ezmam_album_metadata_get($album);
//put title info into $render_dir/title.xml
//generate title description

$res=submit_itm_set_title($album_meta, $asset_meta, $render_dir);

if (!isset($asset_meta['intro'])) {
    if (isset($album_meta['intro'])) {
        $intro=$album_meta['intro'];
    } else {
        $intro=$default_intro;
    }//default intro movie from config.inc
} else {
    $intro = $asset_meta['intro'];
}
$asset_meta['intro'] = $intro;
ezmam_asset_meta_set($album.'/'.$asset, $asset_meta);



if (!isset($asset_meta['credits'])) {
    if (isset($album_meta['credits'])) {
        $credits=$album_meta['credits'];
    } else {
        $credits=$default_credits;
    }//default closing credits movie from config.inc
} else {
    $credits = $asset_meta['credits'];
}


if (!isset($asset_meta['add_title'])) {
    if (isset($album_meta['add_title'])) {
        $asset_meta['add_title'] = $album_meta['add_title'];
        ezmam_asset_meta_set($album.'/'.$asset, $asset_meta);
    } else {
        $asset_meta['add_title'] = $default_add_title;
        if ($default_add_title!=false) {
            ezmam_asset_meta_set($album.'/'.$asset, $asset_meta);
        }   // CREATE THIS FUNCTION AND GIVE THE RIGHTS PARAMS
    }
}

//now write the processing info
//intro movies are in the renderer
//credits movies are in the renderer
//input movie file is in the repository
//output movie will be in the renderer in $render_dir

$processing_assoc=array('submit_date'=>date($dir_date_format),
     'status'=>'submit',
     'origin'=>$asset_meta['origin'],
     'record_type'=>$asset_meta['record_type'],
     'server_pid'=>(string)getmypid(),
     'intro_movie'=>$intro,
     'credits_movie'=>$credits,
     'super_highres' => $asset_meta['super_highres'],
     'add_title' => $asset_meta['add_title'],
     'ratio' => $asset_meta['ratio']
);

//check if this is a usersubmitted file and store its original name in asset meta
if (isset($asset_meta['submitted_filename'])) {
    $processing_assoc['submitted_filename']=$asset_meta['submitted_filename'];
}

//get list of medias and (relative) path in the form 'original_cam'=>'<albumname>/<assetname>/<medianame>/<filename>
$media_path_assoc=submit_itm_get_medias($album, $asset);
//add medias original_cam and/or original_slide and their relative path in the repository:

$processing_assoc=array_merge($processing_assoc, $media_path_assoc);
$res=assoc_array2metadata_file($processing_assoc, $render_dir."/toprocess.xml");

//fix permissions
chmod($render_dir."/toprocess.xml", 0775);
if ($res==0) {
    $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::CRITICAL, "Could not write processing metadata in $render_dir/toprocess.xml", array("cli_submit_intro_title_movie"), $asset);
    exit(3);
}

// Append the new job
$ok = scheduler_append(array(
  'location' => $render_dir,
  'origin' => $asset_meta['origin'],
  'sender' => $asset_meta['author'],
  'name' => $asset_meta['title'],
  'asset' => $asset,
  'album' => $album,
));
if (!$ok) {
    $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::CRITICAL, "Rendering job scheduling failed (call to scheduler_append)", array("cli_submit_intro_title_movie"), $asset);
    exit(4);
}

// Launch the scheduler
scheduler_schedule();

$pos = strrpos($album, "-");
$album_without_mod = substr($album, 0, $pos);
$asset_name = $asset . '_' . $album_without_mod;
$logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::INFO, "Successfully scheduled rendering job", array("cli_submit_intro_title_movie"), $asset_name);
exit(0);

/**
 * generate the title.xml file in render_dir for title processing
 * @global string $organization_name
 * @param array $album_meta
 * @param string $asset_meta
 * @param string $render_dir
 */
function submit_itm_set_title($album_meta, $asset_meta, $render_dir)
{
    global $organization_name,$copyright,$movie_keywords;
    global $logger;

    //get date
    $human_date=get_user_friendly_date($asset_meta['record_date'], ' ', true, 'fr', false);
    $asset_title=$asset_meta['title'];
    //medium short titles should break after album name
    if (strlen($asset_title)<=24) {
        $asset_title=str_replace(' ', utf8_encode(chr(160)), $asset_title);
    }//original title with no breaking spaces
    
    $title_info=array(
      'album' => '['. suffix_remove($album_meta['name']) . '] ' . choose_title_from_metadata($album_meta) ,
      'title'=>  suffix_remove($album_meta['name'])." ".$asset_title,
      'author'=>$asset_meta['author'],
      'date'=> $human_date,
      'organization'=>$organization_name,
      'copyright'=>$copyright,
      'keywords'=>$movie_keywords
    );
    //write the title xml file to the "shared directory"
    $res=assoc_array2metadata_file($title_info, $render_dir."/title.xml");
    if (!$res) {
        $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::CRITICAL, "Could not write title metadata to $render_dir/title.xml", array("cli_submit_intro_title_movie"));
        exit(4);
    }
    return $res;
}

/**
 * returns an assoc array of original medias relative path
 * @global string $repository_path
 * @param string $album
 * @param string $asset
 */
function submit_itm_get_medias($album, $asset)
{
    global $repository_path;
    global $logger;
    
    $medias=array();
    //scan all of asset's media for originals
    $media_meta_list=ezmam_media_list_metadata_assoc($album, $asset);
    foreach ($media_meta_list as $media => $media_meta) {
        if (substr($media, 0, 9)=="original_" && $media_meta['disposition']=="file") {
            //its an original and its a movie
            // $media_type=substr($media,9);//cam or slide
            //add an assoc element: media=>relativepath_to_media
            $medias[$media]=ezmam_media_getpath($album, $asset, $media, true);
            $file_path = $repository_path."/".$medias[$media];
            if (!(file_exists($file_path))) {
                $logger->log(EventType::MANAGER_SUBMIT_RENDERING, LogLevel::ERROR, "File/dir $file_path does not exists", array("cli_submit_intro_title_movie"));
            }
        }//endif original
    }//end foreach media
    
    return $medias;
}
