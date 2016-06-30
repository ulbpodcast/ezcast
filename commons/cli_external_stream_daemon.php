<?php

require_once __DIR__."/lib_external_stream_daemon.php";

if($argc != 2)
{
    echo "Wrong argument count.".PHP_EOL;
    echo "Usage: php ".__FILE__." <upload_root_dir>".PHP_EOL;
    return;
}

$upload_root_dir = $argv[1];
$streamDaemon = new ExternalStreamDaemon($upload_root_dir);

//this does loop until stream daemon stops
$streamDaemon->run();
