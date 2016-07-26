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


$basedir = "~/";
$temp_dir = "$basedir/queues/downloading";
$downloaded_dir = "$basedir/queues/downloaded";
$processing_dir = "$basedir/queues/processing";
$processed_dir = "$basedir/queues/processed";
$failed_dir = "$basedir/queues/failed";
$intros_dir = "$basedir/intros";
$credits_dir = "$basedir/credits";

$performance_idx = 1; // Arbitrary number reflecting the performance of the machine. Float number between 0 and 1.
$num_threads = 4; // Number of threads used for one encoding.
$num_jobs = 4; // Number of jobs allowed to run simultaneously. Ideally <= (num_cores / num_threads)

$default_add_title = "FlyingTitle";

$php_cli_cmd = "/usr/bin/php";
$ffmpegpath = "/usr/local/bin/ffmpeg";
$ffprobepath = "/usr/local/bin/ffprobe";
$encoders_path = "$basedir/bin/encoders/";
$encoding_pgm = array("name" => 'ffmpeg', "description" => 'ffmpeg command line', "file" => 'lib_ffmpeg.php');

$accepted_video_sizes = array(
    "16:9" => array(640, 960, 1024, 1280, 1366, 1600, 1920),
    "4:3" => array(320, 640, 720, 800, 960, 1024, 1280, 1600, 1920)); //sizes of videos supported by the renderer (for user submited videos, they will be downsized to one of these sizes)
$video_high_transcoders = array(
    "16:9" => array(
        'low' => 'libx264_low_640x360',
        '640' => 'libx264_low_640x360',
        '960' => 'libx264_medium_960x540',
        '1024' => 'libx264_medium_1024x576',
        '1280' => 'libx264_medium_1280x720',
        '1366' => 'libx264_medium_1366x768',
        '1600' => 'libx264_medium_1600x900',
        '1920' => 'libx264_medium_1920x1080',
        'super_highres' => 'libx264_superhigh_'),
    "4:3" => array(
        'low' => 'libx264_low_640x480',
        '320' => 'libx264_low_320x240',
        '640' => 'libx264_medium_640x480',
        '720' => 'libx264_medium_720x576',
        '800' => 'libx264_medium_800x600',
        '960' => 'libx264_medium_960x720',
        '1024' => 'libx264_medium_1024x768',
        '1280' => 'libx264_medium_1280x960',
        '1600' => 'libx264_medium_1600x1200',
        '1920' => 'libx264_medium_1920x1440',
        'super_highres' => 'libx264_superhigh_')
    );

$intro_movies = array(
    '16:9' => 'intro_16_9.mp4',
    '16:10' => 'N/A',
    '3:2' => 'N/A',
    '4:3' => 'intro_4_3.mov',
    '5:3' => 'N/A',
    '5:4' => 'N/A',
    '8:5' => 'N/A',
    'default' => 'original_intro_16_9.mp4',
);

$credits_movies = array(
    '16:9' => 'credits_16_9.mp4',
    '16:10' => 'N/A',
    '3:2' => 'N/A',
    '4:3' => 'credits_4_3.mov',
    '5:3' => 'N/A',
    '5:4' => 'N/A',
    '8:5' => 'N/A',
    'default' => 'original_credits_16_9.mp4',
);


///////////// LIBGD - Title image generation ////////////////
// font used to render the title
$fontfile = "$basedir/fonts/dejavu-fonts-ttf-2.34/ttf/DejaVuSans.ttf";
// quality for jpg compression [0-100]
$quality = 100;
// ratio used to determine the fontsize, related to the image height
// higher the ratio, smaller the font [0.1-10]
// this ratio may have to change following the font used
$fontratio = 1;
// value used to determine the max line length to display for title and album name
$linelength = 35;
/////////////////////////////////////////////////////////////
?>
