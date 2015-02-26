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

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

/*
 * This program handles the cam,slide movies and metadata of a classroom recording or user submit:
 * inserts them in the repository (via lib_ezmam)
 *   This means: asset creation, , add metadata
 * calls renderer to process the movies
 */

if($argc!=2){
    echo "usage: ".$argv[0].' <directory_of_downloaded_ok_recording>
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
';
    die;
}



$recording_dir=$argv[1]; //first (and only) parameter in command line
if(!file_exists($recording_dir)){
 //no
 myerror("could not find recording directory: $recording_dir") ; //log error
}
//local log file:
$local_log_file=$recording_dir."/download.log";

//now get metadata about the recording
$recording_metadata = metadata2assoc_array($recording_dir."/metadata.xml");
if($recording_metadata===false)    myerror ("bad xml or read problem on  $recording_dir"."/metadata.xml") ; //log to file
 // 'course_name' 'origin' 'record_date' 'description' 'record_type' 'moderation'

$record_type=$recording_metadata['record_type'];
$course_name=$recording_metadata['course_name'];
$record_date=$recording_metadata['record_date'];

//check for album existance
if($recording_metadata['moderation']=="false")
    $album_name=$course_name."-pub";
  else
    $album_name=$course_name."-priv";

if(!ezmam_album_exists($album_name)){
 print "ERROR: album does not exist! $album_name\n";
 exit(0);

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
$asset_meta['language']="français";
$asset_meta['super_highres'] = $recording_metadata['super_highres'];
if(isset($recording_metadata['submitted_cam']))$asset_meta['submitted_cam']=$recording_metadata['submitted_cam'];
if(isset($recording_metadata['submitted_slide']))$asset_meta['submitted_slide']=$recording_metadata['submitted_slide'];
if(isset($recording_metadata['intro']))$asset_meta['intro']=$recording_metadata['intro'];
if(isset($recording_metadata['add_title']))$asset_meta['add_title']=$recording_metadata['add_title'];
$asset_meta['ratio']= (isset($recording_metadata['ratio'])) ? $recording_metadata['ratio'] : 'auto';
if(isset($recording_metadata['downloadable'])){ // if the recording has been submitted
    $asset_meta['downloadable']=$recording_metadata['downloadable'];
} else { // the recording comes from EZrecorder
    $album_meta = ezmam_album_metadata_get($album_name);
    $asset_meta['downloadable'] = (isset($album_meta['downloadable']) ? $album_meta['downloadable'] : $default_downloadable); 
}


//create asset if not exists!
$asset_name=$record_date;
if(!ezmam_asset_exists($album_name, $asset_name))ezmam_asset_new($album_name, $asset_name, $asset_meta);

//do we have a cam movie?
if( strpos($record_type,"cam")!==false) {
   //insert original cam media in asset with attention to file extension/mimetype
  originals_mam_insert_media($album_name,$asset_name,'cam',$recording_metadata,$recording_dir);
}
//do we have a slide movie?     
if(strpos($record_type,"slide")!==false){
  //insert original slide media in asset with attention to file extension/mimetype
  $res1=originals_mam_insert_media($album_name,$asset_name,'slide',$recording_metadata,$recording_dir);
}
//media(s) inserted into mam, so move the processing directory to mam_inserted
$inserted_recording_dir=dirname(dirname($recording_dir)).'/mam_inserted/'.basename($recording_dir);
rename($recording_dir, $inserted_recording_dir );
//now launch cam and/or slide video processing


    //infos neccessaires: quelle intro, info titre , input movie
    // intro donnees dans les metadata de l'album? ou global semeur?
    // info titre tirees des meta de l'asset
    //input movie donnee par le media
    $cmd="$php_cli_cmd $submit_intro_title_movie_pgm  $album_name $asset_name $super_highres >>/dev/null 2>&1"; // TODO: restore 
    print "exec command: $cmd\n";
    exec($cmd, $cmdoutput, $returncode);
    if($returncode) print "Submit_intro_title_movie failed";



function myerror($msg){
    print $msg."\n";
    exit(1);

}

/**
 * insert original cam or slide in repository and if user submit, checks submitted file's extension
 * @param string $camslide
 * @param assoc_array $recording_metadata
 * @param string $recording_dir
 * @return bool
 */
function originals_mam_insert_media($album_name,$asset_name,$camslide,&$recording_metadata,$recording_dir){
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
  if(isset($recording_metadata['submitted_' . $camslide])){
    $res=file_get_extension($recording_metadata['submitted_' . $camslide]);
    $ext=$res['ext'];
    if($ext==''){
        //if there wasn't an extension, then check mimetype
        $mimetype=$recording_metadata['submitted_mimetype'];
        list($type,$subtype)=explode('/', $mimetype);
        $mimetypes2ext=array('mpeg'=>'mpg','quicktime'=>'mov','x-msvideo'=>'avi');
        if(isset($mimetypes2ext[$subtype]))
            $ext=$mimetypes2ext[$subtype];
          else
            $ext='mov';//default extension
        }
    if(ctype_alnum($ext)){//an extension should not have bad chars
       $mov_filepath=$recording_dir.'/'.$camslide.'.mov';
       $goodext_filepath=$recording_dir.'/'.$camslide.'.'.$ext;
       //rename cam.mov into cam.mpg if submit was an .mpg file
       rename($mov_filepath,$goodext_filepath);
       $media_file_path=$goodext_filepath;
       $media_meta['filename']="$camslide.$ext";
    }
    else{
     print "submitted filename has bad extension: $ext";
        myerror ("submitted filename has bad extension: $ext");
  }
 }else{
  $media_file_path=system("ls $recording_dir/$camslide.*");
  $media_meta['filename']=  basename($media_file_path);
 }
 $filesize=round(filesize($media_file_path)/1048576);
  $media_meta['file_size']=$filesize;
  $res1=ezmam_media_new($album_name, $asset_name, $media_name, $media_meta, $media_file_path);
  if(!$res1){
      myerror( "error adding original_$camslide:".  ezmam_last_error()."\n");
    }
   else
    print "$camslide media inserted in repository in $album_name $asset_name $media_name\n";
  return $res1;
}
?>
