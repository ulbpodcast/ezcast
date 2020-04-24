<?php

// CALLED BY cli_update_title.php in ezmanager

include_once __DIR__ . "/config.inc";
include_once __DIR__ .'/'.$encoding_pgm['file'];
include_once __DIR__ . "/lib_metadata.php";
include_once __DIR__ . "/lib_gd.php";
include_once __DIR__ . "/lib_ffmpeg.php";

global $processing_dir;
global $intros_dir;
global $php_cli_cmd;
global $ffmpegpath;
global $ffprobepath;

//intro default size
$default_intro_duration = 6;
$error = 0;

$processing = $argv[1];
$new_album = base64_decode($argv[2]);
$copyright = base64_decode($argv[3]);
$organization_name = base64_decode($argv[4]);
$id_album = base64_decode($argv[5]);
if (!isset($id_album) || $id_album == '') {
                                                    
    $id_album = base64_decode($argv[6]);
}

if (!isset($id_album) || $id_album == '') {
    $id_album = '';
}

$course_code_public = base64_decode($argv[7]);
if (!isset($course_code_public) || $course_code_public == "") {
    $course_code_public = $id_album;
}

// Debug / Logs
print "\n------------------------ Data passed ------------------------\n";
print "\n processing_dir : ".$processing_dir."\n";
print "\n intros_dir : ".$intros_dir."\n";
print "\n php_cli_cmd : ".$php_cli_cmd."\n";
print "\n ffmpegpath : ".$ffmpegpath."\n";
print "\n ffprobepath : ".$ffprobepath."\n";
print "\n processing : ".$processing."\n";
print "\n new_album : ".$new_album."\n";
print "\n copyright : ".$copyright."\n";
print "\n organization_name : ".$organization_name."\n";

print "\n------------------------ récupération des métadatas------------------------\n";

$res = get_title_info($processing, "_metadata.xml", $title_assoc);  // put metadata in $title_assoc
if ($res) {
    print "\n metadata imported with success\n";
    print "\n metadata : \n";
    print("\n".var_dump($title_assoc)."\n");
} else {
    print "\n metadata import failed \n";
}
                                          

if (isset($title_assoc['add_title']) && $title_assoc['add_title'] != 'false' && $title_assoc['add_title'] != '') {
    print "\n------------------------ Title exists------------------------\n";
 
    $months = array("janvier", "février", "mars", "avril", "mai", "juin",
    "juillet", "août", "septembre", "octobre", "novembre", "décembre");
    $date = explode('_', $title_assoc['record_date']);
    $date_formated = $date[2].' '.$months[$date[1]-1].' '.$date[0]. ' '.$date[3];
    
    
    
    $title_assoc['date'] = $date_formated;
        
    $title_assoc['organization'] = $organization_name;
    $title_assoc['album'] = "[".$course_code_public."]".$new_album;
    $title_assoc['title'] = $course_code_public.' '.$title_assoc['title'];
    $title_assoc['copyright'] = $copyright;
    
    print "\n final title assoc : \n" ;
    print("\n".var_dump($title_assoc)."\n");

    $duration_intro = 0;
    $title_time = 8;
    $record_types = array("low_cam", "high_cam", "low_slide", "high_slide");
    print "\n record_types : \n" ;
    print("\n".var_dump($record_types)."\n");
    
    //create the files title.mov
    $error += create_file_title($processing, $title_assoc, $record_types);
    
    // Create the files with new title
    if (isset($title_assoc['title_time']) && $title_assoc['title_time'] != '') {
        $title_time = $title_assoc['title_time'];
    }
    
    if (isset($title_assoc['intro']) && $title_assoc['intro'] != "" && file_exists($intros_dir.'/'.$title_assoc['intro'].'/original_intro.mov')) {
        print "\n------------------------ Intro exists------------------------\n";
        if (isset($title_assoc['intro_time']) && $title_assoc['intro_time'] != '') {
            $duration_intro = $title_assoc['intro_time'];
        } else {
            if (file_exists($intros_dir.'/'.$title_assoc['intro'].'/original_intro.mov')) {
                $cmd = ' '.$ffprobepath.' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.$intros_dir.'/'.$title_assoc['intro'].'/original_intro.mov';
                $duration_intro = abs(floatval(shell_exec($cmd)));
            } else {
                $duration_intro = $default_intro_duration;
            }
        }
        $error += create_intro_parts($processing, $record_types, $duration_intro, $title_time, true);
        $error += create_list($processing, $record_types, true);
    } else {
       
        $error += create_intro_parts($processing, $record_types, $duration_intro, $title_time, false);
        $error += create_list($processing, $record_types, false);
    }
    

    for ($i = 0;$i<count($record_types);$i++) {
        if (is_dir($processing.'/'.$record_types[$i])) {
            if ($error == 0) {
                // Concat the results files
                print "\n------------------------ Concatenate file ".$record_types[$i]." ------------------------\n";
                $cmd = ' '.$ffmpegpath.' -f concat -safe 0 -i '.$processing.'/'.$record_types[$i].'/mylist.txt -c copy -y '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'.mov';
                exec($cmd, $cmdoutput, $returncode);
                print("\n Command : ".$cmd."\n");
                print(implode("\n", $cmdoutput));
                print("\n returncode : ".$returncode."\n");
                if (!$returncode) {
                    print("\n Concat with success \n");
                } else {
                    print("\n Concat Failed \n");
                }
            } else {
                print("\n Error uccureD, unable to  finish creation of new intro \n");
            }
            
            // delete temps files
            print "\n------------------------ delete files ".$record_types[$i]." ------------------------\n";
            $cmd = ' rm '.$processing.'/'.$record_types[$i].'/mylist.txt ; rm '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'1.mov ; rm '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'2.mov ; rm '.$processing.'/'.$record_types[$i].'/title.mov ; rm '.$processing.'/'.$record_types[$i].'/title.jpg  ';
            exec($cmd, $cmdoutput, $returncode);
            print("\n Command : ".$cmd."\n");
            print(implode("\n", $cmdoutput));
            print("\n returncode : ".$returncode."\n");
            if (!$returncode) {
                print("\n temps files deleted with success \n");
            } else {
                print("\n delete temps files Failed \n");
   
            }
        }
    }
}

