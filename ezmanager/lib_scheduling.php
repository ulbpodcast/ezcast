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
 * @package ezcast.ezmanager.lib.scheduling
 */

/**
 * DEBUG
 *
 *  0 : Disabled
 *  1 : Error
 *  2 : Trace + 1
 *  3 : Alert + 2
 *  4 : warning + 3
 *  5 : notice + 4
 */
define('DEBUG', 5);

require_once __DIR__ . '/config.inc';

/*******************************/
/****** S C H E D U L E R ******/
/*******************************/

/**
 * Schedule a video
 *
 * This method is called only once at at time
 */
function scheduler_schedule()
{
    lib_scheduling_notice('Scheduler::schedule[start]');

    // init the queue (get all the jobs from the scheduler queue folder)
    $queue = lib_scheduling_queue_init();

    $saturate = false;

    // loop on every job until there is no more job to schedule or all renderer are busy
    while (count($queue) && !$saturate) {
        // next job
        $job = lib_scheduling_queue_top($queue);

        // init renderers
        $renderers = lib_scheduling_renderer_list();

        $choice = false;
        // iterate over render and attribuate the job to the optimal one
        foreach ($renderers as $renderer) {

            // check the renderer availibility
            if (!lib_scheduling_renderer_is_available($renderer)) {
                continue;
            }

            // if no choice, pick the first fit. Otherwhise find the optimal
            if (!$choice) {
                $choice = $renderer;
                continue;
            } elseif (lib_scheduling_renderer_is_better_than($renderer, $choice, $job)) {
                $choice = $renderer;
            }
        }

        // no renderer were available for a job which means the system is saturated
        if (!$choice) {
            $saturate = true;
            lib_scheduling_warning('Scheduler::schedule[saturate]');
            continue;
        }

        // assign the job to the renderer
        if (lib_scheduling_renderer_assign($choice, $job)) {
            lib_scheduling_job_remove($queue, $job);
            lib_scheduling_notice('Scheduler::schedule[scheduled]{' . $job['uid'] . ' to ' . $choice['host'] . '}');
        }
    }

    // release the queue
    lib_scheduling_queue_close();

    lib_scheduling_notice('Scheduler::schedule[stop]');
}

/**
 * Append a new job
 *
 * @param array $job The job
 * @return Whehter the job has been successfully added
 */
function scheduler_append($job)
{
    if (!$job['created']) {
        $job['created'] = date('Y-m-d H:i:s');
    }
    if (!$job['priority']) {
        $job['priority'] = lib_scheduling_config('default-priority');
    }

    $job['uid'] = sha1($job['sender'] . strtotime($job['created']));

    $status = lib_scheduling_job_write($job, lib_scheduling_config('queue-path'));

    if ($status) {
        lib_scheduling_notice('Scheduler::append[success]{' . $job['uid'] . '}');
    } else {
        lib_scheduling_warning('Scheduler::append[fail]{' . $job['uid'] . '}');
    }

    return $status;
}

/**
 * Remove a job from the queue
 *
 * @param  string $uid The job uid
 * @return Whether the job has been successfully removed
 */
function scheduler_remove($uid)
{
    $status = lib_scheduling_queue_delete(lib_scheduling_job_find(lib_scheduling_queue_init(), $uid));
    lib_scheduling_queue_close();

    if ($status) {
        lib_scheduling_notice('Scheduler::remove[success]{' . $uid . '}');
    } else {
        lib_scheduling_warning('Scheduler::remove[fail]{' . $uid . '}');
    }

    return $status;
}

/**
 * Put a video in a waiting list
 *
 * @param string $uid The job uid
 * @return boolean Whether the freeze has been successfully done
 */
function scheduler_freeze($uid)
{
    $job = lib_scheduling_job_find(lib_scheduling_queue_init(), $uid);
    $status = lib_scheduling_file_move(lib_scheduling_config('queue-path') . '/' . $job['basename'], lib_scheduling_config('frozen-path') . '/' . $job['basename']);
    lib_scheduling_queue_close();

    if ($status) {
        lib_scheduling_notice('Scheduler::freeze[success]{' . $uid . '}');
    } else {
        lib_scheduling_warning('Scheduler::freeze[fail]{' . $uid . '}');
    }

    return $status;
}

