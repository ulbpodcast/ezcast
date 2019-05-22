
<?php

/*
 * EZCAST EZrenderer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Antoine Dewilde
 *            Thibaut Roskam
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
 *
 */
/**
 * This program processes a recording
 */
// Starting time
$t0 = time();

include_once __DIR__ . "/config.inc";
include_once __DIR__ .'/'.$encoding_pgm['file'];
include_once __DIR__ . "/lib_metadata.php";
include_once __DIR__ . "/lib_ffmpeg.php";
include_once __DIR__ . "/lib_gd.php";
include_once __DIR__ . "/lib_audio_sync.php";

if ($argc != 2) {
    echo "usage: " . $argv[0] . " <directory_name>\n";
    echo "        where <directory_name> is the directory name in $downloaded_dir containing toprocess.xml and titlemeta.xml xml description files\n";
    echo "        The command generates a movie with the right intro (given in toprocess.xml), a custom title (info in titlemeta.xml), the video itself (from toprocess.xml) and a closing credits (from toprocess.xml)\n";
    exit(1);
}

// move from download to processing
$downloaded = $downloaded_dir . '/' . $argv[1];
$processing = $processing_dir . '/' . $argv[1];
rename($downloaded, $processing);
$processed = $processed_dir . '/' . $argv[1];
$fail = $failed_dir . '/' . time() . $argv[1];
$postedit = false;
//test if the cutlist.json existe and therefor taking the postedit way
$cutlist_file=$processing."/_cutlist.json";
if (file_exists($cutlist_file)) {
    $postedit = true;
}

print "\n//////////////////////////////// START RENDERING /////////////////////////////////////////////";
print "\nRunning intro_title_movie.php on: $processing\n";

print "\n------------------------ get processing info ----------------------\n";

$toprocess_assoc = metadata2assoc_array($processing . "/toprocess.xml");
$toprocess_assoc_log=print_r($toprocess_assoc , TRUE);

if (isset($toprocess_assoc['add_title']))
    $add_title = $toprocess_assoc['add_title'];
else
    $add_title = $default_add_title;

if (!isset($toprocess_assoc["ratio"]) || $toprocess_assoc["ratio"] == '')
    $toprocess_assoc["ratio"] = 'auto';

// new assoc array for status update and return info
$processing_assoc['status'] = "processing intro-title-movie";
$processing_assoc['render_pid'] = getmypid();
$res = assoc_array2metadata_file($processing_assoc, $processing . '/processing.xml');
if (!$res)
    myerror("couldnt write to $processing/processing.xml");

// get the path to movies
$working_vids = array(
    'cam' => $processing . '/cam.mov',
    'slide' => $processing . '/slide.mov',
    'audio' => $processing . '/audio.mp3',
);
if ($postedit) {
    if (isset($toprocess_assoc['processed_slide'])) {
        $working_vids['slide'] = $processing . substr($toprocess_assoc['processed_slide'], strrpos($toprocess_assoc['processed_slide'], '/'));
    }
    if (isset($toprocess_assoc['processed_cam'])) {
        $working_vids['cam'] = $processing . substr($toprocess_assoc['processed_cam'], strrpos($toprocess_assoc['processed_cam'], '/'));
    }
    if (isset($toprocess_assoc['processed_audio'])) {
        $working_vids['audio'] = $processing . substr($toprocess_assoc['processed_audio'], strrpos($toprocess_assoc['processed_audio'], '/'));
    }

} else {
    if (isset($toprocess_assoc['original_slide'])) {
        $working_vids['slide'] = $processing . substr($toprocess_assoc['original_slide'], strrpos($toprocess_assoc['original_slide'], '/'));
    }
    if (isset($toprocess_assoc['original_cam'])) {
        $working_vids['cam'] = $processing . substr($toprocess_assoc['original_cam'], strrpos($toprocess_assoc['original_cam'], '/'));
    }
    if (isset($toprocess_assoc['original_audio'])) {
        $working_vids['audio'] = $processing . substr($toprocess_assoc['original_audio'], strrpos($toprocess_assoc['original_audio'], '/'));
    }
}




