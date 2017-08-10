<?php

if($argc != 2) {
    echo "Usage: cli_install_htdocs <webspace_root>";
    exit(1);
}

$apache_documentroot = $argv[1];

# places web files in the webspace
system("cp -rp ".__DIR__."/ezadmin/htdocs $apache_documentroot/ezadmin");
system("cp -rp ".__DIR__."/ezmanager/htdocs $apache_documentroot/ezmanager");
system("cp -rp ".__DIR__."/ezplayer/htdocs $apache_documentroot/ezplayer");

system("cp -rp ".__DIR__."/commons/htdocs $apache_documentroot/ezadmin/commons");
system("cp -rp ".__DIR__."/commons/htdocs $apache_documentroot/ezmanager/commons");
system("cp -rp ".__DIR__."/commons/htdocs $apache_documentroot/ezplayer/commons");

$web_file = file_get_contents($apache_documentroot . "/ezadmin/install.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezadmin/install.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezadmin/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezadmin/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/distribute.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/distribute.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/recorder/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/recorder/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/recorder/logs.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/recorder/logs.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezplayer/index.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezplayer/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezplayer/infos.php");
$web_file = str_replace("!PATH", __DIR__, $web_file);
file_put_contents($apache_documentroot . "/ezplayer/infos.php", $web_file);
