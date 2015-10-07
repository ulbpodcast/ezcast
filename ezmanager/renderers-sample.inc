<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
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

// Renderers.inc
// Configuration file

return array(
 array(
    'name' => 'name of the renderer',
    'host' => 'localhost',
    'client' => 'user name',
    'status' => 'enabled',
    'home' => '/path/to//bin/',
    'downloading_dir' => '/path/to/downloading', // typically ~/ezrenderer/queues/downloading
    'downloaded_dir' => '/path/to/downloaded', // typically ~/ezrenderer/queues/downloaded
    'processed_dir' => '/path/to/processed', // typically ~/ezrenderer/queues/processed
    'statistics' => '/path/to/bin/cli_statistics_get.php', // typically ~/ezrenderer/bin/cli_statistics_get.php
    'php' => '/usr/bin/php',
    'launch' => '/path/to/bin/intro_title_movie.bash', // typically ~/ezrenderer/bin/intro_title_movie.bash
    'kill' => '/path/to/bin/cli_job_kill.php', // typically ~/ezrenderer/bin/cli_job_kill.php
  ), 
);

?>
