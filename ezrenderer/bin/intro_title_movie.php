<?php

/*
 * EZCAST EZrenderer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
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

// get the program directory to fix a relative path include problem
$path = dirname($argv[0]);
if (trim($path) != "")
    $path.='/';

include_once "$path" . "config.inc";
include_once "$path" . $encoding_pgm['file'];
include_once "$path" . "lib_metadata.php";
include_once "$path" . "lib_gd.php";

if ($argc != 2) {
    echo "usage: " . $argv[0] . " <directory_path>\n";
    echo "        where <directory_path> is the path to a directory containing toprocess.xml and titlemeta.xml xml description files\n";
    echo "        The command generates a movie with the right intro (given in toprocess.xml), a custom title (info in titlemeta.xml) and the video itself (from toprocess.xml )\n";
    die;
}

// move from download to processing
$downloaded = $downloaded_dir . '/' . $argv[1];
$processing = $processing_dir . '/' . $argv[1];
rename($downloaded, $processing);
$processed = $processed_dir . '/' . $argv[1];
$fail = $failed_dir . '/' . time() . $argv[1];

print "\n//////////////////////////////// START RENDERING /////////////////////////////////////////////";
print "\nRunning intro_title_movie.php on: $processing\n";

print "\n------------------------ get processing info ----------------------\n";

$toprocess_assoc = metadata2assoc_array($processing . "/toprocess.xml");

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
$originals = array(
    'cam' => $processing . '/cam.mov',
    'slide' => $processing . '/slide.mov',
);

if (isset($toprocess_assoc['original_slide'])) {
    $originals['slide'] = $processing . substr($toprocess_assoc['original_slide'], strrpos($toprocess_assoc['original_slide'], '/'));
}
if (isset($toprocess_assoc['original_cam'])) {
    $originals['cam'] = $processing . substr($toprocess_assoc['original_cam'], strrpos($toprocess_assoc['original_cam'], '/'));
}


if (!file_exists($originals['cam']))
    unset($originals['cam']);
if (!file_exists($originals['slide']))
    unset($originals['slide']);

// read the title meta file and validate its content
print "\n------------------------ get title info ------------------------\n";
$res = get_title_info($processing, "title.xml", $title_assoc);

// handle slide movie combine intro title and movie and encode them in 'high' and 'low' flavors

$types = array('slide', 'cam');
$original_qtinfo = array();
foreach ($types as $type) {
    if (isset($originals[$type])) {
        if (!isset($original_qtinfo) || !isset($original_qtinfo[$type])) {
            $original_qtinfo[$type] = array();
            if (movie_qtinfo($originals[$type], $original_qtinfo[$type]))
                myerror('couldn\'t get info for movie ' . $originals[$type]);
        }
        //save original movie info
        assoc_array2metadata_file($original_qtinfo[$type], $processing . "/original_{$type}_qtinfo.xml");
        print "\n====================== [START] Combines intro - title - movie and encodes them in HD and LD (slide) ========================\n\n";
        itm_intro_title_movie($type, $originals[$type], $title_assoc, $toprocess_assoc['intro_movie'], $add_title);
        print "======================= [END] Combines intro - title - movie and encodes them in HD and LD (slide) ===========================\n\n";
    }
}

processing_status('processed');

print "\n//////////////////////////////// PROCESSING DONE /////////////////////////////////////////////\n";
$t0 = time() - $t0;
print "\nRendering took $t0 seconds \n";

print "\n//////////////////////////////// MOVE TO PROCESSED /////////////////////////////////////////////\n";
if (!rename($processing, $processed)) {
    // already processed ? something like that
    rename($processing, $fail);
} else {
    $blacklist = array(
        'annotated_movie.mov',
        'cam_transcoded.mov',
        'slide_transcoded.mov',
        'output_ref_movie.mov',
        'title.mov',
        'title.jpg',
        'transcoded_intro.mov',
    );

    foreach ($blacklist as $file) {
        unlink($processed . '/' . $file);
    }
}

exit(0); //quit successfully

/**
 *
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
 * @param string $intro name of intro directory (or  empty string if no introp needed)
 * @param bool $chapterize
 * @abstract process movie with addition of intro and title if present.
 */