/**
 * Remove a video from the waiting list and append it to the queue
 *
 * @param string $uid The job uid
 * @return boolean Whether the unfreeze has been successfully done
 */
function scheduler_unfreeze($uid)
{
    lib_scheduling_queue_init();
    $job = lib_scheduling_job_find(scheduler_frozen_get(), $uid);
    $status = lib_scheduling_file_move(lib_scheduling_config('frozen-path') . '/' . $job['basename'], lib_scheduling_config('queue-path') . '/' . $job['basename']);
    lib_scheduling_queue_close();

    if ($status) {
        lib_scheduling_notice('Scheduler::unfreeze[success]{' . $uid . '}');
    } else {
        lib_scheduling_warning('Scheduler::unfreeze[fail]{' . $uid . '}');
    }

    return $status;
}

/**
 * Return all job in the queue
 *
 * @return array The currently queued jobs
 */
function scheduler_queue_get()
{
    $queue = lib_scheduling_job_read_all(lib_scheduling_config('queue-path'));
    usort($queue, 'lib_scheduling_queue_has_higher_priority_than');
    return $queue;
}

/**
 * Return all job currently processed
 *
 * @return array The currently processed jobs
 */
function scheduler_processing_get()
{
    return lib_scheduling_job_read_all(lib_scheduling_config('processing-path'));
}

/**
 * Return all processed job
 *
 * @return array The processed jobs
 */
function scheduler_processed_get()
{
    return lib_scheduling_job_read_all(lib_scheduling_config('processed-path'));
}

/**
 * Return all frozen job
 *
 * @return array The frozen jobs
 */
function scheduler_frozen_get()
{
    return lib_scheduling_job_read_all(lib_scheduling_config('frozen-path'));
}

/**
 * Return all failed job
 *
 * @return array The frozen jobs
 */
function scheduler_failed_get()
{
    return lib_scheduling_job_read_all(lib_scheduling_config('failed-path'));
}

/**
 * Retrieve the information about specific job
 *
 * @param string $uid The job id
 * @return array Job information
 */
function scheduler_job_info_get($uid)
{
    $job = null;

    if ($job = lib_scheduling_job_find(scheduler_queue_get(), $uid)) {
        $job['status'] = 'queue';
    } elseif ($job = lib_scheduling_job_find(scheduler_processing_get(), $uid)) {
        $job['status'] = 'processing';
        $renderer = lib_scheduling_renderer_find(lib_scheduling_renderer_list(), $job['renderer']);
        $status = lib_scheduling_renderer_job_info($renderer, $job);
        if (!$status) {
            lib_scheduling_warning('Scheduler::renderer_job_info_get[fail]{' . $uid . ' on ' . $renderer['host'] . '}');
        }
    } elseif ($job = lib_scheduling_job_find(scheduler_processed_get(), $uid)) {
        $job['status'] = 'processed';
    } elseif ($job = lib_scheduling_job_find(scheduler_frozen_get(), $uid)) {
        $job['status'] = 'frozen';
    } elseif ($job = lib_scheduling_job_find(scheduler_failed_get(), $uid)) {
        $job['status'] = 'failed';
    } else {
        lib_scheduling_warning('Scheduler::renderer_job_info_get[not found]{' . $uid . '}');
    }

    return $job;
}

/**
 * Kill a job on the corresponding renderer
 *
 * @param string $uid The job id
 * @return boolean Whether the job has been successfully killed
 */
