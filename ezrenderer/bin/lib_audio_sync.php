<?php
// generate audio files
function get_wav_from_video($input, $output/*,$asset_name*/)
{
    global $ffmpegpath;
    //Try to create audio file. log errors
    exec($ffmpegpath." -i ".$input." -map 0:1 -acodec pcm_s16le -ac 2 -ar 44100 ".$output, $err, $return);

    return $return;
}

// Synchronize vidéos

function sync_video($movies_path)
{
    global $ffmpegpath;
    global $ffprobepath;
    global $praatpath;
    global $basedir;
    $campath='';
    $slidepath='';
    
    $campath=$movies_path.'/cam.mov';
    $slidepath=$movies_path.'/slide.mov';
        
    // create audio files and log if error
    if ($slidepath!='' && !file_exists($slidepath.".wav")) {
        $return=get_wav_from_video($slidepath, $slidepath.".wav");
        if ($return!= 0) {
            print "Audio creation of ".$slidepath.".wav from ".$slidepath." failed. \n";
            return;
        } else {
            print "Audio creation of ".$slidepath.".wav from ".$slidepath." SUCCEED. \n";
        }
    }
    
    if ($campath!='' && !file_exists($campath.".wav")) {
        $return=get_wav_from_video($campath, $campath.".wav", $asset_name);
        if ($return!= 0) {
            print "Audio creation of ".$campath.".wav from ".$campath." failed. \n";
            return;
        } else {
            print "Audio creation of ".$campath.".wav from ".$campath." SUCCEED. \n";
        }
    }
    
    // synchronize the two video
    if (file_exists($campath.".wav") && file_exists($slidepath.".wav") && file_exists($slidepath) && file_exists($campath)) {
        
        // find decallage
        // depending the version of praat: add --run
        // $cmd=$praatpath." --run $basedir/bin/crosscorrelate.praat ".$slidepath.".wav ".$campath.".wav";
        $cmd=$praatpath." $basedir/bin/crosscorrelate.praat ".$slidepath.".wav ".$campath.".wav";
        $diff_time_string=shell_exec($cmd);
        if (!is_null($diff_time_string)) {
            $diff_time=abs(floatval($diff_time_string));
        } else {
            print "Get diff_time between the two audio files failed.  cmd: ".$cmd." \n";
        }
        
     
        // duration of Cam video
        $cmd=$ffprobepath.' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.$campath;
        $duration_string=shell_exec($cmd);
        if (!is_null($duration_string)) {
            $duration=abs(floatval($duration_string));
        } else {
            print "Get duration of ".$campath." failed.  cmd: ".$cmd." \n";
        }

        // Cut correctly the two video to synchronize
        if (!is_null($duration) && !is_null($diff_time) && ($duration-$diff_time)>0 && $diff_time<20) {
                
             
            // create new temp files
            $cmd=$ffmpegpath.' -ss '.$diff_time.' -i '.$slidepath.' -vcodec copy -acodec copy '.$movies_path.'/slidetemp.mov';
            exec($cmd, $err, $return);
            if ($return!= 0) {
                print "creation of ".$movies_path."/slidetemp.mov failed. \n";
            }

            $cmd=$ffmpegpath.' -ss 0 -i '.$campath.' -vcodec copy -acodec copy -t '.($duration-$diff_time).' '.$movies_path.'/camtemp.mov';
            exec($cmd, $err, $return);
            if ($return!= 0) {
                print "creation of ".$movies_path."/camtemp.mov failed. \n";
            }
             
            // replace cam.mov & slide.mov with the synchronised temp files
            exec('cp '.$movies_path.'/slidetemp.mov '.$slidepath, $err, $return);
            if ($return!= 0) {
                print "Move ".$slidepath." from ".$movies_path."/slidetemp.mov failed. \n";
            }
            exec('cp '.$movies_path.'/camtemp.mov '.$campath, $err, $return);
            if ($return!= 0) {
                print "Move ".$campath." from ".$movies_path."/camtemp.mov failed. \n";
            }
            
            print "AUDIO_SYNCHRONISATION SUCCEED \n";
        }
        
        // Delete audio files
        exec('rm '.$campath.'.wav ', $err, $return);
        if ($return!= 0) {
            print "DELETE ".$campath.".wav failed. \n";
        }
        exec('rm '.$slidepath.'.wav ', $err, $return);
        if ($return!= 0) {
            print "DELETE ".$slidepath.".wav failed. \n";
        }
    }
}
