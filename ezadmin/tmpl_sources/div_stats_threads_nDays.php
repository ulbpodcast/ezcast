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
if (isset($_SESSION['nDaysStats'])) {
    ?>
    <script>
        var ind3 = 0;
        var dataArrayPieChartNDays= new Array();
    </script>
    <?php
    $threadsCountNDays = $_SESSION['nDaysStats']['threadsCount'];
    $commentsCountNDays = $_SESSION['nDaysStats']['commentsCount'];
    $allAlbums = stat_album_get_all();
    $allAlbumsNDays = array();
    foreach ($allAlbums as $albumArr) {
        $nbThreadsNDays = stat_threads_count_by_album_and_date_interval($albumArr["albumName"], $_SESSION['nDaysStats']['nDaysEarlier'], $_SESSION['nDaysStats']['nDaysLater']);
        if ($nbThreadsNDays != "0") {
            $allAlbumsNDays[] = $albumArr;
            ?>
            <script>
                dataArrayPieChartNDays[ind3] = [<?php echo json_encode($albumArr["albumName"]); ?>, <?php echo $nbThreadsNDays; ?>];
                ind3 += 1;
            </script>
            <?php
        }
    }
    ?>
    <script>
        var pieChartNDays = jQuery.jqplot('pieChartNDays', [dataArrayPieChartNDays],
                {
                    title: '®stats_discussions_by_album® [<?php echo substr($_SESSION['nDaysStats']['nDaysEarlier'], 0, 10); ?> - <?php echo substr($_SESSION['nDaysStats']['nDaysLater'], 0, 10); ?>]',
                    seriesDefaults: {
                        // Make this a pie chart.
                        renderer: jQuery.jqplot.PieRenderer,
                        rendererOptions: {
                            showDataLabels: true
                        }
                    },
                    legend: {
                        show: true,
                        location: 'e'
                    }
                }
        );
    </script>

    <div id="pieChartNDays" class="pie">
        <!-- Chart container -->
    </div>
    <h4><span class="label label-default">Totaux</span></h4>
    <p>®stats_discussions_count®    <span class="label label-default"><?php echo $threadsCountNDays; ?></span></p>
    <p>®stats_comments_count®   <span class="label label-default"><?php echo $commentsCountNDays; ?></span></p>
    <div id='tableNDays' class="table-responsive">
        <?php include_once 'div_stats_threads_table_nDays.php'; ?>
    </div>
<?php
} else {
    echo '<label class="label label-info">NO DATA</label>';
}
?>

