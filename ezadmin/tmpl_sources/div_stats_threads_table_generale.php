
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

<table class="table table-striped">
    <thead>
        <tr>
            <th class="left">®stats_album_name®</th>
            <th>®stats_discussions_count®</th>
            <th>®stats_discussions_count® (%)</th>
            <th>®stats_comments_count®</th>
            <th>®stats_comments_by_discussion®n</th>
            <!--<th>Nombre de commentaires par jour</th>-->
        </tr>
    </thead>
    <tfoot>
        <tr class="info">
            <td></td>
            <td>®stats_total® : <?php echo $threadsCount; ?></td>
            <td></td>
            <td>®stats_total® : <?php echo $commentsCount; ?></td>
            <td>®stats_average® : <?php echo number_format((float)($commentsCount/$threadsCount), 2, '.','');?></td>
            <!--<td></td>-->
        </tr>
    </tfoot>
    <tbody>
        <?php 
        foreach ($allAlbums as $albumArr) {
            $nbThreads = stat_threads_count_by_album($albumArr["albumName"]);
            $nbComments = stat_comments_count_by_album($albumArr["albumName"]);
        ?>
        <tr>
            <td class="left"><?php echo $albumArr["albumName"]; ?></td>
            <td><?php echo $nbThreads; ?></td>
            <td><?php echo number_format((float)(($nbThreads/$threadsCount)*100), 2, '.',''); ?></td>
            <td><?php echo $nbComments; ?></td>
            <td><?php echo number_format((float)($nbComments/$nbThreads), 2, '.',''); ?></td>
            <!--<td>n/a</td>-->
        </tr>
        <?php 
        }
        ?>
    </tbody>
    
</table>

