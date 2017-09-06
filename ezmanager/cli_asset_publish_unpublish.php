<?php

/*
 * EZCAST EZmanager
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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

/**
 * @package ezcast.ezmanager.cli
 * This program is used via cron to publish or unpublish asset at a given time
 *
 * TODO same file that controller "asset_publish_unpublish".  Must be merge !
 */
require_once dirname(__FILE__) . '/config.inc';
require_once dirname(__FILE__) . '/lib_ezmam.php';
require_once dirname(__FILE__) . '/lib_various.php';
require_once __DIR__.'/../commons/lib_error.php';
require_once dirname(__FILE__) . '/lib_toc.php';

if ($argc != 4) {
    echo "usage: " . $argv[0] . ' <album> <asset> <action>
        Where <album> is the mnemonic of the album containing the asset
        --> ULB-Podcast-pub for instance
        <asset> is the id of the asset
        --> 2015_09_14_08h00 for instance
        <action> is the action proceeded by this script
        --> publish | unpublish';
    die;
}

$album = $argv[1];
$asset = $argv[2];
$action = $argv[3];

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

//
// Usual sanity checks
//
    if (!isset($album) || !isset($asset)) {
        echo "usage: " . $argv[0] . ' <album> <asset> <action>
        Where <album> is the mnemonic of the album containing the asset
        --> ULB-Podcast-pub for instance
        <asset> is the id of the asset
        --> 2015_09_14_08h00 for instance
        <action> is the action proceeded by this script
        --> publish | unpublish';
        die;
    }

if (!ezmam_album_exists($album) || !ezmam_asset_exists($album, $asset)) {
    error_print_message(ezmam_last_error());
    die;
}

$asset_meta = ezmam_asset_metadata_get($album, $asset);
$asset_meta['scheduled'] = false;
unset($asset_meta['schedule_date']);
unset($asset_meta['schedule_id']);
ezmam_asset_metadata_set($album, $asset, $asset_meta);

//
// (Un)publishing the asset, and displaying a confirmation message.
//
if ($action == 'publish') {
    $res = ezmam_asset_publish($album, $asset);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
    // Move bookmarks and stats
    move_data($album, $asset);
} elseif ($action == 'unpublish') {
    $res = ezmam_asset_unpublish($album, $asset);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
    // Move bookmarks and stats
    move_data($album, $asset);
} else {
    error_print_message('Publish_unpublish: no operation provided');
    die;
}


function move_data($album, $asset)
{
    // moves asset bookmarks from private to public
    toc_album_bookmarks_swap($album, $asset);
    $albumTo = suffix_replace($album);
    
    require_once dirname(__FILE__) . '/lib_sql_stats.php';
    db_stats_update_album($album, $albumTo);
}
