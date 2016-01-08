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

/**
 * @package ezcast.ezmanager.cli
 */

include_once 'config.inc';
include_once 'lib_scheduling.php';
include_once 'lib_ezmam.php';

ezmam_repository_path($repository_path);

$uid = $argv[1];
$job = lib_scheduling_job_find(scheduler_processing_get(), $uid);
$renderer = lib_scheduling_renderer_find(lib_scheduling_renderer_list(), $job['renderer']);
$album = $job['album'];
$asset = $job['asset'];
$asset_meta = ezmam_asset_metadata_get($album, $asset);
$job_dir = basename($job['location']);

// Send the video to the renderer download dir
$cmd = $rsync_pgm . ' -L -r -e ssh -tv  --partial-dir=' . $renderer['downloading_dir'] . ' ' . $job['location'] . ' ' . $renderer['client'] . '@' . $renderer['host'] .  ':' . $renderer['downloaded_dir']  . ' 2>&1';

// try 3 times
for($i = 0; $i < 3; $i++) {
    exec($cmd, $out, $err);
    if($err) {
        lib_scheduling_warning('Scheduler::job_perform[wait]{rsync: ' . $cmd. '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
        sleep(600);
    }
    else break;
}

// Send fail
if($err) {
    lib_scheduling_error('Scheduler::job_perform[fail]{rsync: ' . $cmd . '}('  . $err . ') |::> ' . implode("\n", $out) . ' <::|');

    // tag the renderer as not repsonding
    $renderers = lib_scheduling_renderer_list();
    foreach($renderers as $_) if($_['host'] == $renderer['host']) $_['status'] = 'not responding';
    lib_scheduling_renderer_generate($renderers);
    lib_scheduling_alert('Scheduler::job_perform[not responding]{renderer: ' . $renderer['host'] . '}');

    // move the job back to the queue
    lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('queue-path') . '/' . $job['basename']);

    die;
}

// Launch the rendering
$cmd= $ssh_pgm . ' ' . $renderer['client'] . '@' . $renderer['host'] . ' "' . $renderer['launch'] . ' ' . $job_dir . ' 2>&1"';
$t1=time();
exec($cmd, $cmdoutput, $returncode);
$t2=time();
$dt=$t2-$t1;

file_put_contents($job['location']. '/renderer.log', implode("\n", $cmdoutput));

// Rendering fail
if($returncode) {
 $asset_meta['status']='failed';
 $res=ezmam_asset_metadata_set($album, $asset, $asset_meta);
 lib_scheduling_error('Scheduler::job_perform[fail]{encode: ' . $cmd . '}('  . $returncode . ')[' . $dt .' sec]');
 lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('failed-path') . '/' . $job['basename']);
 die;
}

// Retrieve the video from the renderer
$asset_meta['status']='encoded';
$res=ezmam_asset_metadata_set($album, $asset, $asset_meta);

$cmd = $rsync_pgm . ' -L -r -e ssh -tv  --partial-dir=' . $render_finished_partial_upload_dir  . ' ' . $renderer['client'] . '@' . $renderer['host'] . ':' . $renderer['processed_dir'] . '/' . $job_dir . ' ' . dirname($job['location'] . ' 2>&1');

// try 3 times
for($i = 0; $i < 3; $i++) {
    exec($cmd, $out, $err);
    if($err) {
        lib_scheduling_warning('Scheduler::job_perform[wait]{rsync: ' . $cmd. '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
        sleep(600);
    }
    else break;
}

// Retrieve fail
if($err) {
	lib_scheduling_error('Scheduler::job_perform[fail]{rsync: ' . $cmd . '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
	lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('failed-path') . '/' . $job['basename']);
	die;
}

// Retrieve Success
lib_scheduling_job_metadata_set($job, 'done', date('Y-m-d H:i:s'), lib_scheduling_config('processing-path'));
lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('processed-path') . '/' . $job['basename']);
lib_scheduling_file_move($job['location'], $render_finished_upload_dir . '/' . $job_dir);

// Now that the files have been copied on EZcast server, we delete them from EZrenderer
if ($renderer['processed_dir'] . '/' . $job_dir != '' 
        && $renderer['processed_dir'] . '/' . $job_dir != '/'){
            $cmd= $ssh_pgm . ' ' . $renderer['client'] . '@' . $renderer['host'] . ' " rm -rf  ' . $renderer['processed_dir'] . '/' . $job_dir . ' 2>&1"';
            exec($cmd, $out, $err);
        }


// Now run cli_render_maminsert
$cmd="$php_cli_cmd cli_rendered_maminsert.php  $album $asset $render_finished_upload_dir/$job_dir >> $render_finished_upload_dir/$job_dir/rendered_maminsert.log 2>&1";
exec($cmd, $cmdoutput, $returncode);
if($returncode) {
 //non zero return code -> something bad happened
 $msg = "Submit_intro_title_movie failed";
 $asset_meta['status']='failed';
 $res=ezmam_asset_metadata_set($album, $asset, $asset_meta);
}
else{
	lib_scheduling_notice('Scheduler::job_perform[success]{' . $job['uid'] . '}');
	scheduler_schedule();
}

?>