function scheduler_job_kill($uid)
{
    $job = lib_scheduling_job_find(scheduler_processing_get(), $uid);
    $renderer = lib_scheduling_renderer_find(lib_scheduling_renderer_list(), $job['renderer']);

    $status = lib_scheduling_renderer_job_kill($renderer, $job);

    if ($status) {
        lib_scheduling_notice('Scheduler::renderer_job_kill[success]{' . $job['uid'] . '(' . $job['album'] . ' - ' . $job['asset'] . ') on ' . $renderer['host'] .'}');
    } else {
        lib_scheduling_alert('Scheduler::renderer_job_kill[fail]{' . $job['uid'] . '(' . $job['album'] . ' - ' . $job['asset'] . ') on ' . $renderer['host'] .'}');
    }

    return $status;
}

/**
 * Increase the job priority
 *
 * @param  int $uid The job uid
 * @return Whether the job priority has been successfully increased
 */
function scheduler_job_priority_up($uid)
{
    $status = lib_scheduling_queue_up(lib_scheduling_job_find(lib_scheduling_queue_init(), $uid));
    lib_scheduling_queue_close();

    if ($status) {
        lib_scheduling_notice('Scheduler::job_priority_up[success]{' . $uid .'}');
    } else {
        lib_scheduling_warning('Scheduler::job_priority_up[fail]{' . $uid .'}');
    }

    return $status;
}

/**
 * Decrease the job priority
 *
 * @param  int $uid The job uid
 * @return Whether the job priority has been successfully decreased
 */
function scheduler_job_priority_down($uid)
{
    $status = lib_scheduling_queue_down(lib_scheduling_job_find(lib_scheduling_queue_init(), $uid));
    lib_scheduling_queue_close();

    if ($status) {
        lib_scheduling_notice('Scheduler::job_priority_down[success]{' . $uid .'}');
    } else {
        lib_scheduling_warning('Scheduler::job_priority_down[fail]{' . $uid .'}');
    }

    return $status;
}

/*******************************/
/*********** J O B *************/
/*******************************/

/**
 * Read a job meta data and return the corresponding array
 *
 * <?xml version="1.0" standalone="yes"?>
 * <job>
 *  <uid></uid>
 *  <file></file>
 *  <origin></origin>
 *  <sender></sender>
 *  <priority></priority>
 *  <renderer></renderer>
 *  <created></created>
 *  <sent></sent>
 *  <done></done>
 * </job>
 *
 * @param string $file The file name
 * @return array The job
 */
function lib_scheduling_job_read($file)
{
    $job = array();

    foreach (simplexml_load_file($file) as $tag => $value) {
        $job[$tag] = (string) $value;
    }

    $job['basename'] = basename($file);

    return $job;
}

/**
 * Write a job metadata down
 *
 * array(
 *  'path' => where the job has to be written (autogenerate if empty)
 *  'uid' => the job uid
 *  'file' => path to the video directory
 *  'origin' => submit or in classroom
 *  'sender' => sender netid
 *  'priority' => special priority
 *  'renderer' => when processing, the renderer name
 *  'created' => when the job has been created
 *  'sent' => when the video has been sent to the renderer
 *  'done' => when the job has been finished
 * )
 *
 * @param array $job The job
 * @param string $dir The destination directory
 * @return boolean Whether the job has been successfully written
 */
function lib_scheduling_job_write($job, $dir)
{
    // default name
    if (!$job['name']) {
        $job['name'] = 'no_name';
    }

    // file name
    if (!$job['basename']) {
        $job['basename'] = lib_scheduling_file_safe($job['created'] . '_' . $job['sender']  . '_' . $job['name']) . '.xml';
    }

    // do not write the basename neither the status(redondant)
    $basename = $job['basename'];
    unset($job['basename']);
    unset($job['status']);

    $xml = new SimpleXMLElement("<job></job>");

    foreach ($job as $key => $value) {
        $xml->addChild($key, $value);
    }

    return $xml->asXML($dir . '/' . $basename);
}

/**
 * Read all the job in the given dir
 *
 * @param string $dir The enclosing dir
 * @return array All jobs contained in the directory
 */
function lib_scheduling_job_read_all($dir)
{
    $files = lib_scheduling_file_ls($dir);

    $jobs = array();
    foreach ($files as $file) {
        $jobs[] = lib_scheduling_job_read($file);
    }

    return $jobs;
}

