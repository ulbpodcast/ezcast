<?php
/*
 * EZCAST EZrenderer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Antoine Dewilde
 *            Thibaut Roskam
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

require_once 'lib_statistics.php';
require_once 'config.inc';

if (!isset($argv[1]) || empty($argv[1])) {
    echo 'Usage: cli_job_kill <pid>'.PHP_EOL;
    echo '  or: cli_job_kill <dirname>'.PHP_EOL;
    die;
}

$pids = array();
if (is_numeric($argv[1])) {
    $pids[] = $argv[1];
} else {
    $pids = get_job_pid($argv[1]);
}

foreach ($pids as $pid) {
    exec('kill -9 '.$pid, $output, $val);
    if ($val == 0) {
        echo 'could not kill '.$pid.PHP_EOL;
    } else {
        echo 'killed '.$pid.PHP_EOL;
    }
}
