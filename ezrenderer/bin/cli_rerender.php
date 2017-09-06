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

require_once dirname(__FILE__) . '/config.inc';
require_once dirname(__FILE__) . '/lib_ffmpeg.php';
require_once dirname(__FILE__) . '/lib_gd.php';
require_once dirname(__FILE__) . '/lib_metadata.php';

if ($argc != 2) {
    echo "usage: " . $argv[0] . ' <path_to_new_asset_dir>'
    . 'with <path_to_new_asset_dir> the absolute path to the '
    . 'new asset directory';
}

$new_asset_dir = $argv[1];
if (!is_dir($new_asset_dir)) {
    echo "new asset directory not found";
    die;
}

$new_asset_array = require_once $new_asset_dir . '/torender.inc';


$rendering_dir = $processing_dir . '/' . $new_asset_array['new_asset'] . '_' . $new_asset_array['new_album'];
$cmd = "mv $new_asset_dir $rendering_dir";
exec($cmd);

//$new_asset_array = require_once '/Users/ezcastrender/ezrenderer/queues/processing/2015_08_05_11h26_PODC-I-021-priv/torender.inc';
//$rendering_dir = $processing_dir . '/' . $new_asset_array['new_asset'] . '_' . $new_asset_array['new_album'];

$record_types = array('cam', 'slide');
$files_to_edit = array();
foreach ($record_types as $record_type) {
    if (strpos($new_asset_array['new_type'], $record_type) !== false) {
        $bias = 0;
        if ($record_type === 'cam') {
            if (isset($new_asset_array['cam_bias']) && $new_asset_array['cam_bias'] != '') {
                $bias -= (int) $new_asset_array['cam_bias'];
            }
        }
        if (is_dir($rendering_dir . '/original_' . $record_type)) {
            $files_to_edit['original_' . $record_type]['path'] = $rendering_dir . '/original_' . $record_type;
            $filename = glob($rendering_dir . '/original_' . $record_type . '/' . $record_type . '.*');
            $files_to_edit['original_' . $record_type]['filename'] = basename($filename[0]);
            $files_to_edit['original_' . $record_type]['bias'] = $bias;
            if (isset($new_asset_array['previous_intro']) && $new_asset_array['previous_intro'] == '1') {
                $files_to_edit['original_' . $record_type]['bias'] += 7;
            }
            if (isset($new_asset_array['previous_title']) && $new_asset_array['previous_title'] == '1') {
                $files_to_edit['original_' . $record_type]['bias'] += 8;
            }
        }
        if (is_dir($rendering_dir . '/high_' . $record_type)) {
            $qtinfo_high = array();
            $files_to_edit['high_' . $record_type]['path'] = $rendering_dir . '/high_' . $record_type;
            $files_to_edit['high_' . $record_type]['filename'] = 'high_' . $record_type . '.mov';
            $files_to_edit['high_' . $record_type]['bias'] = $bias;
            movie_qtinfo($rendering_dir . '/high_' . $record_type . '/high_' . $record_type . '.mov', $qtinfo_high);
            $intro_movie = $intros_dir . "/" . $new_asset_array['intro_movie'] . "/" . $intro_movies[$qtinfo_high['aspectRatio']];
            if (!file_exists($intro_movie)) {
                $intro_movie = $intros_dir . "/" . $new_asset_array['intro_movie'] . "/" . $intro_movies['default'];
            }
            $files_to_edit['high_' . $record_type]['intro_movie'] = $intro_movie;
            if ($new_asset_array['super_highres'] == '1') {
                $encoder = 'libx264_superhigh_' . $qtinfo_high['width'] . 'x' . $qtinfo_high['height'];
            } else {
                $encoder = 'libx264_high_' . $qtinfo_high['width'] . 'x' . $qtinfo_high['height'];
            }
            $files_to_edit['high_' . $record_type]['encoder'] = $encoder;
            $files_to_edit['high_' . $record_type]['height'] = $qtinfo_high['height'];
            $files_to_edit['high_' . $record_type]['width'] = $qtinfo_high['width'];
        }
        if (is_dir($rendering_dir . '/low_' . $record_type)) {
            $qtinfo_low = array();
            $files_to_edit['low_' . $record_type]['path'] = $rendering_dir . '/low_' . $record_type;
            $files_to_edit['low_' . $record_type]['filename'] = 'low_' . $record_type . '.mov';
            $files_to_edit['low_' . $record_type]['bias'] = $bias;
            movie_qtinfo($rendering_dir . '/low_' . $record_type . '/low_' . $record_type . '.mov', $qtinfo_low);
            $intro_movie = $intros_dir . "/" . $new_asset_array['intro_movie'] . "/" . $intro_movies[$qtinfo_low['aspectRatio']];
            if (!file_exists($intro_movie)) {
                $intro_movie = $intros_dir . "/" . $new_asset_array['intro_movie'] . "/" . $intro_movies['default'];
            }
            $files_to_edit['low_' . $record_type]['intro_movie'] = $intro_movie;
            $encoder = 'libx264_low_' . $qtinfo_low['width'] . 'x' . $qtinfo_low['height'];
            $files_to_edit['low_' . $record_type]['encoder'] = $encoder;
            $files_to_edit['low_' . $record_type]['height'] = $qtinfo_low['height'];
            $files_to_edit['low_' . $record_type]['width'] = $qtinfo_low['width'];
        }
    }
}

