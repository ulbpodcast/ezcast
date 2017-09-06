
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
            $allAlbumsNDays[] = $albumArr; ?>
            <script>
                dataArrayPieChartNDays[ind3] = [<?php echo json_encode($albumArr["albumName"]); ?>, <?php echo $nbThreadsNDays; ?>];
                ind3 += 1;
            </script>
            <?php
        }
    } ?>
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
                    },
                    highlighter: {
                        show: true,
                        useAxesFormatters: false,
                        tooltipFormatString: '%s',
                        sizeAdjust: 1.5
                    }
                }
        );
    </script>
    
    <center>
        <div id="pieChartNDays" class="pie">
            <!-- Chart container -->
        </div>
    </center>
    <h4>®stats_total_counts®</h4>
    <p class="default">®stats_discussions_count®    <span class="label label-default"><?php echo $threadsCountNDays; ?></span></p>
    <p class="default">®stats_comments_count®   <span class="label label-default"><?php echo $commentsCountNDays; ?></span></p>
    
    <div id='tableNDays' class="table-responsive">
        <?php include_once 'div_stats_threads_table_nDays.php'; ?>
    </div>
<?php
} else {
        echo '<label class="label label-info">NO DATA</label>';
    }
?>

