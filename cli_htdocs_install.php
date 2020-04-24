<?php

$in_install = true;
$config_file = __DIR__.'/commons/config.inc';
if(file_exists($config_file)) {
    require_once(__DIR__.'/commons/config.inc');
    $web_documentroot = $apache_documentroot;
} else {
    if($argc < 2)
        die("Usage: cli_htdocs_install.php <web_root>");
     $web_documentroot = $argv[1];
}

system("mkdir -p $web_documentroot/ezadmin");
system("mkdir -p $web_documentroot/ezmanager");
system("mkdir -p $web_documentroot/ezplayer");

# places web files in the webspace
system("cp -rp ".__DIR__."/ezadmin/htdocs/* $web_documentroot/ezadmin");
system("cp -rp ".__DIR__."/ezmanager/htdocs/* $web_documentroot/ezmanager");
system("cp -rp ".__DIR__."/ezplayer/htdocs/* $web_documentroot/ezplayer");

system("mkdir -p $web_documentroot/ezadmin/commons && mkdir -p $web_documentroot/ezmanager/commons && mkdir -p $web_documentroot/ezplayer/commons ");

system("cp -rp ".__DIR__."/commons/htdocs/* $web_documentroot/ezadmin/commons");
system("cp -rp ".__DIR__."/commons/htdocs/* $web_documentroot/ezmanager/commons");
system("cp -rp ".__DIR__."/commons/htdocs/* $web_documentroot/ezplayer/commons");

$web_file = file_get_contents($web_documentroot . "/ezadmin/install.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezadmin/install.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezadmin/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezadmin/index.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezmanager/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezmanager/index.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezmanager/distribute.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezmanager/distribute.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezmanager/inscription.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezmanager/inscription.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezmanager/recorder/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezmanager/recorder/index.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezmanager/recorder/logs.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezmanager/recorder/logs.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezplayer/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezplayer/index.php", $web_file);

$web_file = file_get_contents($web_documentroot . "/ezplayer/infos.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($web_documentroot . "/ezplayer/infos.php", $web_file);

echo "Copied htdocs files to $web_documentroot" . PHP_EOL;
