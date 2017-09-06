<?php

chdir(__DIR__);

/**
 * @package ezcast.ezmanager.cli
 */
include_once 'config.inc';
include_once 'lib_ezmam.php';
include_once 'lib_various.php';

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);


/*
 * This program handles media cam,slide,upload movies and asset's metadata and ask a remote machine to render the video
 * as intro - title - movie
 * then inserts the result as a media  in the repository (via lib_ezmam)
 *   This means: build a work directory, send it via ssh to the render client , get info  back and finally add/update media
 */
if ($argc != 4) {
    echo "\nusage: " . $argv[0] . " <album_name> <asset_name> <full_path_to_processing_dir>\n";
    die;
}

$album = $argv[1];
$asset = $argv[2];
$render_dir = $argv[3];

//check if given asset exists
if (!ezmam_asset_exists($album, $asset)) {
    myerror("ezmam did not find referenced asset: $album, $asset");
}

//check if intro-title-movie had done its job
var_dump($render_dir . "/processing.xml");
$resultprocessing_assoc = metadata2assoc_array($render_dir . "/processing.xml");
if (!is_array($resultprocessing_assoc)) {
    myerror("could not get result metadata");
}
$status = $resultprocessing_assoc['status'];
if ($status != "processed") {
    myerror("processing return status: $status");
}
//intro-title-movie and compress high/low done, so lets insert them in our asset
$media_files = glob("$render_dir/*.mov");
//scan for all rendered medias (starting with 'high_' or 'low_' and continues with 'slide_' or 'cam_')
foreach ($media_files as $filepath) {
    $filename = basename($filepath);
    list($quality, $type) = explode("_", $filename); // first part is high/low
    list($type, $ext) = explode('.', $type); //second part is cam/slide third part is extension (.mov)
    //look for (low|high)_(cam|slide).mov movie filename
    if (in_array($quality, array('high', 'low')) && in_array($type, array('cam', 'slide')) && $ext == 'mov') {
        //set 'high' media metadata
        $res = high_low_media_mam_insert($album, $asset, $quality, $type, $resultprocessing_assoc, $render_dir);
    }
}
//check if qtinfo has been returned for the originals
$originals_qtinfo_files = glob("$render_dir/original_*_qtinfo.xml");
foreach ($originals_qtinfo_files as $qtinfo_filepath) {
    update_originals_meta($album, $asset, $qtinfo_filepath); //update associated media
}
//if slide look for chapters
$chapter_slide_dir = $render_dir . '/chapter_slide';
if (is_dir($chapter_slide_dir)) {
    insert_chapterslide_media($album, $asset, $chapter_slide_dir);
}

print "intro-title-movie processing done";
//move processed render_dir to archive dir
rename($render_dir, $render_root_path . '/processed/' . basename($render_dir));

// delete video files that are already in the repository (original cam and slide)
foreach (glob($render_root_path . '/processed/' . basename($render_dir) . '/*') as $f) {
    if ((strpos($f, "cam") !== false || strpos($f, "slide") !== false) && !strpos($f, "xml")) {
        unlink($f);
    }
}

$asset_meta = ezmam_asset_metadata_get($album, $asset);
//if we found the duration of the encoded videos set the asset's duration
if (isset($duration) && is_numeric($duration)) {
    $asset_meta['duration'] = round($duration);
}

$asset_meta['status'] = 'processed';
$res = ezmam_asset_metadata_set($album, $asset, $asset_meta);
if (!$res) {
    print "asset metadata set error:" . ezmam_last_error() . "\n";
}

$pos = strrpos($album, "-");
$album_without_mod = substr($album, 0, $pos);
$asset_name = $asset . '_' . $album_without_mod;
$logger->log(EventType::ASSET_FINALIZED, LogLevel::NOTICE, "Asset succesfully finalized", array(basename(__FILE__)), $asset_name);



function high_low_media_mam_insert($album, $asset, $high_low, $cam_slide, $processing_assoc, $render_dir)
{
    global $title, $duration;
    //get qtinfo assoc info
    $qtinfo_assoc = metadata2assoc_array($render_dir . "/{$high_low}_{$cam_slide}_qtinfo.xml");
    if (!is_array($qtinfo_assoc)) {
        myerror("error in intro-title-movie {$high_low}_{$cam_slide}_qtinfo.xml missing/corrupted");
    }
    //first copy most of the parameters from original media
    $movie_filename = $high_low . '_' . $cam_slide . '.mov';
    $media_meta['duration'] = $qtinfo_assoc['duration'];
    if (isset($qtinfo_assoc['duration'])) {
        $duration = $qtinfo_assoc['duration'];
    }
    $media_meta['height'] = $qtinfo_assoc['height'];
    $media_meta['width'] = $qtinfo_assoc['width'];
    $media_meta['videocodec'] = $qtinfo_assoc['videoCodec'];
    $media_meta['audiocodec'] = $qtinfo_assoc['audioCodec'];
    $media_meta['filename'] = $movie_filename;
    $filesize = round(filesize($render_dir . '/' . $movie_filename) / 1048576);
    $media_meta['file_size'] = $filesize;
    $media_meta['disposition'] = "file";
    $media_meta['duration'] = $qtinfo_assoc['duration'];
    $media = $high_low . '_' . $cam_slide;
    if (!isset($duration)) {
        $duration = $qtinfo_assoc['duration'];
    }
    //put  the media in the repository
    //use copy option to have the right owner
    print "insert media $album $asset $media\n";
    $res = ezmam_media_new($album, $asset, $media, $media_meta, $render_dir . '/' . $movie_filename);
    if (!$res) {
        myerror("could not add media to repository: " . ezmam_last_error());
    }
    // remove processed video (owned by remote user but we are owner of upper dir)
    //unlink($media_file_path);
    return true;
}

function myerror($msg)
{
    print $msg . "\n";
    exit(1);
}

function update_originals_meta($album, $asset, $qtinfo_filepath)
{
    $qtinfo_assoc = metadata2assoc_array($qtinfo_filepath);
    //dig media name according to qtinfo filename
    $qtinfofile = basename($qtinfo_filepath);
    list($quality, $type, $qtinfo) = explode("_", $qtinfofile); // first part is high/low
    $media = 'original_' . $type;
    $media_meta = ezmam_media_metadata_get($album, $asset, $media);
    $media_meta['duration'] = $qtinfo_assoc['duration'];
    $media_meta['height'] = $qtinfo_assoc['height'];
    $media_meta['width'] = $qtinfo_assoc['width'];
    if (isset($qtinfo_assoc['videoCodec'])) {
        $media_meta['videocodec'] = $qtinfo_assoc['videoCodec'];
    }
    if (isset($qtinfo_assoc['audioCodec'])) {
        $media_meta['audiocodec'] = $qtinfo_assoc['audioCodec'];
    }

    //update  originals meta
    print "update media metadata $album $asset $media\n";
    ezmam_media_metadata_set($album, $asset, $media, $media_meta);
}

/**
 * insert chapter_slide directory in a media
 * @param string $album
 * @param string $asset
 * @param string $chapter_slide_dir
 */
function insert_chapterslide_media($album, $asset, $chapter_slide_dir)
{
    $media = "chapter_slide";
    $media_meta['filename'] = "chapter_slide";
    $media_meta['disposition'] = "directory"; //not a simple file, but a directory with a chapters.xml and chapter<n>.png
    print "insert media chapter_slide media $album $asset $media\n";
    $res = ezmam_media_new($album, $asset, $media, $media_meta, $chapter_slide_dir, true);
    //unlink($chapter_slide_dir);
}