/**
 * Return the job based on its uid
 *
 * @param array $jobs The jobs
 * @param array $uid The job uid
 */
function lib_scheduling_job_find($jobs, $uid)
{
    for ($i = 0; $i < count($jobs); ++$i) {
        if ($jobs[$i]['uid'] == $uid) {
            return $jobs[$i];
        }
    }

    lib_scheduling_warning('Scheduler::job_find[not found]{' . $uid . '}');

    return null;
}

/**
 * Remove a job from the list
 *
 * @param array $jobs The jobs
 * @param array $job The job uid
 */
function lib_scheduling_job_remove(&$jobs, $job)
{
    $index = -1;
    for ($i = 0; $i < count($jobs); ++$i) {
        if ($jobs[$i]['uid'] == $job['uid']) {
            $index = $i;
        }
    }
    if ($index > -1) {
        array_splice($jobs, $index, 1);
    }
}

/**
 * Add a metadata to a job
 * @param array $job
 * @param string $tag
 * @param string $value
 * @param string $dir
 * @return boolean Whether the metadata has been sucessfully added
 */
function lib_scheduling_job_metadata_set($job, $tag, $value, $dir)
{
    $job[$tag] = $value;
    return lib_scheduling_job_write($job, $dir);
}

/*******************************/
/********* Q U E U E ***********/
/*******************************/


/**
 * Initialize the queue
 *
 * /!\ take the semaphore
 *
 * @return array The queue
 */
function lib_scheduling_queue_init()
{
    lib_scheduling_sema_take();
    return lib_scheduling_job_read_all(lib_scheduling_config('queue-path'));
}

/**
 * Close the queue
 *
 * /!\ release the semaphore
 */
function lib_scheduling_queue_close()
{
    lib_scheduling_sema_release();
}

/**
 * Get the next job from the queue
 *
 * @param array $queue The queue
 * @return array The next job
 */
function lib_scheduling_queue_top($queue)
{
    $top = $queue[0];

    foreach ($queue as $job) {
        if (lib_scheduling_queue_has_higher_priority_than($job, $top) < 0) {
            $top = $job;
        }
    }

    return $top;
}

/**
 * Remove from the queue and delete the meta-data
 *
 * @param array $queue The queue
 * @param array $job The job uid
 */
function lib_scheduling_queue_delete(&$queue, $job)
{
    lib_scheduling_job_remove($queue, $job);
    return lib_scheduling_file_rm(config('queue-path') . '/' . $job['basename']);
}

/**
 * Return whether the first job has a higher priority than the second
 *
 * @param array $first The first job
 * @param array $second The secon job
 * @return boolean Whether the first job has a higher priority than the second
 */
function lib_scheduling_queue_has_higher_priority_than($first, $second)
{
    if ($first['priority'] == $second['priority']) {
        return strtotime($first['created']) - strtotime($second['created']);
    }
    return $first['priority'] - $second['priority'];
}

/**
 * Increase the job priority
 *
 * @param array $job The job
 */
function lib_scheduling_queue_up($job)
{
    return lib_scheduling_job_metadata_set($job, 'priority', intval($job['priority']) -1, lib_scheduling_config('queue-path'));
}

/**
 * Decrease the job priority
 *
 * @param array $job The job
 */
function lib_scheduling_queue_down($job)
{
    return lib_scheduling_job_metadata_set($job, 'priority', intval($job['priority']) +1, lib_scheduling_config('queue-path'));
}

/*******************************/
/****** R E N D E R E R S ******/
/*******************************/

/**
 * Return the list of renderers and their configs
 *
 * array(
 *  'name' => the renderer name
 *  'host' => the renderer host name
 *  'client' => the ssh client
 *  'status' => the renderer status (up, down, busy)
 *  'downloading_dir' => the incoming video dir
 *  'downloaded_dir' => the downloaded video dir
 *  'processed_dir' => the processed video dir
 *  'php' => the php path
 *  'statistics' => the statistics script (php)
 *  'launch' => the encoding launch script (bash)
 *  'kill' => the kill script (php)
 * )
 *
 * @return array All renderers
 */
