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
 */

require_once 'config.inc';

function test_php()
{
    global $php_cli_cmd;
    
    if (exec("if [ -e " . $php_cli_cmd . " ]; then echo 'exists'; fi;") != 'exists') {
        // PHP binary doesn't exist on remote renderer
        echo "php_not_found";
        die;
    } else {
        exec("$php_cli_cmd -v", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'PHP') === false) {
            // PHP not found on remote renderer
            echo "php_not_found";
            die;
        } else {
            // Test PHP version
            $php_version = substr($output[0], 4, 3);
            if (is_nan($php_version) || (double) $php_version < 5.3) {
                // PHP is deprecated
                echo "php_deprecated";
                die;
            } else {
                // Test PHP modules
                exec("$php_cli_cmd -m", $output, $returncode);
                if (!in_array("SimpleXML", $output)) {
                    echo "php_missing_xml";
                    die;
                }
                if (!in_array("gd", $output)) {
                    echo "php_missing_gd";
                    die;
                }
                $gd_info = gd_info();
                if (!$gd_info['FreeType Support']) {
                    echo "gd_missing_freetype";
                    die;
                }
            }
        }
    }
}

function test_ffmpeg($aac_experimental = false)
{
    global $ffmpegpath;
    
    if (exec("if [ -e " . $ffmpegpath . " ]; then echo 'exists'; fi;") != 'exists') {
        // FFMPEG binary doesn't exist on remote renderer
        echo "ffmpeg_not_found";
        die;
    } else {
        exec("$ffmpegpath -version", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'FFMPEG') === false) {
            // FFMPEG not found on remote renderer
            echo "ffmpeg_not_found";
            die;
        } else {
            // Test FFMPEG codecs
            $aac_codec = ($aac_experimental) ? 'aac' : 'libfdk_aac';
            $output = exec("$ffmpegpath -codecs | grep '$aac_codec'");
            if (strpos(strtoupper($output), 'AAC') === false) {
                echo "missing_codec_aac";
                die;
            }
            $output = exec("$ffmpegpath -codecs | grep 'h264'");
            if (strpos(strtoupper($output), 'H.264') === false) {
                echo "missing_codec_h264";
                die;
            }
        }
    }
}

function test_ffprobe()
{
    global $ffprobepath;
    
    if (exec("if [ -e " . $ffprobepath . " ]; then echo 'exists'; fi;") != 'exists') {
        // FFPROBE binary doesn't exist on remote renderer
        echo "ffprobe_not_found";
        die;
    } else {
        exec("$ffprobepath -version", $output, $returncode);
        if (strpos(strtoupper($output[0]), 'FFPROBE') === false) {
            // FFMPEG not found on remote renderer
            echo "ffprobe_not_found";
            die;
        }
    }
}

test_php();
if ($encoding_pgm['name'] == 'ffmpeg' || $encoding_pgm['name'] == 'ffmpeg_exp') {
    test_ffprobe();
    test_ffmpeg($encoding_pgm['name'] == 'ffmpeg_exp');
}
echo "test ok";
