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

/*
 * interfaces ffmpeg commandline tool 
 * All path should be absolute
 */
include_once 'config.inc';

if ($encoding_pgm['name'] == 'ffmpeg_exp')
    $aac_experimental = true;

/**
 * concatenates multiple video files without re-encoding (as a reference movie)
 * @global string $ffmpegpath 
 * @param type $movie_array an array containing movies to concatenate (requires absolute paths)
 * @param type $output name of the output video
 * @return boolean
 */
function movie_join_array($movie_array, $output) {
    global $ffmpegpath;

    // creates a string containing all video files to join
    $filename_list = '';
    for ($i = 0; $i < count($movie_array); $i++) {
        if (!is_file($movie_array[$i])) {
            return false;
        }
        $filename_list .= "file '" . $movie_array[$i] . "'" . PHP_EOL;
    }
    // saves the list of filenames in a temporary text file to be used by ffmpeg
    $tmp_file = uniqid("files_list_") . '.txt';
    $cmd = "echo '$filename_list' > $tmp_file";
    exec($cmd, $cmdoutput, $returncode);
    if ($returncode) {
        return join("\n", $cmdoutput);
    }
    // concatenates the video files with no reencoding
    // -f concat : option for the concatanation of files
    // -i file : input file is the list containing the video files to concat
    // -c copy : keep the current codecs (no reencoding)
    // output : the name and extension for the output file
    $cmd = "$ffmpegpath -f concat -i ./$tmp_file -c copy -y $output";
    print $cmd;
    exec($cmd, $cmdoutput, $returncode);
    // deletes the temporary text file
    unlink("./$tmp_file");
    //check returncode
    if ($returncode) {
        return join("\n", $cmdoutput);
    }
    return false;
}

/**
 *
 * @global string $ffmpegpath
 * @param absolute_path $moviein
 * @param absolute_path $audiomovieout
 * @return false|error_string
 * @desc extract an audio track from a movie
 */
function movie_extract_audiotrack($moviein, $audiomovieout) {
    global $ffmpegpath;

    // sanity check
    if (!is_file($moviein))
        return "input movie not found $moviein";

    // safe path
    $moviein = escape_path($moviein);
    $audiomovieout = escape_path($audiomovieout);
    /**
     * -i : input file 
     * -vn : no video
     * -ac : audio channels
     * -ar : audio sample rate
     * -ab : audio bitrate
     * -f  : force format
     */
    $cmd = "$ffmpegpath -i $moviein -vn -ac 2 -ar 44100 -ab 400k -f mp4 $audiomovieout";
    exec($cmd, $cmdoutput, $returncode);
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput);
    return false;
}

/**
 *
 * @global string $ffmpegpath
 * @param absolute_path $moviein
 * @param absolute_path $videomovieout
 * @return false|error_string
 * @desc extract a video track from a movie
 */
function movie_extract_videotrack($moviein, $videomovieout) {
    global $ffmpegpath;
    if (!is_file($moviein))
        return "input movie not found $moviein";
    $moviein = escape_path($moviein);
    $videomovieout = escape_path($videomovieout);
    /**
     * -i : input source
     * -an: no audio
     */
    $cmd = "$ffmpegpath -i $moviein -an $videomovieout";
    exec($cmd, $cmdoutput, $returncode);
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput);
    return false;
}

/**
 *
 * @global string $ffmpegpath
 * @param absolute_path $movie video movie
 * @param absolute_path $movie2 audio track .mov
 * @param absolute_path $movieout
 * @return false or error_string
 *  @desc add a audio track (.mov) to a  movie  (as a reference movie)
 */