function lib_scheduling_renderer_list()
{
    // return require __DIR__ . '/renderers.inc';
    return require __DIR__ . '/../commons/renderers.inc';
}

/**
 * Write a renderer configuration
 *
 * array(
 *  'name' => the renderer name
 *  'host' => the renderer host name
 *  'client' => the ssh client
 *  'status' => the renderer status (up, down, busy)
 *  'downloading_dir' => the incoming video dir
 *  'downloaded_dir' => the downloaded video dir
 *  'processed_dir' => the processed video dir
 *  'php' => the php path
 *  'statistics' => the statistics script (php)
 *  'launch' => the encoding launch script (bash)
 *  'kill' => the kill script (php)
 * )
 *
 * @return boolean Whether the generation has been successfully done
 */
function lib_scheduling_renderer_generate($renderers)
{
    $data = array('name', 'host', 'client', 'status', 'downloading_dir', 'downloaded_dir', 'processed_dir', 'statistics', 'launch', 'kill', 'php');

    $res = "<?php\n// Renderer.inc\n// Configuration file\n\n";
    $res .= "return array(\n";

    foreach ($renderers as $renderer) {
        $res .= "  array(\n";
        foreach ($renderer as $key => $value) {
            if (in_array($key, $data)) {
                $res .= "    '$key' => '$value',\n";
            }
        }
        $res .= "  ),\n";
    }

    $res .= ");\n\n?>";

    return file_put_contents(dirname(__FILE__) . '/renderers.inc', $res);
}

/**
 * Return the correct renderer based on a hostname
 * @param array $renderers The renderer list
 * @param string $hostname The renderer hostname
 * @return array The associated renderer or null
 */
function lib_scheduling_renderer_find($renderers, $hostname)
{
    foreach ($renderers as $renderer) {
        if ($renderer['host'] == $hostname) {
            return $renderer;
        }
    }

    lib_scheduling_warning('Scheduler::renderer_find[not found]{' . $hostname . '}');

    return null;
}

/**
 * Return whether the renderer is available or not
 *
 * @param array $renderer The renderer
 * @return boolean Whether the renderer is available or not
 */
function lib_scheduling_renderer_is_available($renderer)
{
    if ($renderer['status'] != 'enabled') {
        return false;
    }

    $renderer = lib_scheduling_renderer_metadata($renderer);

    if (intval($renderer['max_num_jobs']) - intval($renderer['num_jobs']) <= 0) {
        return false;
    }

    return true;
}

/**
 * Return whether the first renderer is a better choice for the given job than the second renderer
 *
 * @param array $first The first renderer
 * @param array $second The second renderer
 * @param array $job The concerned job
 * @return boolean Whether the first renderer is a better choice for the given job than the second renderer
 */
function lib_scheduling_renderer_is_better_than($first, $second, $job)
{
    $first = lib_scheduling_renderer_metadata($first);
    $second = lib_scheduling_renderer_metadata($second);
    // @todo IMPROVE, taking count of the load
    return $first['performance_idx'] < $second['performance_idx'];
}

/**
 * Retrieve rendere metadata
 *
 * <statistics>
 *  <performance_idx></performance_idx>
 *  <max_num_thread></max_num_thread>
 *  <max_num_jobs></max_num_jobs>
 *  <num_jobs></num_jobs>
 *  <load></load>
 *  <jobs>
 *    <job>
 *      <pid></pid>
 *      <time></time>
 *      <state></state>
 *    </job>
 *  </jobs>
 * </statistics>
 *
 * @param array $renderer The renderer
 * @return array The renderer with all its meta-data up to date
 */
