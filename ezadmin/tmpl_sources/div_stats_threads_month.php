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
if (isset($_SESSION['monthStats'])) {
    ?>
    <script>
        var ind2 = 0;
        var dataArrayPieChartMonth = new Array();
    </script>
    <?php
    $threadsCountMonth = $_SESSION['monthStats']['threadsCount'];
    $commentsCountMonth = $_SESSION['monthStats']['commentsCount'];
    $allAlbums = stat_album_get_all();
    $allAlbumsMonth = array();
    foreach ($allAlbums as $albumArr) {
        $nbThreadsTmp = stat_threads_count_by_album_and_month($albumArr["albumName"], $_SESSION['currentMonth']);
        if ($nbThreadsTmp != "0") {
            $allAlbumsMonth[] = $albumArr;
            ?>
            <script>
                dataArrayPieChartMonth[ind2] = [<?php echo json_encode($albumArr["albumName"]); ?>, <?php echo $nbThreadsTmp; ?>];
                ind2 += 1;
            </script>
            <?php
        }
    }
    ?>
    <script>
        var pieChartMonth = jQuery.jqplot('pieChartMonth', [dataArrayPieChartMonth],
                {
                    title: '®stats_discussions_by_album®',
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

    <div id="pieChartMonth" class="pie">
        <!-- Chart container -->
    </div>
    <h4><span class="label label-default">Totaux</span></h4>
    <p>®stats_discussions_count®   <span class="label label-default"><?php echo $threadsCountMonth; ?></span></p>
    <p>®stats_comments_count®   <span class="label label-default"><?php echo $commentsCountMonth; ?></span></p>
    <div id='tableMonth' class="table-responsive">
        <?php include_once template_getpath('div_stats_threads_table_month.php'); ?>
    </div>
<?php
} else {
    echo '<label class="label label-info">NO DATA</label>';
}
?>