if($enable_audio_sync){
	print "\n------------------------ Audio Syncronisation ----------------------\n";
	sync_video($processing);
}

if (!file_exists($working_vids['cam']))
    unset($working_vids['cam']);
if (!file_exists($working_vids['slide']))
    unset($working_vids['slide']);
if (!file_exists($working_vids['audio']))
    unset($working_vids['audio']);

// read the title meta file and validate its content
print "\n------------------------ get title info ------------------------\n";
$res = get_title_info($processing, "title.xml", $title_assoc);

//fwrite(fopen('./'.time().'.dump_input', 'w'), print_r($title_assoc, true));

if ($postedit) {
    print "\n------------------------ processing the json to a cut array ------------------------\n";

    $cutlist_array=[];
    $startime=0;
    $duration=0;
    $cmd;
    $campath;
    if (isset($working_vids['cam'])) {
        $campath=$working_vids['cam'];
    }else{
        $campath=$working_vids['slide'];
    }
    $cmd = $ffprobepath.' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.$campath;
    $duration_string = shell_exec($cmd);
    if (!is_null($duration_string)){
        $duration = abs(floatval($duration_string));
    }
    else{
        print "Get duration of ".$campath." failed.  cmd: ".$cmd. PHP_EOL ;
    }

    //get the json to an array
    $jsonStr=file_get_contents($cutlist_file);
    try {

            $stdClass=json_decode($jsonStr);
        } catch (Exception $e) {
            error_print_message('cutarray not a well formatted JSON');
            die;
        }
    $cutArray=get_object_vars($stdClass)['cutArray'];
    if (isset($cutArray)&&count($cutArray)!=0) {
        if ($cutArray[0][0]!=0) {
            $tmp_array=[];
            array_push($tmp_array,$startime);
            array_push($tmp_array,$cutArray[0][0]);
            array_push($cutlist_array,$tmp_array);

        }
        for ($i=1; $i < count($cutArray); $i++) {
            $tmp_array=[];
            array_push($tmp_array,$cutArray[$i-1][1]);
            array_push($tmp_array,$cutArray[$i][0]);
            array_push($cutlist_array,$tmp_array);

        }
        if ($cutArray[count($cutArray)-1][1]!=round($duration,2)) {
            $tmp_array=[];
            array_push($tmp_array,$cutArray[count($cutArray)-1][1]);
            array_push($tmp_array,$duration);
            array_push($cutlist_array,$tmp_array);

        }
    }
    print("Value of the cutlist_array : \n");
    print_r($cutlist_array , true);
}



// handle slide movie combine intro title and movie and encode them in 'high' and 'low' flavors
$types = array('slide', 'cam','audio');
$working_vids_qtinfo = array();
foreach ($types as $type) {
    if (isset($working_vids[$type])) {
        if (!isset($working_vids_qtinfo) || !isset($working_vids_qtinfo[$type])) {
            $working_vids_qtinfo[$type] = array();
            if (movie_qtinfo($working_vids[$type], $working_vids_qtinfo[$type]))
                myerror('couldn\'t get info for movie ' . $working_vids[$type]);
        }
        //save original movie info
        if ($postedit) {
            assoc_array2metadata_file($working_vids_qtinfo[$type], $processing . "/processed_{$type}_qtinfo.xml");

        } else {
            assoc_array2metadata_file($working_vids_qtinfo[$type], $processing . "/original_{$type}_qtinfo.xml");
        }

        print "\n====================== [START] Combines intro - title - movie - credits and encodes them in HD and LD (slide) ========================\n\n";
        if ($postedit) {
            itm_intro_title_movie($type, $working_vids[$type], $title_assoc, $toprocess_assoc['intro_movie'], $add_title, $toprocess_assoc['credits_movie'],$processing ,$cutlist_array ,true);
        }else {
            itm_intro_title_movie($type, $working_vids[$type], $title_assoc, $toprocess_assoc['intro_movie'], $add_title, $toprocess_assoc['credits_movie']);
        }        //itm_intro_title_movie($type, $originals[$type], $title_assoc, $toprocess_assoc['intro_movie'], $add_title);
        print "======================= [END] Combines intro - title - movie - credits and encodes them in HD and LD (slide) ===========================\n\n";
    }
}
//die();

