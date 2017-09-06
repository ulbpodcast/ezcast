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

$jobs = get_job_infos();

echo '<?xml version="1.0" standalone="yes"?>'.PHP_EOL;
echo '<statistics>'.PHP_EOL;
echo '  <performance_idx>' . $performance_idx . '</performance_idx>' . PHP_EOL;
echo '  <max_num_threads>' . get_max_num_threads() . '</max_num_threads>' . PHP_EOL;
echo '  <max_num_jobs>' . get_max_num_jobs() . '</max_num_jobs>' . PHP_EOL;
echo '  <num_jobs>' . get_num_jobs() . '</num_jobs>' . PHP_EOL;
echo '  <load>'.get_load() . '</load>' . PHP_EOL;
echo '  <encoding_pgm>'.$encoding_pgm['name'] . '</encoding_pgm>' . PHP_EOL;
echo '  <encoding_desc>'.$encoding_pgm['description'] . '</encoding_desc>' . PHP_EOL;
echo '  <jobs>'.PHP_EOL;
foreach ($jobs as $job) {
    echo '      <job>'.PHP_EOL;
    echo '          <pid>'.$job['pid'].'</pid>' . PHP_EOL;
    echo '          <time>'.$job['time'].'</time>' . PHP_EOL;
    echo '          <state>'.$job['state'].'</state>' . PHP_EOL;
    echo '      </job>'.PHP_EOL;
}
echo '  </jobs>'.PHP_EOL;
echo '</statistics>'.PHP_EOL;