function lib_scheduling_renderer_metadata($renderer)
{
    $out = lib_scheduling_renderer_ssh($renderer, $renderer['php'] . ' ' . $renderer['statistics']);

    $xml = new SimpleXMLElement($out);
    foreach ($xml as $tag => $value) {
        if ($tag == 'jobs') {
            $jobs = array();
            foreach ($value->children() as $job) {
                $j = array();
                foreach ($job as $key => $value) {
                    $job[$key] = (string) $value;
                }
                $jobs[] = $j;
            }
            $renderer['jobs'] = $jobs;
        } else {
            $renderer[$tag] = (string) $value;
        }
    }


    return $renderer;
}

/**
 * Retrieve job information
 *
 * @param array $renderer The renderer
 * @param string $job The job
 * @return array The job information
 */
function lib_scheduling_renderer_job_info($renderer, &$job)
{
    //@todo IMPLEMENT
    return true;
}

/**
 * Kill a job on the corresponding renderer
 *
 * @param array $renderer The renderer
 * @param string $job The job
 * @return boolean Whether the job has been successfully killed
 */
function lib_scheduling_renderer_job_kill($renderer, $job)
{
    lib_scheduling_renderer_ssh($renderer, $renderer['php'] . ' ' . $renderer['kill'] . ' ' . $job['asset']);
    return lib_scheduling_file_move(
        lib_scheduling_config('processing-path') . '/' . $job['basename'],
            lib_scheduling_config('failed-path') . '/' . $job['basename']
    );
}

/**
 * SSH with the renderer
 *
 * @param array $renderer The renderer
 * @param string $cmd The command
 * @return string The output
 */
function lib_scheduling_renderer_ssh($renderer, $cmd)
{
    $ssh_pgm=lib_scheduling_config('ssh-path');

    $ssh_cmd = $ssh_pgm . ' -oBatchMode=yes ' . $renderer['client'] . '@' . $renderer['host'] . ' "' . $cmd . '"';
    exec($ssh_cmd, $output, $ret);
    if ($ret) {
        lib_scheduling_alert($ssh_cmd);
        lib_scheduling_alert('Scheduler::renderer_ssh[fail]{' . $cmd . '} |::>' . implode("\n", $output) . '<::|');
    }
    return implode("\n", $output);
}

/**
 * Assign the video to the renderer and launch the job
 *
 * @param array $renderer The renderer
 * @param array $job The job to send
 */
function lib_scheduling_renderer_assign($renderer, $job)
{
    $job['sent'] = date('Y-m-d H:i:s');
    $job['renderer'] = $renderer['host'];
    $queue_path = lib_scheduling_config('queue-path');
    $processing_path = lib_scheduling_config('processing-path');
    
    if (!lib_scheduling_job_write($job, $queue_path)) {
        return false;
    }
    
    if (!lib_scheduling_file_move($queue_path . '/' . $job['basename'], $processing_path . '/' . $job['basename'])) {
        return false;
    }

    system('echo "' . lib_scheduling_config('php-path') . ' ' . dirname(__FILE__) . '/cli_scheduler_job_perform.php ' . $job['uid'] . '" | at now');

    return true;
}

/*******************************/
/****** S E M A P H O R E ******/
/*******************************/

/**
 * Fall back functions in case the system V library are not installed
 */

function my_sem_get($key)
{
    return fopen(lib_scheduling_config('var-path') . $key . '.sem', 'w+');
}
function my_sem_acquire($sem_id)
{
    return flock($sem_id, LOCK_EX);
}
function my_sem_acquire_or_continue($sem_id)
{
    return flock($sem_id, LOCK_EX | LOCK_NB);
}
function my_sem_release($sem_id)
{
    return flock($sem_id, LOCK_UN);
}

/**
 * Take the semaphore
 */
function lib_scheduling_sema_take()
{
    global $semaphore;
    global $time;

    // try to get the semaphore
    if (my_sem_acquire_or_continue($semaphore)) {
        return;
    }

    // Remove semaphore if it is 30sec or more old to avoid dead locks
    $now = time();
    if ($time && $now - $time > 30) {
        lib_scheduling_warning('Scheduler::sema[deadlock]{remove semaphore}');
        lib_scheduling_file_rm(lib_scheduling_config('var-path') . lib_scheduling_config('sem-key') . '.sem');
    }

    // Wait for the semaphore
    my_sem_acquire($semaphore);
}