processing_status('processed');

print "\n//////////////////////////////// PROCESSING DONE /////////////////////////////////////////////\n";
$t0 = time() - $t0;
print "\nRendering took $t0 seconds \n";


print "\n//////////////////////////////// MOVE TO PROCESSED /////////////////////////////////////////////\n";
if (!rename($processing, $processed)) {
    // already processed? Rename old one and replace it
    $ok = rename($processed, $processed . uniqid());
    $ok = $ok && rename($processing, $processed);
    if(!$ok)
        exit(2);
}

//cleanup
$blacklist = array(
    'annotated_movie.mov',
    'cam_transcoded.mov',
    'slide_transcoded.mov',
    'output_ref_movie.mov',
    'title.mov',
    'title.jpg',
    'transcoded_intro.mov',
    'transcoded_credits.mov',
);

foreach ($blacklist as $file) {
   unlink($processed . '/' . $file);
}

//log remaining file in processed directory
$file_list=scandir($processed);
print "\n//////////////////////////////// remaining file in processed directory /////////////////////////////////////////////\n";
foreach ($file_list as $file) {
    print "- $file \n";
}
print "\n//////////////////////////////// end of the list /////////////////////////////////////////////\n";


exit(0); //quit successfully

// choose intro or outro movie
//returns false on failure
function choose_movie($aspectRatio, $movies_dir, $movie_name, $movies_list, $width, $height) {

    switch ($aspectRatio) {
        case "16:9":
        case "16:10":
        case "3:2":
        case "4:3":
        case "5:3":
        case "5:4":
        case "8:5":
            $movie = $movies_dir . "/$movie_name" . "/" . $movies_list[$aspectRatio];
            break;
        default :
            if ($height && $width) {
                $ratio = $width / $height;
                $aspectRatio = (abs($ratio - 1.77) <= abs($ratio - 1.33)) ? '16:9' : '4:3';
            }
            $movie = $movies_dir . "/$movie_name" . "/" . $movies_list[$aspectRatio];
            break;
    }

    if (!is_file($movie)) {
        print "choose_movie: file $movie did not exists, use default instead" . PHP_EOL;
        $movie = $movies_dir . "/$movie_name" . "/" . $movies_list['default'];
    }

    return $movie;
}

/**
 * @global type $procdirpath
 * @global string $intro_dir
 * @global type $toprocess_assoc
 * @global string $processing
 * @global type $processing_dir
 * @global type $titleqtz
 * @global type $tempdir
 * @global type $superhigh_encoder
 * @global type $high_encoder
 * @global type $low_encoder
 * @param string $camslide
 * @param path $moviein
 * @param assoc_array $title_assoc description of title to add (or false if no title)
 * @param string $intro name of intro file (or empty string if no intro needed)
 * @param bool $add_title
 * @param string $credits name of credits file (or empty string if no intro needed)
 * @abstract process movie with addition of intro, outro and title if present.
 */
