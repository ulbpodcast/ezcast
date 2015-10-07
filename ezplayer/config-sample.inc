<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
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

$appname="ezplayer";

$user_files_path = "$repository_basedir/ezplayer/users";
$bookmarks_validation_file = "./bookmarks.xsd";
$message_of_the_day = "$repository_basedir/ezplayer/message_of_day.html";

$default_personal_bm_order = "reverse_chron"; // may be [reverse_chron | chron]
$default_official_bm_order = "chron"; // may be [reverse_chron | chron]
$default_display_thread = true;
$default_display_thread_notif = true;
$default_display_count = true;

$accepted_media_qualities = array('high', 'low', 'ezplayer'); // Media quality prefixes accepted by the distribution script
$accepted_media_types = array('cam', 'slide');    // Media types accepted by the distribution script (todo: upload)

$template_folder = 'tmpl/';
$ezplayer_logs = $basedir . '/ezplayer/ezplayer.log';
$ezplayer_trace_path = $repository_basedir . '/ezplayer/ezplayer_traces/';

$valid_extensions = array('xml'); // accepted extensions for imported bookmarks
$trace_on = false; // determines whether we want to enable traces on actions or not

$cache_limit = 5; // number of trending threads that are displayed on level 2 

$streaming_video_player = "flowplayer"; // defines the video player to use for live stream ('momovi' | 'flowplayer')

?>