function movie_add_audiotrack($moviein, $audiomovie, $movieout) {
    global $ffmpegpath;
    // sanity check
    if (!is_file($moviein))
        return "input movie1 not found $moviein";
    if (!is_file($audiomovie))
        return "input audiotrack not found $audiomovie";

    $movie = escape_path($movie);
    $audiomovie = escape_path($audiomovie);
    // -i : input sources
    // -vcodec copy : keep the current video codec
    // -acodec copy : keep the current audio codec 
    $cmd = "$ffmpegpath -i $moviein -i $audiomovie -vcodec copy -acodec copy $movieout";
    exec($cmd, $cmdoutput, $returncode);
    //check returncode
    if ($returncode) {
        return join("\n", $cmdoutput);
    }
    return false;
}

/**
 * Returns information about the given movie
 * @global string $ffmpegpath
 * @param path $moviein
 * @param assoc_array &$qtinfo
 * @return bool false if sucess
 */
function movie_qtinfo($moviein, &$qtinfo) {
    global $ffprobepath;
    $qtinfo = array();
    // Sanity check
    if (!is_file($moviein))
        return "input movie not found $moviein";
    $moviein = escape_path($moviein);

    // ffprobe gathers information from multimedia streams 
    // -v quiet : loglevel shows nothing (no error or warning)
    // -print_format : information given in json
    // -show_format : gives information about the container format of the input multimedia stream
    // -show_streams : gives information about each media stream contained in the input multimedia stream
    $cmd = "$ffprobepath -v quiet -print_format json -show_format -show_streams $moviein";
    exec($cmd, $cmdoutput, $returncode);

    print $cmd . PHP_EOL;
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput); //error

        
//ffprobe cmd went ok
    //analyses output and saves some specific information we'll need later
    $qtinfo = json_decode(preg_replace("/[^[:alnum:][:punct:] ]/", "", implode($cmdoutput)), true);
    for ($i = 0; $i < count($qtinfo["streams"]); $i++) {
        if ($qtinfo["streams"][$i]["codec_type"] == "video") {
            $qtinfo["height"] = $qtinfo["streams"][$i]["height"];
            $qtinfo["width"] = $qtinfo["streams"][$i]["width"];
            $qtinfo["videoCodec"] = $qtinfo["streams"][$i]["codec_name"];
            $qtinfo["aspectRatio"] = $qtinfo["streams"][$i]["display_aspect_ratio"];
            $qtinfo["pixRatio"] = $qtinfo["streams"][$i]["sample_aspect_ratio"];
        } else if ($qtinfo["streams"][$i]["codec_type"] == "audio") {
            $qtinfo["audioCodec"] = $qtinfo["streams"][$i]["codec_name"];
        }
    }
    $qtinfo["duration"] = $qtinfo["format"]["duration"];
    var_dump($qtinfo);

    return false;
}

/**
 *
 * @global string $ffmpegpath
 * @param absolute_path $moviein
 * @param absolute_path $movieout
 * @param string encoder
 * @param assoc_array qtinfo information about the video
 * @return false|error_string
 * @desc encodes a (ref) movie to a self-contained movie using a specified codec
 */
