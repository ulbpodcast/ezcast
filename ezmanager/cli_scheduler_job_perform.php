<?php

/**
 * @package ezcast.ezmanager.cli
 */

include_once __DIR__.'/config.inc';
require_once __DIR__.'/../commons/lib_scheduling.php';
include_once __DIR__.'/lib_ezmam.php';

if ($argc != 2) {
    echo "Usage: cli_scheduler_job_perform <job_uid>" . PHP_EOL;
    $logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "cli_scheduler_job_perform called with wrong arg count $argc", array(basename(__FILE__)));
    exit(1);
}
Logger::$print_logs = true;

//overwrite shutdown function: log and set rendering to failure in share of php shutdown
register_shutdown_function('job_perform_shutdown_function');

$uid = $argv[1];
$logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "cli_scheduler_job_perform called with uid $uid", array(basename(__FILE__)));


ezmam_repository_path($repository_path);

$job = lib_scheduling_job_find(scheduler_processing_get(), $uid);
$renderer = lib_scheduling_renderer_find(lib_scheduling_renderer_list(), $job['renderer']);
$album = $job['album'];
$asset = $job['asset'];
$asset_meta = ezmam_asset_metadata_get($album, $asset);
$job_dir = basename($job['location']);

// Send the video to the renderer download dir
$cmd = $rsync_pgm . ' -L -r -e ssh -tv  --partial-dir=' . $renderer['downloading_dir'] . ' ' . $job['location'] . ' ' . $renderer['client'] . '@' . $renderer['host'] .  ':' . $renderer['downloaded_dir']  . ' 2>&1';
echo $cmd . PHP_EOL;
// try 3 times
for ($i = 0; $i < 3; $i++) {
    exec($cmd, $out, $err);
    if ($err) {
        lib_scheduling_warning('Scheduler::job_perform[wait]{rsync: ' . $cmd. '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
        sleep(600);
    } else {
        break;
    }
}

// Send fail
if ($err) {
    lib_scheduling_error('Scheduler::job_perform[fail]{rsync: ' . $cmd . '}('  . $err . ') |::> ' . implode("\n", $out) . ' <::|');
    $logger->log(EventType::MANAGER_RENDERING, LogLevel::CRITICAL, "Failed to send video to renderer for job $uid. Location: ". $job['location'] . ". Cmd was $cmd", array(basename(__FILE__)));

    // tag the renderer as not repsonding
    $renderers = lib_scheduling_renderer_list();
    foreach ($renderers as $_) {
        if ($_['host'] == $renderer['host']) {
            $_['status'] = 'not responding';
        }
    }
    lib_scheduling_renderer_generate($renderers);
    lib_scheduling_alert('Scheduler::job_perform[not responding]{renderer: ' . $renderer['host'] . '}');

    // move the job back to the queue
    lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('queue-path') . '/' . $job['basename']);

    die;
}
$logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "Successfully sent videos for job $uid. Location: ". $job['location'], array(basename(__FILE__)));

// Launch the rendering
$cmd= $ssh_pgm . ' -oBatchMode=yes ' . $renderer['client'] . '@' . $renderer['host'] . ' "' . $renderer['launch'] . ' ' . $job_dir . ' 2>&1"';
$t1=time();
echo $cmd . PHP_EOL;
exec($cmd, $cmdoutput, $returncode);
$t2=time();
$dt=$t2-$t1;
echo "Output result in " .$job['location'] . PHP_EOL;
file_put_contents($job['location']. '/renderer.log', implode("\n", $cmdoutput));

// Rendering fail
if ($returncode) {
    $asset_meta['status']='failed';
    $res=ezmam_asset_metadata_set($album, $asset, $asset_meta);
    lib_scheduling_error('Scheduler::job_perform[fail]{encode: ' . $cmd . '}('  . $returncode . ')[' . $dt .' sec]');
    $from_dir = lib_scheduling_config('processing-path') . '/' . $job['basename'];
    $to_dir = lib_scheduling_config('failed-path') . '/' . $job['basename'];
    lib_scheduling_file_move($from_dir, $to_dir);
    $logger->log(EventType::MANAGER_RENDERING, LogLevel::CRITICAL, "Rendering failed for job $uid. Album $album. Asset: $asset. Moving job file from dir $from_dir to dir $to_dir. Cmd was $cmd", array(basename(__FILE__)));
    die;
}
$logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "Successfully rendered job $uid. Album $album. Asset: $asset", array(basename(__FILE__)));

echo "Retrieving from renderer..." . PHP_EOL;

// Retrieve the video from the renderer
$asset_meta['status']='encoded';
$res=ezmam_asset_metadata_set($album, $asset, $asset_meta);

