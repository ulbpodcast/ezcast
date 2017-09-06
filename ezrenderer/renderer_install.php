<?php

/*
 * EZCAST EZrenderer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Antoine Dewilde
 *            Carlos Avimadjessi
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

/*
 * This file is part of EZrenderer installation process
 * This is meant to be executed from ezadmin "Create renderer" menu.
 * Print 0 on success
 */

if ($argc != 7) {
    echo "usage: " . $argv[0] . " <php_path> <encoding_pgm> <ffmpeg_path> <ffprobe_path> <threads_number> <jobs_number>" .
    "\n <php_path> the path to the php binary." .
    "\n <encoding_pgm> string containing information about encoder such as : ".
    "\n         array('name' => 'ffmpeg', 'description' => 'ffmpeg desc.', 'file' => 'lib_ffmpeg.php')" .
    "\n <ffmpeg_path> the path to FFMPEG binary." .
    "\n <ffprobe_path> the path to FFPROBE binary." .
    "\n <threads_number> the number of theads used for one encoding." .
    "\n <jobs_number> Number of jobs allowed to run simultaneously.";
    die;
}

$current_dir = dirname(__FILE__);
$config_dir = $current_dir."/bin";
$config_file = "config.inc";
$config_file_path = $config_dir."/".$config_file;

$php_cmd = $argv[1];
$encoding_pgm = var_export(unserialize($argv[2]), true);
$ffmpeg_path = $argv[3];
$ffprobe_path = $argv[4];
$num_threads = $argv[5];
$num_jobs = $argv[6];

$config = file_get_contents($current_dir."/bin/config-sample.inc");

$config = preg_replace('/\$basedir = (.+);/', '\$basedir = "'.$current_dir.'/";', $config);
$config = preg_replace('/\$num_threads = (.+);/', '\$num_threads = '.$num_threads.';', $config);
$config = preg_replace('/\$num_jobs = (.+);/', '\$num_jobs = '.$num_jobs.';', $config);
$config = preg_replace('/\$php_cli_cmd = (.+);/', '\$php_cli_cmd = "'.$php_cmd.'";', $config);
$config = preg_replace('/\$ffmpegpath = (.+);/', '\$ffmpegpath = "'.$ffmpeg_path.'";', $config);
$config = preg_replace('/\$ffprobepath = (.+);/', '\$ffprobepath = "'.$ffprobe_path.'";', $config);
$config = preg_replace('/\$encoding_pgm = (.+);/', '\$encoding_pgm = '.$encoding_pgm.';', $config);
file_put_contents($config_file_path, $config);

// Replaces path in
$intro_title_movie = file_get_contents($current_dir . "/bin/intro_title_movie.bash");
$intro_title_movie = str_replace("!PATH", $current_dir, $intro_title_movie);
$intro_title_movie = str_replace("!PHP_PATH", $php_cmd, $intro_title_movie);
file_put_contents($current_dir . "/bin/intro_title_movie.bash", $intro_title_movie);

chmod($current_dir . "/bin/intro_title_movie.bash", 0755);

echo "0";