function movie_encode($moviein, $movieout, $encoder, $qtinfo, $letterboxing = true) {
    global $ffmpegpath, $encoders_path, $aac_experimental;
    // sanity check
    if (!is_file($moviein))
        return "input movie not found $moviein";
    // safe path
    $moviein = escape_path($moviein);
    $movieout = escape_path($movieout);
    // encoder comes in three parts
    // first part is the codec (libx264 | ...)
    // second part is the quality [low | medium | superhigh]
    // third part is the resolution [width_x_height]
    $encoder_values = explode('_', $encoder);
    $codec = $encoder_values[0];
    $quality = $encoder_values[1];

    $resolution_values = explode('x', $encoder_values[2]);
    $width = $resolution_values[0];
    $height = $resolution_values[1];

    $pixel_correction = (!isset($qtinfo["pixRatio"])) ? array(1, 1) : explode(':', $qtinfo["pixRatio"]);
    $pixw = ($pixel_correction[0] == 0) ? 1 : $pixel_correction[0];
    $pixh = ($pixel_correction[1] == 0) ? 1 : $pixel_correction[1];

    $encoder = $encoders_path . '/' . $codec . '_' . $quality . '.ffpreset';

    if ($letterboxing) {
        // iw : image width
        // ih : image height
        // pad : letterboxing filter
        //  $video_filter = "scale=iw*min($width/iw\,$height/ih):ih*min($width/iw\,$height/ih), pad=$width:$height:($width-iw*min($width/iw\,$height/ih))/2:($height-ih*min($width/iw\,$height/ih))/2";
        $video_filter = "scale=iw*min($width/iw\,$height/(ih/$pixw*$pixh)):(ih/$pixw*$pixh)*min($width/iw\,$height/(ih/$pixw*$pixh)), pad=$width:$height:($width-iw)/2:($height-ih)/2";
    } else {
        $video_filter = "scale=$width:$height";
    }

    // checks if the encoder file exists
    if (!is_file($encoder))
        return "encoder not found $encoder";

    if ($aac_experimental) {
        $aac_codec = ' -acodec aac -strict experimental ';
        // overwrites audio codec from encoders file
    }
    /**
     * -i : input
     * -r : frame rate
     * -fpre : preset file (contains encoding settings such as video codec, audio codec, ...)
     * -vf : video filters
     * -y : overwrites movie if existing yet
     */
    $cmd = "$ffmpegpath -i $moviein -r 25 -fpre $encoder -vf \"$video_filter\" -ar 44100 -ac 2 -y -pix_fmt yuv420p $aac_codec $movieout";
    exec($cmd, $cmdoutput, $returncode);
    print $cmd;
    //check returncode
    return $returncode;
// if($returncode)return join ("\n", $cmdoutput);
// return false;
}

/**
 * stores author, copyright,... in the movie file

 * @global string $ffmpegpath
 * @param string $movie
 * @param string $title
 * @param string $comment
 * @param string $description
 * @param string $author
 * @param string $keywords
 * @param string $copyright
 * @return false|errmessage
 */
function movie_annotate($moviein, $movieout, $title, $comment, $description, $author, $keywords, $copyright) {
    global $ffmpegpath;
    // Sanity check
    if (!is_file($moviein))
        return "movie not found '$moviein'";
    //escape parameters
    $title_esc = escape_path($title);
    $comment_esc = escape_path($comment);
    $description_esc = escape_path($description);
    $author_esc = escape_path($author);
    $keywords_esc = escape_path($keywords);
    $copyright_esc = escape_path($copyright);
    $movie_esc = escape_path($moviein);

    /**
     * -i : input file
     * -metadata : add metadata
     * -c copy : copy codecs (no re-encoding)
     * -f mp4: forces mp4 encoding for Android support
     * -y : overwrite existing movie
     */
    $cmd = "$ffmpegpath -i $movie_esc -metadata title=$title_esc -metadata author=$author_esc -metadata keywords=$keywords_esc -metadata copyright=$copyright_esc -c copy -f mp4 -y $movieout";
    exec($cmd, $cmdoutput, $returncode);
    print "\n$cmd\n";
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput);
    return false;
}

/**
 * Creates a video title
 * @global string $ffmpegpath
 * @param absolute_path $movieout
 * @param assoc_array $title_elements
 * @param integer $width
 * @param integer $height
 * @param integer $duration
 * @param absolute_path $overlay_movie
 * @return false|error_string
 */