function itm_intro_title_movie($camslide, $moviein, &$title_assoc, $intro, $add_title, $credits, $path='', $cutlist_array='',$postedit=false) {
    global $processing, $intros_dir, $credits_dir, $toprocess_assoc, $processing, $working_vids_qtinfo, $intro_movies, $credits_movies, $imageAudioFilePath,$enable_render_audio_from_video,$enableMimeTypeCheck,$video_mimeTypes,$audio_mimeTypes,$enable_postedit;
    if ($postedit) {
        print "*************************************************************************" . PHP_EOL .
                "Starting to cut the processed movie" . PHP_EOL .
                "*************************************************************************" . PHP_EOL;
        print_r($cutlist_array ,true);
        $files_to_edit = array();
            // cuts the processeds assets in multiple parts

        if (isset($cutlist_array) && count($cutlist_array) != 0) {
            mkdir($path . '/tmpdir');
            movie_cut($path, $moviein, $cutlist_array,0,$postedit);
            $movie_array = array();
            $dir = new DirectoryIterator($path . '/tmpdir');
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    $movie_array[] = $path . '/tmpdir/' . $fileinfo->getFilename();
                }
            }
            // $movie_array=sort($movie_array);
            $sorted_movie_array = array();
            foreach($movie_array as $index => $array_path){
                $done=false;
                $true_index=intval(get_str_btw_str($array_path,"part-",".mov"));
                if (!$done&&count($sorted_movie_array)==0) {
                    array_push($sorted_movie_array,$array_path);
                    $done=true;
                }
                if (!$done&&$true_index<intval(get_str_btw_str($sorted_movie_array[0],"part-",".mov"))) {
                    array_unshift($sorted_movie_array,$array_path);
                    $done=true;
                }
                if (!$done&&$true_index>intval(get_str_btw_str($sorted_movie_array[count($sorted_movie_array)-1],"part-",".mov"))) {
                    array_push($sorted_movie_array,$array_path);
                    $done=true;
                }
                if (!$done) {
                    for ($i=1; $i < count($sorted_movie_array); $i++) {
                        if (!$done&&$true_index<intval(get_str_btw_str($sorted_movie_array[$i],"part-",".mov"))) {
                            array_insert($sorted_movie_array, $i, $array_path);
                            $done=true;
                        }
                    }
                }
            }
            movie_join_array($sorted_movie_array, $path . '/transcoded_' . $camslide . '.mov' );
            exec("rm -rf " . $path . '/tmpdir');
            exec("rm -rf " . $path . '/*count*');
        } else {
            $ext = file_extension_get($moviein);
            copy($moviein, $path . '/transcoded_' . $camslide .'.'. $ext['ext']);
        }

        $moviein=$path . '/transcoded_' . $camslide . '.mov';

    }
    if ( $enableMimeTypeCheck && ($toprocess_assoc["record_type"]!='audio') && !in_array(mime_content_type($moviein),$video_mimeTypes)) {
        myerror("mimetypeExcepted not found", false);
        exit(1);
    }
    if ( $enableMimeTypeCheck && $toprocess_assoc["record_type"]=='audio' && !in_array(mime_content_type($moviein),$audio_mimeTypes)) {
        myerror("mimetypeExcepted not found", false);
        exit(1);
    }
    $qtinfo = $working_vids_qtinfo[$camslide];
