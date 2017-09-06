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
 * @package ezcast.ezmanager.cli
 */
include_once 'config.inc';

$album = $argv[1];
$asset = $argv[2];


if (!isset($album) || $album == '' || !isset($asset) || $asset == '') {
    die;
}
 
system("rm -rf " . $apache_documentroot . '/ezplayer/videos/' . $album . '/' . $asset . '/');

if (is_dir_empty($apache_documentroot . '/ezplayer/videos/' . $album)) {
    system("rm -rf " . $apache_documentroot . '/ezplayer/videos/' . $album);
}

function is_dir_empty($dir)
{
    if (!is_readable($dir)) {
        return null;
    }
    $handle = opendir($dir);
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            return false;
        }
    }
    return true;
}