function movie_title($movieout, $title_elements, $encoder, $duration = 8) {
    global $ffmpegpath, $fontfile, $encoders_path, $aac_experimental;

    $movieout = escape_path($movieout);

    $encoder_values = explode('_', $encoder);
    $codec = $encoder_values[0];
    $quality = $encoder_values[1];

    $resolution_values = explode('x', $encoder_values[2]);
    $width = $resolution_values[0];
    $height = $resolution_values[1];

    $encoder = $encoders_path . '/' . $codec . '_' . $quality . '.ffpreset';

    // checks if the encoder file exists
    if (!is_file($encoder))
        return "encoder not found $encoder";

    $drawtext_filter = '';
    // draw album name
    if (isset($title_elements['album'])) {
        // drawtext filter cannot handle ':'
        $album = str_replace(":", "\:", escape_path($title_elements['album'], false));
        /**
         * fontfile : the font to use
         * text : the text to display
         * fontsize : the font size (determined following the video height)
         * x : the horizontal position (determined following the total width (w) and the text width (text_w))
         * y : the vertical position (determined following the total height (h) and the text height (text_h))
         * fontcolor : the color of the text
         */
        $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$album':fontsize=($height/19): x=(w-text_w)/2: y=(h)/2-(6.5*text_h):fontcolor=white, ";
    }
    // draw title - if title is longer than 35 char, it is split in several lines
    if (isset($title_elements['title'])) {
        include_once 'lib_gd.php';
        $title = split_title(str_replace(":", "\:", escape_path($title_elements['title'], false)));
        foreach ($title as $index => $title_line) {
            $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$title_line':fontsize=($height/11.5): x=(w-text_w)/2: y=(h)/2-((3-$index)*($height/18.5)):fontcolor=white, ";
        }
    }
    // draw the author
    if (isset($title_elements['author'])) {
        $author = str_replace(":", "\:", escape_path($title_elements['author'], false));
        $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$author': x=(w-text_w)/2: fontsize=($height/13): y=(h-text_h)/2+(2*text_h):fontcolor=white, ";
    }
    // draw the date - except if it is not required
    if (isset($title_elements['date']) && $title_elements['hide_date'] != true) {
        $date = str_replace(":", "\:", escape_path($title_elements['date'], false));
        $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$date': x=(w-text_w)/2: fontsize=($height/19): y=(h-text_h)/2+(6.5*text_h):fontcolor=white, ";
    }
    // draw copyrights
    if (isset($title_elements['copyright'])) {
        $copyright = str_replace(":", "\:", escape_path($title_elements['copyright'], false));
        $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$copyright': fontsize=($height/28.8): x=(w-text_w)/2: y=(h)-(4.5*text_h):fontcolor=white, ";
    }
    // draw organization name
    if (isset($title_elements['organization'])) {
        $organization = str_replace(":", "\:", escape_path($title_elements['organization'], false));
        $drawtext_filter .= "drawtext=fontfile=$fontfile: text='$organization': fontsize=($height/28.8): x=(w-text_w)/2: y=(h)-(4*text_h):fontcolor=white, ";
    }

    // removes trailing ', ' from the last line
    if (strlen($drawtext_filter) > 2) {
        $drawtext_filter = substr($drawtext_filter, 0, -2);
    }

    if ($aac_experimental) {
        $aac_codec = ' -acodec aac -strict experimental ';
        // overwrites audio codec from encoders file
    }

    /*
     * generates input silent audio stream for titling:
     *      -ar : audio sample rate
     *      -ac : audio channels
     *      -f  : force audio input format (s161e required to generate the audio stream)
     *      -t  : duration in seconds
     *      -i  : input source
     * generates input video stream for titling :
     *      -f  : force video input format (lavfi required to generate the video stream)
     *      -t  : duration in secondes
     *      -i  : a colored screen with color (c) 0x... , size (s) $widthx$height and framerate (r) 25/1
     * applies filters for output file
     *      -vf : video filters to apply
     *      -vpre : video preset file (settings used to encode the video)
     */
    $cmd = $ffmpegpath . ' -ar 44100 -ac 2 -f s16le -t ' . $duration . ' -i /dev/zero ' .
            '-f lavfi -t ' . $duration . ' -i color=c=0x2578B6:s=' . $width . 'x' . $height . ':r=25 ' .
            '-vf "' . $drawtext_filter . '" -fpre ' . $encoder . ' ' . $aac_codec . ' -y ' . $movieout;

    exec($cmd, $cmdoutput, $returncode);
    print "\n$cmd\n";
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput);
    return false;
}