//    generate video from sound submited and image
    if ($toprocess_assoc["record_type"] == "audio" ) {
        $movieout = $processing .'/cam.mp4';
        $audioin = $moviein;
        if (generateVideoFromSound($audioin, $movieout, $imageAudioFilePath)) {
//          add some metadata top toprocess.xml
            $moviein = $movieout;
            $camslide = 'cam';
            $intro = '';
            $credits = '';
            $add_title = 'false';
            $toprocess_assoc["record_type"] = "cam";
            $toprocess_assoc["has_audio"] = "true";
            assoc_array2metadata_file($toprocess_assoc,$processing . "/toprocess.xml");
            $path_parts = pathinfo($moviein);
            $audioout = $path_parts['dirname'].'/audio_'.$camslide.'.mp3';
            if (getAudioFromVideo($moviein, $audioout)) {
//              Indicate that there is a audio file for ezplayer
                $toprocess_assoc["has_audio"] = "true";
                assoc_array2metadata_file($toprocess_assoc,$processing . "/toprocess.xml");
            }
        }
    }
    else if ($enable_render_audio_from_video) {
        $path_parts = pathinfo($moviein);
        $audioout = $path_parts['dirname'].'/audio_'.$camslide.'.mp3';
        if (getAudioFromVideo($moviein, $audioout)) {
//            indicate that there is a audio file for ezplayer
            $toprocess_assoc["has_audio"] = "true";
            assoc_array2metadata_file($toprocess_assoc,$processing . "/toprocess.xml");
        }
    }

    if (isset($toprocess_assoc['ratio']) && $toprocess_assoc['ratio'] != 'auto')
        $qtinfo["aspectRatio"] = $toprocess_assoc['ratio'];

    $high_movieout = $processing . '/high_' . $camslide . '.mov';
    $high_qtinfo_path = $processing . '/high_' . $camslide . '_qtinfo.xml';
    $low_movieout = $processing . '/low_' . $camslide . '.mov';
    $low_qtinfo_path = $processing . '/low_' . $camslide . '_qtinfo.xml';
    $processed_movieout = $processing . '/processed_' . $camslide . '.mov';
    $processed_qtinfo_path = $processing . '/processed_' . $camslide . '_qtinfo.xml';

    $qualities[] = ($toprocess_assoc['super_highres'] == 'on') ? 'superhigh' : 'high';
    $qualities[] = 'low';

    foreach ($qualities as $quality) {
        $t1 = time();
        print "\n------------------------ transcoding $moviein in $quality quality ------------------------\n";
        $encoder = '';
        // determines the appropriate encoder to use for the desired quality and transcodes the video
        $transcoded_movie = itm_handle_movie($moviein, $camslide, $quality, $toprocess_assoc['ratio'], $encoder);
        $dt = time() - $t1;
        print "\n------------------------ encoding $transcoded_movie ($quality) took $dt seconds ------------------------\n";

        //copying the high output before intro to processed occurence
        if (( $quality == 'high' || $quality == 'superhigh' ) && $enable_postedit && !$postedit) {
            print "\n------------------------ copying $transcoded_movie to processed occurence ------------------------\n";
            safe_copy($transcoded_movie , $processing . '/processed_' . $camslide . '.mov');
            // relocates the MOOV atom in the video to allow playback to begin before the file is completely downloaded
            $res = movie_moov_atom($annotated_movie, $processed_movieout);
            if ($res)
                myerror("couldn't relocate MOOV atom for movie $processed_movieout");
            //get qtinfo for high movie and save them
            $res = movie_qtinfo($processed_movieout, $processed_qtinfo);
            if ($res)
                myerror("couldn't get info for movie $processed_movieout");
            $res = assoc_array2metadata_file($processed_qtinfo, $processed_qtinfo_path);

        }

        $movies_to_join = array(); //list of movie parts to merge (for intro-title-movie))
        //check if we have an intro movie to prepend
        if (trim($intro) != "") {
            //encodes original intro movie using the same encoder as for the video
            $intro_movie = choose_movie($qtinfo["aspectRatio"], $intros_dir, $intro, $intro_movies, $qtinfo["width"], $qtinfo["height"]);

            $transcoded_intro = $processing . "/transcoded_intro.mov";

            print "\n----------------- transcoding intro with encoder $encoder ---------------------\n\n";
            $res = safe_movie_encode($intro_movie, $transcoded_intro, $encoder, false);
            if($res == false)
            {
                print "Adding intro $intro_movie to join array. Res: $res" . PHP_EOL;
                array_push($movies_to_join, $transcoded_intro);
            } else
                print "\n\nSkipping $quality intro encoder: $encoder\n";

            print "\n\n$quality intro encoder: $encoder\n";
        }

        //if we have a title to add, generate it
        if ($add_title != 'false') {
            //generate title movie using the same encoder as for the video
            print "\n------------------------ generating title ------------------------\n";
            $title_movieout = $processing . "/title.mov";
            $title_movieout_temp = $processing . "/title_temp.mov";
            $title_image = $processing . "/title.jpg";

            $encoder_values = explode('_', $encoder);
            $resolution_values = explode('x', $encoder_values[2]);
            $width = $resolution_values[0];
            $height = $resolution_values[1];
            $ratio = explode(":", $qtinfo["aspectRatio"]);
            if ($ratio[0] > 0 && $ratio[1] > 0)
                $height = $resolution_values[0] * $ratio[1] / $ratio[0];

            processing_status("title $camslide");
            $res = gd_image_create($title_assoc, $width, $height, $title_image);
            if (!$res || !file_exists($title_image)) {
                myerror("couldn't generate title $title_image", false);
                $title_image = false;
            }
            if($title_image) {
            //   $res = movie_title($title_movieout, $title_assoc, $encoder, 8); //duration is hardcoded to 8
                $res = movie_title_from_image($title_movieout_temp, $title_image, $encoder);
                $res2 = safe_movie_encode($title_movieout_temp, $title_movieout, $encoder, false);
                if ($res)
                    myerror("couldn't generate title $title_movieout", false);
                else
                    array_push($movies_to_join, $title_movieout);
            }
        }

//        die();
        //join main movie
        array_push($movies_to_join, $transcoded_movie);

        //if we have a outro to add, generate it
        if (trim($credits) != "") {
            //encodes original credits movie using the same encoder as for the video
            $credits_movie = choose_movie($qtinfo["aspectRatio"], $credits_dir, $credits, $credits_movies, $qtinfo["width"], $qtinfo["height"]);
            $transcoded_credits = $processing . "/transcoded_credits.mov";

            print "\n----------------- transcoding credits with encoder $encoder ---------------------\n\n";
            if(safe_movie_encode($credits_movie, $transcoded_credits, $encoder, false) == false)
                array_push($movies_to_join, $transcoded_credits);
            else
                print "\n\nSkipping $quality credits encoder: $encoder\n";

            print "\n\n$quality credits encoder: $encoder\n";
        }

        //add the real movie part to intro, title and credits if they are present (intro , title, input_movie, credits)
        if (count($movies_to_join) > 1) {
            //var_dump($movies_to_join);
            $outputrefmovie = $processing . "/output_ref_movie.mov";
            print "\n------------------------ joining intro title movie parts ---------------------\n";
            var_dump($movies_to_join);
            $res = movie_join_array($movies_to_join, $outputrefmovie);
            if ($res)
                myerror("couldn't join movie $outputrefmovie. Result: $res");
        } else {
            //movie without intro nor title so no join needed
            $outputrefmovie = $transcoded_movie;
        }
        $annotated_movie = $processing . '/annotated_movie.mov';
        //set title, author,... in movie
        print "\n\n------------------------ Annotate $quality $camslide ---------------------\n";
        $res = movie_annotate($outputrefmovie, $annotated_movie, $title_assoc['title'], $title_assoc['date'], $title_assoc['description'], $title_assoc['author'], $title_assoc['keywords'], $title_assoc['copyright']);
        if ($res) {
            myerror("couldn't annotate movie $outputrefmovie. Res: $res", false);
            $annotated_movie = $outputrefmovie; //skip and try to continue anyway with the previous video file
        }

        print "\n\n------------------------ Relocate MOOV atom $quality $camslide ---------------------\n";
        if ($quality != 'low') {
            // relocates the MOOV atom in the video to allow playback to begin before the file is completely downloaded
            $res = movie_moov_atom($annotated_movie, $high_movieout);
            if ($res)
                myerror("couldn't relocate MOOV atom for movie $high_movieout");
            //get qtinfo for high movie and save them
            $res = movie_qtinfo($high_movieout, $high_qtinfo);
            if ($res)
                myerror("couldn't get info for movie $high_movieout");
            $res = assoc_array2metadata_file($high_qtinfo, $high_qtinfo_path);
        } else {
            // relocates the MOOV atom in the video to allow playback to begin before the file is completely downloaded
            $res = movie_moov_atom($annotated_movie, $low_movieout);
            if ($res)
                myerror("couldn't relocate MOOV atom for movie $low_movieout");
            //get qtinfo for high movie and save them
            $res = movie_qtinfo($low_movieout, $low_qtinfo);
            if ($res)
                myerror("couldn't get info for movie $low_movieout");
            $res = assoc_array2metadata_file($low_qtinfo, $low_qtinfo_path);
        }
    }
}

