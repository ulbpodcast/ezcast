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
 * @package ezcast.ezmanager.lib.upload
 */

function media_submit_create_metadata($tmp_name, $metadata)
{
    global $submit_upload_dir;
    
   
    // Sanity checks
    if (!is_dir($submit_upload_dir)) {
        ezmam_last_error($submit_upload_dir.' is not a directory');
        return false;
    }
    
    if (!$metadata) {
        ezmam_last_error('not metadata provided');
        return false;
    }
    
    // 1) create directory
    $folder_path = $submit_upload_dir.'/'.$tmp_name;
    
    if (file_exists($folder_path)) {
        ezmam_last_error('Asset already exists. Wait one minute before retrying.');
        return false;
    }
    
    mkdir($folder_path);
    
    // 2) put metadata into xml file
    $res = assoc_array2metadata_file($metadata, $folder_path.'/metadata.xml');
    if (!$res) {
        ezmam_last_error('ezmam_media_submit_create_metadata: could not save metadata');
        return false;
    }
    
    return true;
}

function media_submit_error($tmp_name)
{
    global $submit_upload_dir;
    global $submit_upload_failed_dir;
    
    // Sanity checks
    if (!is_dir($submit_upload_dir)) {
        ezmam_last_error($submit_upload_dir.' is not a directory');
        return false;
    }
    
    if (!is_dir($submit_upload_failed_dir)) {
        ezmam_last_error($submit_upload_failed_dir.' is not a directory');
        return false;
    }
    
    $folder_path = $submit_upload_dir.'/'.$tmp_name;
    
    if (file_exists($folder_path)) {
        rename($folder_path, $submit_upload_failed_dir.'/'.$tmp_name);
    } else {
        ezmam_last_error('Asset does not exist.');
        return false;
    }
    return true;
}

function checkInitSubmitServiceVar(&$input,$ratio="auto",$downloadable="true",$add_title="false",$credits="false",$intro="",$keepQuality="",$type="cam",$moderation="false"){
   global $TokenUploadServcice;
   global $valid_extensions;
    
    if(isset($input['user_full_name']) && $input['user_full_name']!='' &&
        isset($input['user_login']) && $input['user_login']!='' &&
        isset($input['album']) && $input['album']!='' && ezmam_album_exists($input['album']."-pub" ) && 
        isset($input['title']) && $input['title']!='' &&
        isset($input['description']) && $input['description']!='' &&
        isset($input['token'])  && $TokenUploadServcice!="" && $input['token'] == md5($TokenUploadServcice.$input['user_login'])){

        //Check if user has the right to post video in this album
        $users=db_course_get_users($input['album']);
        for($i=0;$i<count($users);$i++){
            $authorizedUsers[$i]=$users[$i]['user_ID'];            
        }        
        $_SESSION['user_full_name']=$input['user_full_name'];
        $_SESSION['user_login']=$input['user_login'];

        //init by default
        $input['ratio']=$ratio;
        $input['downloadable']=$downloadable;
        $input['add_title']=$add_title;
        $input['credits']=$credits;
        $input['intro']=$intro;
        $input['keepQuality']=$keepQuality;
        $input['type']=$type;
        $input['moderation']=$moderation;
        return true;
    }
    else 
        return false;
}