/**
 * Transforms an image in video clip
 * @global string $ffmpegpath
 * @global type $encoders_path
 * @param type $movieout
 * @param type $imagein
 * @param string $encoder
 * @param type $duration
 * @return boolean
 */
function movie_title_from_image($movieout, $imagein, $encoder, $duration = 8) {
    global $ffmpegpath, $encoders_path, $aac_experimental;

    $movieout = escape_path($movieout);

    $encoder_values = explode('_', $encoder);
    $codec = $encoder_values[0];
    $quality = $encoder_values[1];

    $resolution_values = explode('x', $encoder_values[2]);
    $width = $resolution_values[0];
    $height = $resolution_values[1];

    $encoder = $encoders_path . '/' . $codec . '_' . $quality . '.ffpreset';

    // checks if the encoder file exists
    if (!is_file($encoder))
        return "encoder not found $encoder";

    $video_filter = "scale=iw*min($width/iw\,$height/ih):ih*min($width/iw\,$height/ih), pad=$width:$height:($width-iw*min($width/iw\,$height/ih))/2:($height-ih*min($width/iw\,$height/ih))/2";

    if ($aac_experimental) {
        $aac_codec = ' -acodec aac -strict experimental ';
        // overwrites audio codec from encoders file
    }

    /*
     * generates input silent audio stream for titling:
     *      -ar : audio sample rate
     *      -ac : audio channels
     *      -f  : force audio input format (s161e required to generate the audio stream)
     *      -t  : duration in seconds
     *      -i  : input source
     * generates input video stream for titling :
     *      -loop : better image rendering
     *      -r  : frame rate
     *      -i  : input file (image) for title 
     *      -t  : duration in secondes
     *      -fpre : video preset file (settings used to encode the video)
     */
    $cmd = $ffmpegpath . ' -ar 44100 -ac 2 -f s16le -t ' . $duration . ' -i /dev/zero ' .
            '-loop 1 -r 25 -i ' . $imagein . ' -t ' . $duration . ' -vf "' . $video_filter . '" -fpre ' . $encoder . ' ' . $aac_codec . ' -y ' . $movieout;

    exec($cmd, $cmdoutput, $returncode);
    print "\n$cmd\n";
    //check returncode
    if ($returncode)
        return join("\n", $cmdoutput);
    return false;
}

/**
 * Removes duplicate slashes (/) in a file path
 * @param type $path
 * @param type $escapeshellarg
 * @return type
 */
function escape_path($path, $escapeshellarg = true) {
    $newpath = str_replace("//", "/", $path); //removes multiple / 
    return ($escapeshellarg) ? escapeshellarg($newpath) : $newpath;
}

/**
 * moves the moov atom of the video at the beginning of the video
 * @global string $ffmpegpath
 * @param type $moviein
 * @param type $movieout
 * @return boolean
 */
function movie_moov_atom($moviein, $movieout) {
    global $ffmpegpath;
    /**
     * -i : input file
     * -movflags faststart : plugin for ffmpeg 
     * -c copy : keep the audio and video codecs (no re-encoding)
     * -y : overwrite the movie if it exists yet
     */
    $cmd = $ffmpegpath . ' -i ' . $moviein . ' -movflags faststart -c copy -f mp4 -y ' . $movieout;
    exec($cmd, $cmdoutput, $returncode);

    print "\n$cmd\n";
    if ($returncode) {
        $cmd = $ffmpegpath . ' -i ' . dirname($moviein) . '/output_ref_movie.mov' . ' -movflags faststart -c copy -f mp4 -y ' . $movieout;
        exec($cmd, $cmdoutput, $returncode);

        if ($returncode) {
            return join("\n", $cmdoutput);
        }
    }
    return false;
}

/* * ***************** E D I T I O N   F U N C T I O N S ********************* */