/**
 * get a picture associated with each chapter transition and put it in $destdir
 * @param string $moviein absolute path to movie file
 * @param string $chapterfile absolute path to chapter xml file
 * @param string $destdir path to directory that will be filled with png chapter images
 * @return bool
 */
function itm_get_chapters_images($moviein, $chapterfile, $destdir) {
    @ $xml = simplexml_load_file($chapterfile);
    if ($xml === false)
        return false;
    $idx = 1;
    foreach ($xml->array->dict as $key) {
        $pictureout = $destdir . '/chapter' . $idx . '.png';
        $ok = movie_getposterimage($moviein, $pictureout, (float) $key->real);
        $idx+=1;
    }
    return true;
}

/**
 * saves the status of the processing
 * @global string $procdirpath path of the processing directory
 * @global array $processing_assoc info of current processing
 * @param string $status
 * @return bool
 */
function processing_status($status) {
    global $processing, $processing_assoc;

    $processing_assoc['status'] = $status;
    $res = assoc_array2metadata_file($processing_assoc, $processing . '/processing.xml');
    return $res;
}

/**
 *
 * @param string_path $title_meta_path
 * @param assoc_array $title_assoc
 * @return bool true on success
 * @desc load the title (xml) info and validate it. parameters should be in title,author,date,organization,copyright
 */
