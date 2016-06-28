<?php

include_once __DIR__."lib_external_stream_daemon.php";

if($argc != 2)
{
    echo "Wrong argument count. Usage: php ".__FILE__." <upload_root_dir>";
}

$upload_root_dir = $argv[1];
$streamDaemon = new ExternalStreamDaemon($upload_root_dir);

$pid = getmypid();
file_put_contents($pid_file, getmypid());

//this does loop until stream daemon stops
$streamDaemon->run();
