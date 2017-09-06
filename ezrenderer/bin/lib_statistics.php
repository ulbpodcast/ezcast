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

/**
 * Returns the maximum number of threads used by a single rendering
 * @global type $num_threads
 * @return type
 */
function get_max_num_threads()
{
    global $num_threads;
    
    return $num_threads;
}

/**
 * Returns the maximum number of jobs that can run simultaneously
 * @global type $num_jobs
 * @return type
 */
function get_max_num_jobs()
{
    global $num_jobs;
    
    return $num_jobs;
}

/**
 * Returns the number of jobs currently running
 */
function get_num_jobs()
{
    $cmd = 'ps ax | grep ffmpeg';
    exec($cmd, $output, $val);
    
    /*if($val != 0)
        return 0;*/
    return count($output) - 2;
}

/**
 * Returns an array of PIDs corresponding to the current renders
 */
function get_job_pids()
{
    $res = array();
    
    $cmd = 'ps ax | grep intro_title_movie.bash';
    exec($cmd, $output, $val);

    foreach ($output as $o) {
        if (strpos($o, 'grep') === false) {
            $infos = explode(' ', $o);
            $res[] = $infos[0];
        }
    }
    
    return $res;
}

function get_job_pid($asset_name)
{
    //exec("ps ax | grep ffmpeg | grep ".$asset_name, $output, $val);
    exec("ps ax | grep ./intro_title_movie | grep ".$asset_name, $output, $val);
    var_dump($asset_name);
    var_dump($output);
    $pids = array();
    foreach ($output as $job) {
        $jobinfos = explode(' ', $job);
        $pids[] = $jobinfos[0];
    }
    
    return $pids;
}

/**
 * Returns an assoc array with infos (PID, time, #threads) for each PID
 */
function get_job_infos()
{
    $res = array();
    
    //$cmd = 'ps -ax | grep intro_title_movie.bash';
    $cmd = 'ps axO state | grep intro_title_movie.bash | tr -s \' \'';
    exec($cmd, $output, $val);

    foreach ($output as $o) {
        if (strpos($o, 'grep') === false) {
            $infos = explode(' ', $o);
            $pid  = $infos[0];
            $time = $infos[4];
            $state = $infos[1];
            
            $res[$pid] = array(
                'pid' => $pid,
                'time' => $time,
                'state' => $state
            );
        }
    }
    
    return $res;
}

/**
 * Returns some sort of metrics regarding the current workload
 */
function get_load()
{
    $cmd = 'uptime';
    exec($cmd, $output, $val);
    $infos = explode(' ', $output[0]);
    
    //var_dump($output);
    
    return substr($infos[count($infos) - 3], 0, -1);
}