function get_processing_info($processing_info_path, $processing_filename, &$processing_assoc) {
    global $intros_dir, $credits_dir, $processing;
    $processing_assoc = metadata2assoc_array($processing_info_path . "/" . $processing_filename);
    if (!is_array($processing_assoc))
        myerror("processing info file read error $processing_info_path/$processing_filename\n");
    if (!is_dir($intros_dir . "/" . $processing_assoc['intro_movie']))
        myerror("intro_movie not found\n");
    if (!is_dir($credits_dir . "/" . $processing_assoc['credits_movie']))
        myerror("credits_movie not found\n");
    if (!is_file($processing . "/" . $processing_assoc['input_movie_file']))
        myerror("input_movie_file not found\n");
    return true;
}


/**
 * look at the movies, transcode them and return path to transcoded movies
 * @global string $processing
 * @global <type> $accepted_video_sizes
 * @global <type> $video_high_encoders
 * @global <type> $original_qtinfo
 * @global string $intro_dir
 * @param pathtomovie $movie
 * @param $camslide
 * @param string $quality if superhigh the movie keeps its original resolution,
 * if high the movie is transcoded to the nearest standard resolution,
 * if low the movie is transcoded to the lowest resolution
 * @param $encoder receives the encoder used to transcode the video
 * @return string path to transcoded movie or original movie
 */
function itm_handle_movie($movie, $camslide, $quality, $ratio, &$encoder) {
    global $processing, $accepted_video_sizes, $video_high_transcoders, $original_qtinfo;

    $qtinfo = $original_qtinfo[$camslide];
    $height = $qtinfo['height'];
    $width = $qtinfo['width'];
    $letterboxing = true;

    if (!isset($ratio) || $ratio == 'auto') {
        if ($height != 0) {
            $ratio = $width / $height;
            $ratio = (abs($ratio - 1.77) <= abs($ratio - 1.33)) ? '16:9' : '4:3';
        } else {
            $ratio = '16:9';
        }
    } else {
        $letterboxing = false;
    }

    //WARNING THIS CONDITION IS STRANGE BECAUSE OF THE LOW DEFINITION PARAMETERS IN CONFIG.... IF THE VIDEO IS NOT 16:9 OR 4/3 (phone for instance !!!!!
    if ($quality == 'high' || ($quality == 'low' && ($width / $height)!= (16/9) && ($width / $height)!= (4/3) ) ) {
        $vididx = 0;
        $count = count($accepted_video_sizes[$ratio]);
        while ($vididx < $count && $width > $accepted_video_sizes[$ratio][$vididx]) {
            $vididx += 1;
        }
        $vididx = ($vididx == $count) ? $vididx - 1 : $vididx;
        $good_width = $accepted_video_sizes[$ratio][$vididx];
        $encoder = $video_high_transcoders[$ratio][(string) $good_width];
        print "\nSize: ${width}x$height
               \nQuality: $quality
               \nGood_width: $good_width
               \nEncoder: $encoder\n";
    } else if ($quality == 'low') {
        $encoder = $video_high_transcoders[$ratio]['low'];
    } else {
        $encoder = $video_high_transcoders[$ratio]['super_highres'] . $width . 'x' . $height;
    }

    //we need to transcode
    processing_status("transcoding $camslide");
    print "\n----------------- [START] transcoding $camslide ---------------------\n\n";
    $movieout = $processing . "/{$camslide}_transcoded.mov";
    $res = safe_movie_encode($movie, $movieout, $encoder, $qtinfo, $letterboxing);
    if ($res != false)
        myerror("transcoding error with movie $movie encoder $encoder\n");
    print "\n----------------- [END] transcoding $camslide ---------------------\n";
    return $movieout;
}