$cmd = $rsync_pgm . ' -L -r -e ssh -tv  --partial-dir=' . $render_finished_partial_upload_dir  . ' ' . $renderer['client'] . '@' . $renderer['host'] . ':' . $renderer['processed_dir'] . '/' . $job_dir . ' ' . dirname($job['location'] . ' 2>&1');

// try 3 times
for ($i = 0; $i < 3; $i++) {
    exec($cmd, $out, $err);
    if ($err) {
        lib_scheduling_warning('Scheduler::job_perform[wait]{rsync: ' . $cmd. '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
        sleep(600);
    } else {
        break;
    }
}

// Retrieve fail
if ($err) {
    $logger->log(EventType::MANAGER_RENDERING, LogLevel::CRITICAL, "Failed to retrieve resulting videos for job $uid. Album $album. Asset: $asset. Cmd was: $cmd", array(basename(__FILE__)));
    lib_scheduling_error('Scheduler::job_perform[fail]{rsync: ' . $cmd . '}('  . $err . ') |::>' . implode("\n", $out) . ' <::|');
    lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('failed-path') . '/' . $job['basename']);
    die;
}

// Retrieve Success
$logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "Successfully retrived resulting videos for job $uid. Album $album. Asset: $asset", array(basename(__FILE__)));
lib_scheduling_job_metadata_set($job, 'done', date('Y-m-d H:i:s'), lib_scheduling_config('processing-path'));
lib_scheduling_file_move(lib_scheduling_config('processing-path') . '/' . $job['basename'], lib_scheduling_config('processed-path') . '/' . $job['basename']);
lib_scheduling_file_move($job['location'], $render_finished_upload_dir . '/' . $job_dir);

// Now that the files have been copied on EZcast server, we delete them from EZrenderer
if ($renderer['processed_dir'] . '/' . $job_dir != ''
   && $renderer['processed_dir'] . '/' . $job_dir != '/') {
    $cmd= $ssh_pgm . ' -oBatchMode=yes ' . $renderer['client'] . '@' . $renderer['host'] . ' " rm -rf  ' . $renderer['processed_dir'] . '/' . $job_dir . ' 2>&1"';
    exec($cmd, $out, $err);
    if ($err != 0) {
        $logger->log(EventType::MANAGER_RENDERING, LogLevel::ERROR, "Failed to delete videos on renderer for job $uid. Album $album. Asset: $asset. Renderer: " . $renderer['host'], array(basename(__FILE__)));
    }
}

// Now run cli_render_maminsert
$cmd="$php_cli_cmd ". __DIR__."/cli_rendered_maminsert.php  $album $asset $render_finished_upload_dir/$job_dir >> $render_finished_upload_dir/$job_dir/rendered_maminsert.log 2>&1";
exec($cmd, $cmdoutput, $returncode);
if ($returncode != 0) {
    //non zero return code -> something bad happened
    $logger->log(EventType::MANAGER_RENDERING, LogLevel::CRITICAL, "Call to cli_rendered_maminsert failed. Cmd was $cmd", array(basename(__FILE__)));
    $msg = "Submit_intro_title_movie failed";
    set_rendering_failure($album, $asset, $asset_meta);
    exit(2);
}

$logger->log(EventType::MANAGER_RENDERING, LogLevel::DEBUG, "Job perform finished with success for job $uid. Album $album. Asset: $asset", array(basename(__FILE__)));
lib_scheduling_notice('Scheduler::job_perform[success]{' . $job['uid'] . '}');
scheduler_schedule();

exit(0);

function set_rendering_failure($album, $asset, $asset_meta)
{
    $asset_meta['status']='failed';
    ezmam_asset_metadata_set($album, $asset, $asset_meta);
}

function job_perform_shutdown_function()
{
    global $job;
    global $cmd;
    global $logger;
    
    //include if not already
    require_once(__DIR__.'/../commons/custom_error_handling.php');
    
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if (is_critical_error($error['type'])) {
        $file = $job['location']."/job_perform_crashed.txt";
        $content =  $error["file"].':'.$error["line"] . PHP_EOL;
        $content .= $error['message'] . PHP_EOL;
        $content .= "Last command:".PHP_EOL;
        $content .= $cmd.PHP_EOL;
        file_put_contents($file, $content);

        global $asset_meta;
        global $album;
        global $asset;
        set_rendering_failure($album, $asset, $asset_meta);

        global $uid;
        $logger->log(EventType::MANAGER_RENDERING, LogLevel::CRITICAL, "Rendering script crashed for job $uid. More completed output in $file", array(basename(__FILE__)));
    }
    
    //call default shutdown_handler
    shutdown_handler();
}
