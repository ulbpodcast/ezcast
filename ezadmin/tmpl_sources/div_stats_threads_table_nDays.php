
<table class="table table-striped">
    <thead>
        <tr>
            <th class="left">®stats_album_name®</th>
            <th>®stats_discussions_count®</th>
            <th>®stats_comments_count®</th>
            <th>®stats_comments_by_discussion®</th>
        </tr>
    </thead>
    <tfoot>
        <tr class="warning">
            <td></td>
            <td>®stats_total® : <?php echo $threadsCountNDays; ?></td>
            <td>®stats_total® : <?php echo $commentsCountNDays; ?></td>
            <td>®stats_average® : <?php echo $commentsCountNDays/$threadsCountNDays; ?></td>
            <!--<td></td>-->
        </tr>
    </tfoot>
    <tbody>
        <?php 
        foreach ($allAlbumsNDays as $albumNDay) {
            $nbThreads = stat_threads_count_by_album_and_date_interval($albumNDay["albumName"], $_SESSION['nDaysStats']['nDaysEarlier'], $_SESSION['nDaysStats']['nDaysLater']);
            $nbComments = stat_comments_count_by_album_and_date_interval($albumNDay["albumName"], $_SESSION['nDaysStats']['nDaysEarlier'], $_SESSION['nDaysStats']['nDaysLater']); ?>
        <tr>
            <td class="left"><?php echo $albumNDay["albumName"]; ?></td>
            <td><?php echo $nbThreads; ?></td>
            <td><?php echo $nbComments; ?></td>
            <td><?php echo $nbComments/$nbThreads; ?></td>
            <!--<td>n/a</td>-->
        </tr>
        <?php
        }
        ?>
    </tbody>
    
</table>

