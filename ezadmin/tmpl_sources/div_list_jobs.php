
<?php
/*
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
*/
?>

<?php
require_once 'config.inc';
?>

<table class="table table-striped table-hover table-condensed classrooms">
    <tr>
        <th>®job_priority®</th>
        <th>®job_submit_date®</th>
        <th>®job_author®</th>
        <?php global $classrooms_category_enabled;
        if($classrooms_category_enabled) { ?>
            <th>®job_origin®</th>
        <?php } ?>
        <th>®job_renderer®</th>
        <th>®job_details®</th>
        <th>®job_duration®</th>
        <th>®job_actions®</th>
    </tr>
    
    <?php include_once 'lib_scheduling.php';
    
    foreach($jobs as $job) {
        if(empty($job['done'])) {
            $date = '';
            $threshold1 = 0; // If duration above threshold: display in yellow as a warning
            $threshold2 = 0; // Same as above, except if we go above this threshold it is time to seriously worry
            if(!empty($job['done']))
                $date = '';
            else if(!empty($job['sent'])) {
                $date = $job['sent'];
                $threshold1 = 18000; // 18000s = 5 hours
                $threshold2 = 43200; // 43200s = 12 hours
            }
            else if(!empty($job['created'])) {
                $date = $job['created'];
                $threshold1 =  3600; //  3600s = 1 hour
                $threshold2 = 18000; // 18000s = 5 hours
            }
            
            if(!empty($date)) {
                date_default_timezone_set('Europe/Brussels');
                $dt = new DateTime();

                $dt = $dt->createFromFormat('Y-m-d H:i:s', $date);
                $datetime = $dt->getTimestamp();
                $duration = abs(time() - $datetime);
                
                $class = 'success';
                if($duration > $threshold2)
                    $class = 'error';
                else if ($duration > $threshold1)
                    $class = 'info';
            }
            else {
                $class = '';
            }
            ?>
            <tr class="<?php echo $class; ?>">
                <td><?php echo $job['priority']; ?></td>
                <td><?php echo $job['created']; ?></td>
                <td><?php echo $job['sender']; ?></td>
                <?php global $classrooms_category_enabled;
                if($classrooms_category_enabled) { ?>
                    <td><?php echo $job['origin']; ?></td>
                <?php } ?>
                <td><?php echo $job['renderer']; ?></td>
                <td>
                    <?php if(in_array($job, scheduler_frozen_get())) {
                        echo '<i class="icon-minus-sign" title="®frozen®"></i> ®frozen®';
                    }
                    else if(!empty($job['done'])) {
                        echo '<i class="icon-ok-sign" title="®finished_on® '.$job['done'].'"></i> ®finished®';
                    } else if(!empty($job['sent'])) {
                        echo '<i class="icon-refresh" title="®sent_on® '.$job['created'].'"></i> ®processing®';
                    } else if(!empty($job['created'])) {
                        echo '<i class="icon-time" title="®created_on® '.$job['created'].'"></i> ®waiting®';
                    } ?>
                </td>
                <td>
                    <?php
                    $hours = floor($duration / 3600);
                    $minutes = floor(($duration % 3600) / 60);
                    $seconds = ($duration % 3600) % 60;

                    if($hours > 0)
                        echo $hours .'h ';
                    if($minutes > 0)
                        echo $minutes.'m ';
                    if($seconds > 0)
                        echo $seconds.'s';
                    ?>
                </td>
                <td>
                    <?php if(empty($job['done']) && empty($job['sent'])) { ?>
                        <?php if(!in_array($job, scheduler_frozen_get())) { ?>
                            <a href="index.php?action=job_priority_up&amp;job=<?php echo $job['uid']; ?>"><i class="icon-chevron-up" title="®increase_priority®"></i></a>&nbsp;
                            <a href="index.php?action=job_priority_down&amp;job=<?php echo $job['uid']; ?>"><i class="icon-chevron-down" title="®decrease_priority®"></i></a>&nbsp;
                        <?php } ?>
                        <?php if(in_array($job, scheduler_frozen_get())) { ?>
                            <a href="index.php?action=free_unfreeze_job&amp;job=<?php echo $job['uid']; ?>"><i class="icon-repeat" title="®unfreeze_job®"></i></a>
                        <?php } else { ?>
                            <a href="index.php?action=free_unfreeze_job&amp;job=<?php echo $job['uid']; ?>"><i class="icon-ban-circle" title="®freeze_job®"></i></a>
                        <?php } ?>
                    <?php } else if(empty($job['done']) && !empty($job['sent'])) {
                        ?>
                        <a href="index.php?action=job_kill&amp;job=<?php echo $job['uid']; ?>"><i class="icon-remove" title="®kill_job®"></i></a>
                        <?php
                    } ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>

