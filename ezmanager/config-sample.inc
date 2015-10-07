<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	   Arnaud Wijns <awijns@ulb.ac.be>
 *         Antoine Dewilde
 * UI Design by Julien Di Pietrantonio
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

error_reporting(E_PARSE | E_ERROR);
require "../commons/config.inc";

$appname="EZmanager";

//recorder parameters cli_recorder_download_pgm
$recorder_upload_dir="$repository_basedir/queues/recorder_uploads";
$recorder_upload_ok_dir="$repository_basedir/queues/recorder_uploads_ok";
$recorder_upload_failed_dir="$repository_basedir/queues/recorder_uploads_failed";
$recorder_download_pgm="$ezmanager_basedir/cli_recorder_download.php";
$streaming_asset_delete_pgm="$ezmanager_basedir/cli_streaming_asset_delete.php";
$asset_publish_pgm="$ezmanager_basedir/cli_asset_publish_unpublish.php";

//submit parameters
$submit_upload_dir="$repository_basedir/queues/submit_uploads";
$submit_upload_ok_dir="$repository_basedir/queues/submit_uploads_ok";
$submit_upload_failed_dir="$repository_basedir/queues/submit_uploads_failed";

$recorder_upload_to_server="$recorder_recorddir/upload_to_server/";//path of folder containing recordings on the recorder (mac mini)
$recorder_upload_ok="$recorder_recorddir/upload_ok/";//same but for recordings sucessfully downloaded (they will be deleted later by a cron)
$max_download_retries=500;//number of rsync retries
//what program to call when downloading from classroom or uploading from user
$recorder_mam_insert_pgm="$ezmanager_basedir/cli_mam_insert.php";
//pgm called by maminsert
$submit_intro_title_movie_pgm="$ezmanager_basedir/cli_submit_intro_title_movie.php";
$render_root_path="$repository_basedir/queues/rendering/";
$render_finished_partial_upload_dir="$repository_basedir/queues/rendering/uploading";
$render_finished_upload_dir="$repository_basedir/queues/rendering/processed";

$dir_date_format="Y_m_d_H\hi";// ls friendly date to create directories/files

// used by web_index when creating a new album for titling the videos
$default_intro = 'Semeur';
$intros = array( array('value' => 'Semeur', 'label' => 'ULB Podcast'));
$default_add_title = "FlyingTitle";
// titlings value: false if no titling needed / name of the titling if value
$titlings = array( array('value' => 'FlyingTitle', 'label' => 'Flying Title (default)'));
                //   array('value' => 'RevealTitle', 'label' => 'Reveal'));
$default_downloadable = true;


// used by lib_ezmam (ezmanager)
$accepted_media_qualities = array('high', 'low', 'ezplayer'); // Media quality prefixes accepted by the distribution script
$accepted_media_types = array('cam', 'slide');    // Media types accepted by the distribution script (todo: upload)

$template_folder = 'tmpl/';
$ezmanager_logs="$ezmanager_basedir/ezmanager.log";

// used by ezmanager for asset edit
$title_max_length = 70; // Maximum number of characters for the title

// used by ezmanager for asset submit
$valid_extensions = array('mov', 'm4v', 'mp4', 'mpg4', 'mpg', 'nuv', 'ac3', 'mpeg4', 'avi', 'mpeg', 'flv', 'wmv', 'mka', 'mks', 'rmvb', 'divx', 'xvid', 'vob', 'mkv', 'f4v');

// used by web_distribute.php
$flash_only_browsers = array('Firefox');

// used by web_index.php to set the size of each chunck of file that is uploaded via the submit form
$upload_slice_size = 1024 * 1024;

?>
