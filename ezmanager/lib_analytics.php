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
 * @package ezcast.ezmanager.lib.analytics
 */
/**
 * This file contains all methods related to learning analytics.
 */
include_once dirname(__FILE__) . '/config.inc';
include_once dirname(__FILE__) . '/lib_ezmam.php';

/**
 * Returns the number of assets contained in the given album
 * @param type $album
 */
function analytics_album_assets_count($album)
{
    global $repository_path;

    $count = 0;
    if (is_dir($repository_path . '/' . $album)) {
        $assets = glob($repository_path . '/' . $album . '/20??_??_??_??h??');
        $count = count($assets);
    }
    return $count;
}

/**
 * Returns an associative array containing the number of assets for both submit and recorder origins
 * @global type $repository_path
 * @param type $album
 * @return array
 */
function analytics_album_asset_count_by_origin($album)
{
    global $repository_path;

    $origin_array = array('submit' => 0, 'recorder' => 0);
    
    ezmam_repository_path($repository_path);
    
    if (is_dir($repository_path . '/' . $album)) {
        $assets = glob($repository_path . '/' . $album . '/20??_??_??_??h??');
        foreach ($assets as $asset_path) {
            if (strpos(file_get_contents($asset_path . '/_metadata.xml'), 'SUBMIT') !== false) {
                $origin_array['submit']++;
            } else {
                $origin_array['recorder']++;
            }
        }
    }
    return $origin_array;
}

/**
 * Returns the number of access to the given album for a specific period of time
 * @global type $repository_basedir
 * @param type $album
 * @param int $start_date the date we begin the analyse (format YYYYMMDD)
 * @param int $end_date the date we stop the analyse (format YYYYMMDD)
 * @return int the number of access to the album between the given dates
 */
function analytics_album_access_count($album, $start_date = 0, $end_date = 99999999)
{
    global $repository_basedir;
    
    if (is_nan($start_date)) {
        $start_date = 0;
    }
    if (is_nan($end_date)) {
        $end_date = 99999999;
    }
    $trace_path = $repository_basedir . '/ezplayer/';
    $trace_files = glob($trace_path . '/*ezplayer.trace');
    $previous_trace = 0;
    $access_count = 0;
    
    $access_by_date = array();
    foreach ($trace_files as $trace_file) {
        $trace_file_parts = explode('_', basename($trace_file));
        // doesn't analyse traces that are out of date
        if (is_numeric($trace_file_parts[0])) {
            if ($trace_file_parts[0] < $start_date) {
                continue;
            }
            if ($trace_file_parts[0] > $end_date && $previous_trace > $end_date) {
                break;
            }
        }
        $previous_trace = $trace_file_parts[0];
        exec("cat $trace_file | grep 'view_album_assets' | grep '$album' | cut -d '|' -f 1 | cut -d '-' -f 1,2,3 | sort | uniq -c", $output);
        //print_r($output);
        foreach ($output as $output_line) {
            $output_line = str_replace('-', '', $output_line);
            $output_part = explode(' ', trim($output_line));
            if ($output_part[1] >= $start_date && $output_part[1] <= $end_date) {
                $access_count += $output_part[0];
            }
        }
        $output = array();
    }
    return $access_count;
}