// create filetitle.mov
function create_file_title($processing, $title_assoc, $record_types)
{
    global $video_high_transcoders;
    
    $err = 0;
    for ($i = 0;$i<count($record_types);$i++) {
        if (is_dir($processing.'/'.$record_types[$i])) {
            print("\n------------------------ file title Creation ".$record_types[$i]."------------------------\n");
            // Find ratio  (get the metadata file)
            $meta = metadata2assoc_array($processing.'/'.$record_types[$i].'/_metadata.xml');
            $height = $meta['height'];
            $width = $meta['width'];

            if (isset($height) && $height != 0) {
                $ratio = $width / $height;
                $ratio = (abs($ratio - 1.77) <= abs($ratio - 1.33)) ? '16:9' : '4:3';
            } else {
                $ratio = '16:9';
            }
            
            if (isset($video_high_transcoders[$ratio][$width]) && $video_high_transcoders[$ratio][$width] != ''  && isset($title_assoc)) {
                $title_file = generate_new_title($processing.'/'.$record_types[$i], $video_high_transcoders[$ratio][$width], $title_assoc);
            } else {
                $err++;
            }
            print("\n title file : ".$title_file."\n encoder : ".$video_high_transcoders[$ratio][$width]."\n ration   : ".$ratio." \n width : ".$width);
            print("\n meta for title creation : \n") ;
            print("\n".var_dump($meta)."\n");
            print("\n path to meta : " .$processing."/".$record_types[$i]."/_metadata.xml \n");
  
        }
    }
    return $err;
}

// create parts of the video
function create_intro_parts($processing, $record_types, $duration_intro, $title_time, $hasintro)
{
    global $ffmpegpath;
    $err = 0;
    for ($i = 0;$i<count($record_types);$i++) {
        if (is_dir($processing.'/'.$record_types[$i])) {
            if ($hasintro) {
                $cmd = ' '.$ffmpegpath.' -i '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'.mov -vcodec copy -acodec copy -ss 00:00:00 -t '.$duration_intro.' -y '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'1.mov  -vcodec copy -acodec copy -ss '.($duration_intro+$title_time).' -y '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'2.mov';
            } else {
                $cmd = ' '.$ffmpegpath.' -i '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'.mov -vcodec copy -acodec copy -ss '.($title_time).' -y '.$processing.'/'.$record_types[$i].'/'.$record_types[$i].'2.mov';
            }
            
            exec($cmd, $cmdoutput, $returncode);
            print "\n------------------------ Intro Creation ".$record_types[$i]."------------------------\n";
            print("\n Command : ".$cmd."\n");
            print("output: ".implode("\n", $cmdoutput)."\n");
            print(" returncode : ".$returncode."\n");
            if (!$returncode) {
                print("\n Create intro parts with success \n");
            } else {
        
                print("\n Create intro parts Failed \n");
                $err++;
            }
        }
    }
    return $err;
}

// create a txt file to refer the file to concatenate
function create_list($processing, $record_types, $hasintro)
{
    $err = 0;
    for ($i = 0;$i<count($record_types);$i++) {
        if (is_dir($processing.'/'.$record_types[$i]) && file_exists($processing."/".$record_types[$i]."/title.mov")) {
            if ($hasintro && file_exists($processing."/".$record_types[$i]."/".$record_types[$i]."2.mov") && file_exists($processing."/".$record_types[$i]."/".$record_types[$i]."1.mov")  && file_exists($processing."/".$record_types[$i]."/title.mov")) {
                $cmd = " printf \"%s\n\" \"file '".$processing."/".$record_types[$i]."/".$record_types[$i]."1.mov'\" \"file '".$processing."/".$record_types[$i]."/title.mov'\" \"file '".$processing."/".$record_types[$i]."/".$record_types[$i]."2.mov'\" > ".$processing."/".$record_types[$i]."/mylist.txt";
            } elseif (file_exists($processing."/".$record_types[$i]."/title.mov") && file_exists($processing."/".$record_types[$i]."/".$record_types[$i]."2.mov")) {
                                                                                                                                                         
                $cmd = " printf \"%s\n\" \"file '".$processing."/".$record_types[$i]."/title.mov'\" \"file '".$processing."/".$record_types[$i]."/".$record_types[$i]."2.mov'\" > ".$processing."/".$record_types[$i]."/mylist.txt";
            } else {
        
                print("\n Create list Failed \n");
                $err++;
            }
            exec($cmd, $cmdoutput, $returncode);
            print "\n------------------------ List Creation ".$record_types[$i]."------------------------\n";
            print("\n Command : ".$cmd."\n");
            print(implode("\n", $cmdoutput));
            print("\n returncode : ".$returncode."\n");
            if (!$returncode) {
                print("\n Created list with success \n");
            } else {
         
                print("\n Create list Failed \n");
                $err++;
            }
        }
    }
    return $err;
}