foreach ($files_to_edit as $key => $file) {
    // cuts the original assets in multiple parts
    mkdir($file['path'] . '/tmpdir');
    if (isset($new_asset_array['cutlist']) && $new_asset_array['cutlist'] != '') {
        movie_cut($file['path'], $file['filename'], $new_asset_array['cutlist'], $file['bias'], 'part');
    } else {
        $ext = file_extension_get($file['filename']);
        copy($file['path'] . '/' . $file['filename'], $file['path'] . '/tmpdir/part-0.' . $ext['ext']);
    }

    if (strpos($key, 'original') === false) {
        // encodes the jingle
        if (isset($new_asset_array['intro_movie']) && $new_asset_array['intro_movie'] !== "") {
            $ret = movie_encode($file['intro_movie'], $file['path'] . '/tmpdir/0-intro.mov', $file['encoder'], false);
        }
        // generates the titling
        if (isset($new_asset_array['add_title']) && $new_asset_array['add_title'] === "FlyingTitle") {
            // prepares the title array
            $title_info = array();
            if ($new_asset_array['title_fields']['album'] !== 'false') {
                $title_info['album'] = $new_asset_array['new_album_name'];
            }
            if ($new_asset_array['title_fields']['title'] !== 'false') {
                $title_info['title'] = $new_asset_array['new_title'];
            }
            if ($new_asset_array['title_fields']['author'] !== 'false') {
                $title_info['author'] = $new_asset_array['new_author'];
            }
            if ($new_asset_array['title_fields']['date'] !== 'false') {
                $title_info['date'] = $new_asset_array['new_date'];
            }
            $title_info['organization'] = $new_asset_array['organization'];
            $title_info['copyright'] = $new_asset_array['copyright'];

            $res = gd_image_create($title_info, $file['width'], $file['height'], $file['path'] . '/tmpdir/title.jpg');
            if (!$res || !file_exists($file['path'] . '/tmpdir/title.jpg')) {
                echo "couldn't generate title " . $file['path'] . '/tmpdir/title.jpg';
                die;
            }
            $res = movie_title_from_image($file['path'] . '/tmpdir/0-title.mov', $file['path'] . '/tmpdir/title.jpg', $file['encoder']);
            if ($res) {
                echo "couldn't generate title " . $file['path'] . '/tmpdir/0-title.mov';
                die;
            }
            unlink($file['path'] . '/tmpdir/title.jpg');
        }

        $movie_array = array();
        $dir = new DirectoryIterator($file['path'] . '/tmpdir');
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $movie_array[] = $file['path'] . '/tmpdir/' . $fileinfo->getFilename();
            }
        }
        movie_join_array($movie_array, $file['path'] . '/' . $file['filename']);
        exec("rm -rf " . $file['path'] . '/tmpdir');
        exec("rm -rf " . $file['path'] . '/*count*');
        
        $video_meta = metadata2assoc_array($file['path'] . '/_metadata.xml');
        movie_qtinfo($file['path'] . '/' . $file['filename'], $qtinfo);
        $video_meta['duration'] = $qtinfo['duration'];
        assoc_array2metadata_file($video_meta, $file['path'] . '/_metadata.xml');
    }
}

$rendered_dir = $processed_dir . '/' . $new_asset_array['new_asset'] . '_' . $new_asset_array['new_album'];
$cmd = "mv $rendering_dir $rendered_dir";
exec($cmd);
