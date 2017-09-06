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

include "config.inc";
set_time_limit(0);
$input=array_merge($_GET, $_POST);

//check whether upload has a good file
$upload_err=$_FILES['userfile']['error'];
if ($upload_err!=0) {
    print "UPLOAD_ERROR:".$upload_err;
    die;
}

//get input parameters
$doctype=$input['document_type'];
$recording_name=$input['recording_name'];
$recording_name_sanitized=str_to_safedir($recording_name);
if ($doctype=="slide") {
    $filename="original_slide.mp4";
}
if ($doctype=="cam") {
    $filename="original_cam.mp4";
}
if ($doctype=="metadata") {
    $filename="_metadata.xml";
}

//creates a directory that will contain slide, camera and record metadata
if (!file_exists($recorder_uploads_dir."/".$recording_name_sanitized)) {
    mkdir($recorder_uploads_dir."/".$recording_name_sanitized);
}
$uploadfile = $recorder_uploads_dir ."/".$recording_name_sanitized."/". $filename;
print "uploadfile $uploadfile <br>\n";
echo '<pre>';

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";


/**
 *
 * @param string $string
 * @return string
 * @desc returns a directory safe
 */
function str_to_safedir($string)
{
    $toalnum="";
    for ($idx=0;$idx<strlen($string);$idx++) {
        if (ctype_alnum($string[$idx]) || $string[$idx]=="-") {
            $toalnum.=$string[$idx];
        } else {
            $toalnum.="_";
        }
    }
    return $toalnum;
}
