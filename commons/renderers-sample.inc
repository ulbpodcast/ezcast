<?php

// Renderers.inc
// Configuration file

return array(
 array(
    'name' => 'name of the renderer',
    'host' => 'localhost',
    'client' => 'user name',
    'status' => 'enabled',
    'home' => '/path/to/bin/',
    'downloading_dir' => '/path/to/downloading', // typically ~/ezrenderer/queues/downloading
    'downloaded_dir' => '/path/to/downloaded', // typically ~/ezrenderer/queues/downloaded
    'processed_dir' => '/path/to/processed', // typically ~/ezrenderer/queues/processed
    'statistics' => '/path/to/bin/cli_statistics_get.php', // typically ~/ezrenderer/bin/cli_statistics_get.php
    'php' => '/usr/bin/php',
    'launch' => '/path/to/bin/intro_title_movie.bash', // typically ~/ezrenderer/bin/intro_title_movie.bash
    'kill' => '/path/to/bin/cli_job_kill.php', // typically ~/ezrenderer/bin/cli_job_kill.php
  ), 
);
