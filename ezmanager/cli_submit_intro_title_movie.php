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
include_once 'lib_various.php';
include_once 'lib_scheduling.php';

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

/*
 * This program handles media cam,slide,upload movies and asset's metadata an ask a remote machine to render the video
 * as intro - title - movie
 * then inserts the result as a media  in the repository (via lib_ezmam)
 *   This means: build a work directory, send it via ssh to the render client (XServe), get info  back and finally add/update media
 */
if($argc!=3){
    echo "\nusage: ".$argv[0]." <album_name> <asset_name>\n";
    die;
}



$album=$argv[1];
$asset=$argv[2];

//check if given asset exists
if(!ezmam_asset_exists($album, $asset)){
    myerror ("ezmam did not find referenced asset: $album, $asset");
}

//create directory used to transmit the (video) rendering/processing work to one of the Macs
$processing_dir_name=$asset."_".$album."_intro_title_movie";
$render_dir=$render_root_path."/processing/".$processing_dir_name;
mkdir($render_dir);
chmod($render_dir, 0777);

$path_to_videos = $repository_path.'/'.$album.'/'.$asset;
$medias = submit_itm_get_medias($album, $asset);

foreach($medias as $media_name => $media_path) {
    exec('ln -s '.$repository_path.'/'.$media_path.' '.$render_dir.'/', $output, $val);
}

$asset_meta=ezmam_asset_metadata_get($album, $asset);
//lets get the name of intro movie
$album_meta=ezmam_album_metadata_get($album);
//put title info into $render_dir/title.xml
//generate title description

$res=submit_itm_set_title($album_meta,$asset_meta,$render_dir);

if (!isset($asset_meta['intro'])){
    if(isset($album_meta['intro']))
        $intro=$album_meta['intro'];
    else
        $intro=$default_intro;//default intro movie from config.inc
} else {
    $intro = $asset_meta['intro'];
}

if(!isset($asset_meta['add_title'])) {
    if (isset($album_meta['add_title'])) {
        $asset_meta['add_title'] = $album_meta['add_title'];
    } else {
        $asset_meta['add_title'] = $default_add_title;
    }
}

//now write the processing info
//intro movies are in the rw nfs share
//input movie file is in the repository
//output movie will be in rw share in $render_dir

$processing_assoc=array('submit_date'=>date($dir_date_format),
     'status'=>'submit',
     'origin'=>$asset_meta['origin'],
     'record_type'=>$asset_meta['record_type'],
     'server_pid'=>(string)getmypid(),
     'intro_movie'=>$intro,
     'super_highres' => $asset_meta['super_highres'],
     'add_title' => $asset_meta['add_title'],
     'ratio' => $asset_meta['ratio']
);

//check if this is a usersubmitted file and store its original name in asset meta
if(isset($asset_meta['submitted_filename']))$processing_assoc['submitted_filename']=$asset_meta['submitted_filename'];
//get list of medias and (relative) path in the form 'original_cam'=>'<albumname>/<assetname>/<medianame>/<filename>
$media_path_assoc=submit_itm_get_medias($album,$asset);
//add medias original_cam and/or original_slide and their relative path in the repository:
$processing_assoc=array_merge($processing_assoc,$media_path_assoc);
$res=assoc_array2metadata_file($processing_assoc, $render_dir."/toprocess.xml");
//fix permissions
chmod($render_dir."/toprocess.xml", 0775);
if($res==0) myerror ("couldnt write processing metadata in $render_dir/toprocess.xml");

// Append the new job
scheduler_append(array(
  'location' => $render_dir,
  'origin' => $asset_meta['origin'],
  'sender' => $asset_meta['author'],
  'name' => $asset_meta['title'],
  'asset' => $asset,
  'album' => $album,
));

// Launch the scheduler
scheduler_schedule();

die();


/**
 * generate the title.xml file in render_dir for title processing
 * @global string $organization_name
 * @param string $album
 * @param string $asset_meta
 * @param string $render_dir
 */
function submit_itm_set_title($album_meta,$asset_meta,$render_dir){
    global $organization_name,$copyright,$title_duration;

  //get date
  $human_date=get_user_friendly_date($asset_meta['record_date'], ' ', true , 'fr' , false);
  $asset_title=$asset_meta['title'];
  //medium short titles should break after album name
  if(strlen($asset_title)<=24)$asset_title=str_replace(' ', utf8_encode(chr(160)), $asset_title);//original title with no breaking spaces
  $title_info=array(
    'album' => '['. suffix_remove($album_meta['name']) . '] ' . $album_meta['description'] ,
    'title'=>  suffix_remove($album)." ".$asset_title,
    'author'=>$asset_meta['author'],
    'date'=> $human_date,
    'organization'=>$organization_name,
    'copyright'=>$copyright
   );
//write the title xml file to the "shared directory"
$res=assoc_array2metadata_file($title_info,$render_dir."/title.xml");
if(!$res)myerror ("couldnt write title metadata to $render_dir/title.xml");
 return $res;
}

/**
 * returns an assoc array of original medias relative path
 * @global string $repository_path
 * @param string $album
 * @param string $asset
 */
function submit_itm_get_medias($album,$asset){
    global  $repository_path;
    $medias=array();
//scan all of asset's media for originals
$media_meta_list=ezmam_media_list_metadata_assoc($album, $asset);
foreach ($media_meta_list as $media => $media_meta) {
  if(substr($media,0,9)=="original_" && $media_meta['disposition']=="file"){
    //its an original and its a movie
    $media_type=substr($media,9);//cam or slide
    //add an assoc element: media=>relativepath_to_media
    $medias[$media]=ezmam_media_getpath($album, $asset, $media, true);
    if(!(file_exists($repository_path."/".$medias[$media]) 
            && !is_dir($repository_path."/".$medias[$media])))myerror ("$repository_path/$media_file_path is not a file");
  }//endif original
 }//end foreach media
 return $medias;
}

function myerror($msg){
    global $mailto_alert, $album, $asset;
    print $msg."\n";
    mail($mailto_alert, "Rendering failure on $asset"."_"."$album", $msg);
    exit(1);

}

?>
