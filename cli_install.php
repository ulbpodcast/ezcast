<?php
/*
 * EZCAST 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Antoine Dewilde
 *            Carlos Avimadjessi
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

/*
 * This file is part of the installation process
 */

if ($argc != 11) {
    echo "usage: " . $argv[0] . " <php_path> <rsync_path> <apache_documentroot> <ezcast_basedir> <repository_basedir> <user> <passwd> <fullname> <apache_username>" .
    "\n <php_path> the path to the php binary" .
    "\n <rsync_path> the path to the rsync binary" .
    "\n <apache_documentroot> the path Apache's documentroot" .
    "\n <ezcast_basedir> the root directory for the ezcast application" .
    "\n <repository_basedir> the root directory for the repository".
    "\n <user> the username for the web installer" .
    "\n <passwd> the password for the web installer".
    "\n <firstname> first name of the first user".
    "\n <lastname> last name of the first user" ;
    die;
}

$php_cli_cmd = $argv[1];
$rsync_pgm = $argv[2];
$apache_documentroot = $argv[3];
$basedir = $argv[4];
$repository_basedir = $argv[5];
$username = $argv[6];
$password = $argv[7];
$firstname = $argv[8];
$lastname = $argv[9];
$apache_username = $argv[10];

/*
 * First of all, we adapt the paths in webspace according to the actual
 * position of EZcast products
 */

$web_file = file_get_contents($apache_documentroot . "/index.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezadmin/install.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezadmin/install.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezadmin/index.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezadmin/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/index.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/distribute.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/distribute.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezmanager/recorder/index.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezmanager/recorder/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezplayer/index.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezplayer/index.php", $web_file);

$web_file = file_get_contents($apache_documentroot . "/ezplayer/infos.php");
$web_file = str_replace("!PATH", $basedir, $web_file);
file_put_contents($apache_documentroot . "/ezplayer/infos.php", $web_file);

/*
 * Then, we add user's configuration in commons/config.inc
 */

$config = file_get_contents($basedir . "/commons/config-sample.inc");

$config = preg_replace('/\$repository_basedir = (.+);/', '\$repository_basedir = "' . $repository_basedir . '";', $config);
$config = preg_replace('/\$basedir = (.+);/', '\$basedir = "' . $basedir . '";', $config);
$config = preg_replace('/\$php_cli_cmd = (.+);/', '\$php_cli_cmd = "' . $php_cli_cmd . '";', $config);
$config = preg_replace('/\$rsync_pgm = (.+);/', '\$rsync_pgm = "' . $rsync_pgm . '";', $config);
$config = preg_replace('/\$apache_username = (.+);/', '\$apache_username = "' . $apache_username . '";', $config);
$config = preg_replace('/\$apache_documentroot = (.+);/', '\$apache_documentroot = "' . $apache_documentroot . '";', $config);
file_put_contents($basedir . "/commons/config.inc", $config);

/*
 * Finally, create the first user to access the web installer
 */

$des_seed = chr(rand (33,126)) . chr(rand (33,126));
$password = crypt($password, $des_seed);

file_put_contents("$basedir/first_user", $username);
file_put_contents("first_user", " , $password", FILE_APPEND);
file_put_contents("first_user", " , $firstname", FILE_APPEND);
file_put_contents("first_user", " , $lastname", FILE_APPEND);
?>
