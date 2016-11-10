<?php
file_put_contents("/var/lib/ezcast/external_stream/FOMXFAVE/ba", "1");

require_once __DIR__."/lib_external_stream_daemon.php";

if($argc != 3)
{
    echo "Wrong argument count.".PHP_EOL;
    echo "Usage: php ".__FILE__." <video_root_dir> <asset_token>".PHP_EOL;
    return;
}

$video_root_dir = $argv[1];
$asset_token = $argv[2];
$streamDaemon = new ExternalStreamDaemon($video_root_dir, $asset_token);

//this does loop until stream daemon stops
$streamDaemon->run();
