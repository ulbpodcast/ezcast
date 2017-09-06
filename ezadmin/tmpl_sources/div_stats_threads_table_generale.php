
<table class="table table-striped">
    <thead>
        <tr>
            <?php echo $colOrder->insertThSort("albumName", "®stats_album_name®", "left"); ?> 
            <th>®stats_discussions_count®</th>
            <th>®stats_discussions_count® (%)</th>
            <th>®stats_comments_count®</th>
            <th>®stats_comments_by_discussion®</th>
            <!--<th>Nombre de commentaires par jour</th>-->
        </tr>
    </thead>
    <tfoot>
        <tr class="info">
            <td></td>
            <td>®stats_total® : <?php echo $threadsCount; ?></td>
            <td></td>
            <td>®stats_total® : <?php echo $commentsCount; ?></td>
            <td>®stats_average® : <?php echo number_format((float)($commentsCount/$threadsCount), 2, '.', '');?></td>
            <!--<td></td>-->
        </tr>
    </tfoot>
    <tbody>
        <?php 
        foreach ($allAlbums as $albumArr) {
            $nbThreads = stat_threads_count_by_album($albumArr["albumName"]);
            $nbComments = stat_comments_count_by_album($albumArr["albumName"]); ?>
        <tr>
            <td class="left"><?php echo $albumArr["albumName"]; ?></td>
            <td><?php echo $nbThreads; ?></td>
            <td><?php echo number_format((float)(($nbThreads/$threadsCount)*100), 2, '.', ''); ?></td>
            <td><?php echo $nbComments; ?></td>
            <td><?php echo number_format((float)($nbComments/$nbThreads), 2, '.', ''); ?></td>
            <!--<td>n/a</td>-->
        </tr>
        <?php
        }
        ?>
    </tbody>
    
</table>