/**
 * Release the semaphore
 */
function lib_scheduling_sema_release()
{
    global $semaphore;
    my_sem_release($semaphore);
}

/*******************************/
/********** F I L E ************/
/*******************************/

function lib_scheduling_file_move($from, $to)
{
    return rename($from, $to);
}

function lib_scheduling_file_ls($dir)
{
    $handler = opendir($dir);
    $files = array();
    if ($handler === false) {
        echo "nodir given" . PHP_EOL;
        lib_scheduling_error('Scheduler::file_ls - Could not open dir "$dir"');
        return $files;
    }

    while (($file = readdir($handler)) !== false) {
        if ($file != '.' && $file != '..' && $file[0] != '.') {
            $files[] = $dir . '/' . $file;
        }
    }

    closedir($handler);

    return $files;
}

function lib_scheduling_file_rm($file)
{
    return unlink($file);
}

function lib_scheduling_file_rmdir($dir)
{
    foreach (lib_scheduling_file_ls($dir) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($file)) {
            lib_scheduling_file_rmdir($dir . '/' . $file);
        } else {
            unlink($dir . '/' . $file);
        }
    }

    return rmdir($dir);
}

function lib_scheduling_file_safe($filename)
{
    return preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
}

/*******************************/
/** C O N F I G U R A T I O N **/
/*******************************/

/**
 * Retrieve configuration value
 * @param string $name The config name
 * @return string|boolean The config value or false
 */
function lib_scheduling_config($name)
{
    global $config;
    global $ssh_pgm;
    global $php_cli_cmd;
    
    switch ($name) {
        case 'scheduler-path':
            return $config['paths']['scheduler'];
        case 'queue-path':
            return $config['paths']['queue'];
        case 'processing-path':
            return $config['paths']['processing'];
        case 'processed-path':
            return $config['paths']['processed'];
        case 'failed-path':
            return $config['paths']['failed'];
        case 'frozen-path':
            return $config['paths']['frozen'];
        case 'logs-path':
            return $config['paths']['logs'];
        case 'var-path':
            return $config['paths']['var'];
        case 'php-path':
            return $php_cli_cmd;
        case 'ssh-path':
            return $ssh_pgm;
        case 'sem-key':
            return $config['keys']['sem'];
        case 'default-priority':
            return $config['scheduler']['priority'];
    }

    return false;
}

/*******************************/
/********** L O G S ************/
/*******************************/

function lib_scheduling_notice($msg)
{
    if (DEBUG > 4) {
        lib_scheduling_log(' NOTICE', $msg);
    }
}
function lib_scheduling_warning($msg)
{
    if (DEBUG > 3) {
        lib_scheduling_log('WARNING', $msg);
    }
}
function lib_scheduling_alert($msg)
{
    if (DEBUG > 2) {
        lib_scheduling_log('  ALERT', $msg);
    }
}
function lib_scheduling_trace($msg)
{
    if (DEBUG > 1) {
        lib_scheduling_log('  TRACE', $msg);
    }
}
function lib_scheduling_error($msg)
{
    if (DEBUG > 0) {
        lib_scheduling_log('  ERROR', $msg);
    }
}
function lib_scheduling_log($cat, $msg)
{
    file_put_contents(lib_scheduling_config('logs-path'), '' . date('Y-m-d H:i:s') . ' - ' . $cat . ' - ' . $msg . "\n", FILE_APPEND);
    //also print it to console in case scheduler was started manually
    echo $msg . PHP_EOL;
}

/*******************************/
/********** M A I N ************/
/*******************************/
$time = filemtime(lib_scheduling_config('var-path') . lib_scheduling_config('sem-key') . '.sem');
$semaphore = my_sem_get(lib_scheduling_config('sem-key'));