function movie_cut($movie_path, $movie_in, $cutlist, $bias = 0) {
    global $ffmpegpath;

    // converts the associative array in regular array
    foreach ($cutlist as $key => $values) {
        if (is_string($values)) {
            $cutlist_array[$values] = $key;
        } else if (is_array($values)) {
            foreach ($values as $value) {
                $cutlist_array[$value] = $key;
            }
        }
    }
    // sorts array on index
    ksort($cutlist_array);

    $ffmpeg_params = array();
    $startime = 0;
    $duration = 0;
    // prepares parameters for ffmpeg 
    foreach ($cutlist_array as $index => $value) {

        switch ($value) {
            case 'start' :
            case 'resume':
                if ($startime == 0) {
                    $startime = $index;
                }
                break;
            case 'pause' :
            case 'stop':
                if ($startime != 0 && $index > $startime) {
                    $duration = $index - $startime;
                    $ffmpeg_params[] = (($startime - $bias) < 0) ? (array( 0 , $duration - abs($startime - $bias))) : (array( $startime - $bias , $duration));
                    
                    $startime = 0;
                }
                break;
        }
        if ($value == 'stop')
            break;
    }
    if ($startime != 0){
        $ffmpeg_params[] = (($startime - $bias) < 0) ? (array( 0 , -1)) : (array( $startime - $bias , -1));
    }
    
    chdir($movie_path);

    $tmp_dir = 'tmpdir';
    mkdir("./$tmp_dir");
    
    // creates each recording segments to be concatenated
    foreach ($ffmpeg_params as $index => $params) {
        $try = 0;
        $part_duration = -5;
        // sometimes, ffmpeg doesn't extract the recording segment properly
        // This results in a shortened segment which may cause problems in the final rendering 
        // We then loop on segment extraction to make sure it has the expected duration
        while ($try < 3 && $part_duration < $params[1]) {
            // extracts the recording segment from the full recording
            // -ss : the segment starts at $param[0] seconds of the full video
            // -t  : the segment lasts $param[1] seconds long
            // -c  : audio and video codecs are copied
            // -y  : the segment is replaced if already existing
            $more_params = ($try >= 1) ? ' -probesize 1000000 -analyzeduration 1000000 ' : ''; // increase analyze duration
            $more_params .= ($try >= 2) ? ' -pix_fmt yuv420p ' : ''; // defines pixel format, which is often lacking
            $ext = file_extension_get($movie_in);
            $ext = $ext['ext'];
            $cmd = "$ffmpegpath -i $movie_path/$movie_in -ss " . $params[0] . (($params[1] !== -1 ) ? " -t " . $params[1] : '') . $more_params . " -c copy -y $tmp_dir/part-$index.$ext; wait";
            print "*************************************************************************" . PHP_EOL .
                    $cmd . PHP_EOL .
                    "*************************************************************************" . PHP_EOL;
            exec($cmd, $cmdoutput, $returncode);
            // the segment has been extracted, we verify here its duration
            $cmd = "$ffmpegpath -i $tmp_dir/part-$index.$ext 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,// | sed 's@\..*@@g'";
            $part_duration = system($cmd); // duration in HH:MM:SS
            sscanf($part_duration, "%d:%d:%d", $hours, $minutes, $seconds);
            $part_duration = $hours * 3600 + $minutes * 60 + $seconds; // duration in seconds
            $try++;
            print "--------------------------------------------------------------------------" . PHP_EOL .
                    "Try [$try]: duration found : $part_duration - expected : " . $params[1] . PHP_EOL .
                    "--------------------------------------------------------------------------" . PHP_EOL;
        }
    }
}


/**
 * scans a filename and extract 'name' and 'ext'(ension) parts return them in an assoc array
 * @param <type> $filename
 * @return false|assoc_array
 */
function file_extension_get($filename) {
    //search last dot in filename
    $pos_dot = strrpos($filename, '.');
    if ($pos_dot === false)
        return array('name' => $filename, 'ext' => "");

    $ext_part = substr($filename, $pos_dot + 1);
    $name_part = substr($filename, 0, $pos_dot);
    $result_assoc['name'] = $name_part;
    $result_assoc['ext'] = $ext_part;
    return $result_assoc;
}
?>