function myerror($msg, $exit = true) {

    global $procdirpath;

    processing_status("ERROR");
    print "\n******************************** ERROR ********************************\n";
    fprintf(STDERR, "%s", $msg);
    print PHP_EOL;
    if($exit)
        exit(1); //return error code
}

/**
 * delete the contents of a directory recursively (BUT DOES NOT DELETE THE DIRECTORY ITSELF)
 * @param <type> $path
 * @param <type> $recursive
 */
function dir_empty($path, $recursive = false) {

    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != "..") {
            $filepath = $path . '/' . $file;
            $isdir = is_dir($path . '/' . $file);
            if (is_dir($path . '/' . $file)) {
                if ($recursive == true) {
                    dir_empty($path . "/" . $file, false); //recursively delete
                    rmdir($path . "/" . $file);
                }
            } else {
                print "unlink $filepath\n";
                unlink($path . '/' . $file);
            }
        }
    }
    return true;
}

function safe_copy($from, $to, $recursif = false) {

    $recursif ? $recursif = '-r ' : $recursif = '';
    processing_status("copy $from to $to dir");
    print "\n====================== [START] copy $from to $to ========================\n";
    $repeat = 50;
    do {
        $cmd = "cp $recursif $from $to";
        exec($cmd, $cmdoutput, $returncode);
        if ($returncode) {
            print "\n**************************";
            print "\n* ERROR COPY FILE: code $returncode *";
            print "\n**************************";
        }
        $repeat-=1;
    } while ($repeat > 0 && $returncode);
    if (!$returncode) {
        print "\nFile copied from $from to $to\n";
    }
    print "\n======================= [END] copy $from to $to ===========================\n";
    return (!$returncode ? true : false);
}

//return false on success, else an error message
function safe_movie_encode($moviein, $movieout, $encoder, $qtinfo, $letterboxing = true) {
    $repeat = 1;
    $res = false;
    do {
        $res = movie_encode($moviein, $movieout, $encoder, $qtinfo, $letterboxing);
        if ($res) {
            print "\n**************************";
            print "\n* ERROR ENCODE MOVIE [$repeat]:";
            print "\n* moviein: $moviein";
            print "\n* movieout: $movieout";
            print "\n* encoder: $encoder";
            print "\n* $res ";
            print "\n**************************";
        }
        $repeat+=1;
    } while ($res && $repeat < 10);

    print PHP_EOL . "safe_movie_encode returns $res" . PHP_EOL;
    return $res;
}

function get_str_btw_str($source,$start,$end){
    $start_len=strlen($start);
    $end_len=strlen($end);

    return substr($source,strrpos($source,$start)+$start_len,(strlen($source)-((strrpos($source,$start)+$start_len)+(strlen($source)-strrpos($source, $end)))));
}
function array_insert(&$array, $position, $insert)
{
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}