function itm_intro_title_movie($camslide, $moviein, &$title_assoc, $intro, $add_title) {
    global $processing, $intros_dir, $toprocess_assoc, $processing, $original_qtinfo, $intro_movies;


    $qtinfo = $original_qtinfo[$camslide];

    if (isset($toprocess_assoc['ratio']) && $toprocess_assoc['ratio'] != 'auto')
        $qtinfo["aspectRatio"] = $toprocess_assoc['ratio'];

    $high_movieout = $processing . '/high_' . $camslide . '.mov';
    $high_qtinfo_path = $processing . '/high_' . $camslide . '_qtinfo.xml';
    $low_movieout = $processing . '/low_' . $camslide . '.mov';
    $low_qtinfo_path = $processing . '/low_' . $camslide . '_qtinfo.xml';

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

        $movies_to_join = array(); //list of movie parts to merge (for intro-title-movie))
        //check if we have an intro movie to prepend
        if (trim($intro) != "") {
            //encodes original intro movie using the same encoder as for the video
            switch ($qtinfo["aspectRatio"]) {
                case "16:9":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['16:9'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "16:10":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['16:10'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "3:2":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['3:2'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "4:3":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['4:3'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "5:3":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['5:3'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "5:4":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['5:4'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                case "8:5":
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['8:5'];
                    if (!file_exists($intro_movie))
                        $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies['default'];
                    break;
                default : 
                    if ($qtinfo['height'] != 0) {
                        $ratio = $qtinfo['width'] / $qtinfo['height'];
                        $qtinfo["aspectRatio"] = (abs($ratio - 1.77) <= abs($ratio - 1.33)) ? '16:9' : '4:3';
                    }
                    $intro_movie = $intros_dir . "/$intro" . "/" . $intro_movies[$qtinfo["aspectRatio"]];
                    break;
            }

            $transcoded_intro = $processing . "/transcoded_intro.mov";

            print "\n----------------- transcoding intro with encoder $encoder ---------------------\n\n";
            safe_movie_encode($intro_movie, $transcoded_intro, $encoder, false);
            array_push($movies_to_join, $transcoded_intro);
            print "\n\n$quality intro encoder: $encoder\n";
        }
        //if we have a title to add, generate it
        if ($add_title != 'false') {
            //generate title movie using the same encoder as for the video
            print "\n------------------------ generating title ------------------------\n";
            $title_movieout = $processing . "/title.mov";
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
            if (!$res || !file_exists($title_image))
                myerror("couldn't generate title $title_image");
            //   $res = movie_title($title_movieout, $title_assoc, $encoder, 8); //duration is hardcoded to 8
            $res = movie_title_from_image($title_movieout, $title_image, $encoder);
            if ($res)
                myerror("couldn't generate title $title_movieout");

            array_push($movies_to_join, $title_movieout);
        }
        //add the real movie part to intro and title if they are present (intro , title, input_movie)
        if (count($movies_to_join) > 0) {
            //var_dump($movies_to_join);
            array_push($movies_to_join, $transcoded_movie);
            $outputrefmovie = $processing . "/output_ref_movie.mov";
            print "\n------------------------ joining intro title movie parts ---------------------\n";
            $res = movie_join_array($movies_to_join, $outputrefmovie);
            if ($res)
                myerror("couldn't join movie $outputrefmovie");
        } else {
            //movie without intro nor title so no join needed
            $outputrefmovie = $transcoded_movie;
        }
        $annotated_movie = $processing . '/annotated_movie.mov';
        //set title, author,... in movie
        print "\n\n------------------------ Annotate $quality $camslide ---------------------\n";
        $res = movie_annotate($outputrefmovie, $annotated_movie, $title_assoc['title'], $title_assoc['date'], $title_assoc['description'], $title_assoc['author'], "ULB,podcast", $title_assoc['copyright']);
        if ($res)
            myerror("couldn't annotate movie $outputrefmovie");
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
    global $intros_dir, $processing;
    $processing_assoc = metadata2assoc_array($processing_info_path . "/" . $processing_filename);
    if (!is_array($processing_assoc))
        myerror("processing info file read error $processing_info_path/$processing_filename\n");
    if (!is_dir($intros_dir . "/" . $processing_assoc['intro_movie']))
        myerror("intro_movie not found\n");
    if (!is_file($processing . "/" . $processing_assoc['input_movie_file']))
        myerror("input_movie_file not found\n");
    return true;
}

/**
 *
 * @param string_path $title_meta_path
 * @param assoc_array $title_assoc
 * @return bool true on success
 * @desc load the title (xml) info and validate it. parameters should be in title,author,date,organization,copyright
 */
function get_title_info($title_meta_path, $title_filename, &$title_assoc) {
    if (!file_exists($title_meta_path . "/" . $title_filename)) {
        $title_assoc = false;
        return true; //no title file means no title to generate
    }
    $title_assoc = metadata2assoc_array($title_meta_path . "/" . $title_filename);
    if (!is_array($title_assoc))
        myerror("Title metadata file read error $title_meta_path/$title_filename\n");

    //check if we dont have any invalid properties
    $valid_title_elems = array("album", "title", "author", "date", "organization", "copyright");
    $badmeta = "";
    foreach ($title_assoc as $key => $value) {
        if (!in_array($key, $valid_title_elems)) {
            $badmeta.="'$key',";
        }
    }

    if ($badmeta != "") {
        $badmeta = "Error with metadata elements: " . $badmeta . "\n";
        myerror($badmeta);
    }

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

    if ($quality == 'high') {
        //find the  encoder to the nearest dimensions
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
    if ($res)
        myerror("transcoding error with movie $movie encoder $encoder\n");
    print "\n----------------- [END] transcoding $camslide ---------------------\n";
    return $movieout;
}

function myerror($msg) {

    global $procdirpath;

    processing_status("ERROR");
    print "\n******************************** ERROR ********************************\n";
    fprintf(STDERR, "%s", $msg);
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
    print "\n====================== [START] copy $from to $to ========================";
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
        print "\nFile copied from $from to $to";
    }
    print "\n======================= [END] copy $from to $to ===========================\n";
    return (!$returncode ? true : false);
}

function safe_movie_encode($moviein, $movieout, $encoder, $qtinfo, $letterboxing = true) {
    $repeat = 1;
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
    return $res;
}

?>
