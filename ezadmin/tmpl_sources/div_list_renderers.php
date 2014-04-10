
<!--
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
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
-->

<?php
require_once 'config.inc';
?>

<table class="table table-striped table-hover table-condensed classrooms">
    <tr>
        <th>®renderer_performance_idx®</th>
        <th>®renderer_name®</th>
        <th>®renderer_hostname®</th>
        <th>®renderer_jobs®</th>
        <th>®renderer_load®</th>
        <th>®renderer_status®</th>
        <th>®renderer_threads®</th>
    </tr>
    
    <?php include 'lib_scheduling.php';
    foreach($renderers as $r) {
        $r = lib_scheduling_renderer_metadata($r);
        //var_dump($r2);
        ?>
        <tr class="<?php echo $class; ?>">
            <td><?php echo $r['performance_idx']; ?></td>
            <td><?php echo $r['name']; ?></td>
            <td><?php echo $r['host']; ?></td>
            <td><?php echo $r['num_jobs'].'/'.$r['max_num_jobs']; ?></td>
            <td><?php echo $r['load']; ?></td>
            <td><?php echo $r['status']; ?></td>
            <td><?php echo $r['max_num_threads']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>

