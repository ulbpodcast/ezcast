<?php
// generate audio files
function get_wav_from_video($input, $output/*,$asset_name*/)
{
	global $ffmpegpath;	    
    //We need to cut the movie to have the correct audio file, otherwhise we have a key-frame issue who leeds to an error in the synchronisation operation.
    exec( $ffmpegpath.' -ss 00:00:00 -i '.$input.' -vcodec copy -acodec copy '.$input.'_temp1.mov', $err, $return);    
    exec($ffmpegpath." -ss 00:00:00 -i ".$input."_temp1.mov -map 0:1 -acodec pcm_s16le -ac 2 -ar 44100 ".$output, $err, $return);
	return $return;
}

// Synchronize vidéos

function sync_video($movies_path){
    global $ffmpegpath;
    global $ffprobepath;
    global $praatpath;
    global $basedir;
    global $copy_sound_from_slide_to_cam;

    $campath = '';
    $slidepath = '';
 
    $campath = $movies_path.'/cam.mov'; 
    $slidepath = $movies_path.'/slide.mov'; 
  
    // create audio files and log if error
    if ($slidepath != '' && !file_exists($slidepath.".wav")){
        $return = get_wav_from_video($slidepath, $slidepath.".wav");
        if ($return != 0){
            print "Audio creation of ".$slidepath.".wav from ".$slidepath." failed. ". PHP_EOL ;
            return;
        }
        else 
            print "Audio creation of ".$slidepath.".wav from ".$slidepath." SUCCEED. ". PHP_EOL ;
    }
	
    if ($campath != '' && !file_exists($campath.".wav")){
        $return = get_wav_from_video($campath, $campath.".wav");
        if ($return != 0){
            print "Audio creation of ".$campath.".wav from ".$campath." failed.". PHP_EOL ;
            return;
        }
        else 
            print "Audio creation of ".$campath.".wav from ".$campath." SUCCEED.". PHP_EOL ;
    }
	
    // synchronize the two video
    if (file_exists($campath.".wav") && file_exists($slidepath.".wav") && file_exists($slidepath) && file_exists($campath)){
		
        // find decallage
        // depending the version of praat: add --run
        $cmd = $praatpath." --run $basedir/bin/crosscorrelate.praat ".$slidepath.".wav ".$campath.".wav";
        //$cmd = $praatpath." $basedir/bin/crosscorrelate.praat ".$slidepath.".wav ".$campath.".wav";
        $diff_time_string = shell_exec($cmd);
        file_put_contents("/tmp/test1.txt","difftimeSting: ". $diff_time_string . PHP_EOL,FILE_APPEND);
        if (!is_null($diff_time_string)){
            $diff_time = abs(floatval($diff_time_string));                                                                                 
            print "diff_time between the two audio files : ".$diff_time_string." ". PHP_EOL ;	
        }
		
        else 
            print "Get diff_time between the two audio files failed.  cmd: ".$cmd." ". PHP_EOL ;		
	 
        // duration of Cam video
        $cmd = $ffprobepath.' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.$campath;	 
        $duration_string = shell_exec($cmd);
        if (!is_null($duration_string))
            $duration = abs(floatval($duration_string));        
        else 
            print "Get duration of ".$campath." failed.  cmd: ".$cmd. PHP_EOL ;

        // Cut correctly the two video to synchronize
        if (!is_null($duration) && !is_null($diff_time) && ($duration-$diff_time) >0 && $diff_time<20 ){				 
            // create new temp files
            $cmd = $ffmpegpath.' -ss '.$diff_time.' -i '.$slidepath.'  -vcodec copy -acodec copy  '.$movies_path.'/slidetemp.mov';
            exec($cmd,$err,$return);
            if ($return != 0) 
                print "creation of ".$movies_path."/slidetemp.mov failed.". PHP_EOL ;

            $cmd = $ffmpegpath.' -ss 00:00:00 -i '.$campath.' -vcodec copy -acodec copy -t '.($duration-$diff_time).' '.$movies_path.'/camtemp.mov';
            exec($cmd,$err,$return);
            print "DURATION :".$duration. " DIFTIME: ".$diff_time ; 

            if ($return != 0) 
                print "creation of ".$movies_path."/camtemp.mov failed. ". PHP_EOL ;
            // replace cam.mov & slide.mov with the synchronised temp files
            exec('cp '.$movies_path.'/slidetemp.mov '.$slidepath,$err,$return);
            if ($return != 0) 
                print "Move ".$slidepath." from ".$movies_path."/slidetemp.mov failed.". PHP_EOL ;
                                                                                           
			
            exec('cp '.$movies_path.'/camtemp.mov '.$campath,$err,$return);
            if ($return != 0)
                print "Move ".$campath." from ".$movies_path."/camtemp.mov failed. " .PHP_EOL ;            
            
            //use to replace the cam sound with the slide sound
            if ($copy_sound_from_slide_to_cam){
                $error = 0;
                //Copy Slide sound
                $cmd = $ffmpegpath.' -i '.$slidepath.' '.$movies_path.'/slidesound.mp3';
                exec($cmd,$err,$return);
                if ($return != 0) 
                    $error = 1;    
                
                //Delete cam sound
                $cmd = $ffmpegpath.' -i '.$campath.' -an '.$movies_path.'/camNosound.mov';
                exec($cmd,$err,$return);
                if ($return != 0) 
                    $error = 1;    
                
                //insert slide sound into cam movie
                $cmd = $ffmpegpath.' -i '.$movies_path.'/camNosound.mov'.' -i '.$movies_path.'/slidesound.mp3'. ' -shortest -y '.$campath;
                exec($cmd,$err,$return);
                if ($return != 0) {
                    $error = 1;   
                
                    exec('rm '.$movies_path.'/slidesound.mp3',$err,$return);
                    if ($return != 0) 
                        print "DELETE ".$movies_path."/slidesound.mp3 failed. ". PHP_EOL ;	
                    exec('rm '.$movies_path.'/camNosound.mov',$err,$return);
                    if ($return != 0) 
                        print "DELETE ".$movies_path."/camNosound.mov failed. ". PHP_EOL ;
      
                }
                if ($error != 0)
                    print "Copy sound from slide to Cam failed. ". PHP_EOL ;                
            }		
            print "AUDIO_SYNCHRONISATION SUCCEED ".PHP_EOL ;
			print "duration : $duration SUCCEED \n";
			print "diff_time : $diff_time SUCCEED \n";
			print "diff_time_string : $diff_time_string SUCCEED \n";
		}
//         Delete audio files
        exec('rm '.$campath.'.wav ',$err,$return);
        if ($return != 0)  
            print "DELETE ".$campath.".wav failed. ".PHP_EOL ;
      
        exec('rm '.$slidepath.'.wav ',$err,$return);
        if ($return != 0) 
            print "DELETE ".$slidepath.".wav failed. ". PHP_EOL ;   
    }
    else 
        print "NO AUDIO FOUND";
}
