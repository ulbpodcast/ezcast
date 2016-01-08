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
<script>
    var ind = 0;
    var dataArrayPieChart = new Array();
    var result = null;
</script>
<?php
########## CHARTS DATA LOADING #################################################
setlocale(LC_ALL, 'fr_BE');
$DTZ = new DateTimeZone('Europe/Paris');
$allAlbums = stat_album_get_all();

setlocale(LC_ALL, 'fr_BE');
$threadsCount = stat_threads_count_all();
$commentsCount = stat_get_nb_comments();

$minCreationDate = get_oldest_date();
$minDateFr = new DateTimeFrench($minCreationDate, $DTZ);
$maxCreationDate = get_newest_date();
$today = date('Y-m-d H:i:s');
$todayMY = date('m/Y');

foreach ($allAlbums as $albumArr) {
    $nbThreads = stat_threads_count_by_album($albumArr["albumName"]);
    ?>
    <script>
        dataArrayPieChart[ind] = [<?php echo json_encode($albumArr["albumName"]); ?>, <?php echo $nbThreads; ?>];
        ind += 1;
    </script>
    <?php
}
##### ##########################################################################
?>
</script>
<section>
    <div class="container">
        <div class="page-header">
            <h2>®stats_main_title®</h2>
            <h3>®stats_sub_title®</h3>
        </div>
        <h4>®stats_main_title®</h4>
        <div class="container">
            <h5>®stats_since® <?php echo "[$minCreationDate]"//echo $minDateFr->format('l j F Y');  ?></h5>
            <p><b><?php echo $threadsCount; ?></b> ®stats_discussions_created®</p>
            <p><b><?php echo $commentsCount; ?></b> ®stats_comments_created®</p>
        </div>
        <br/>
        <h4>®discussions_comments_by_album®</h4>
        <span class="glyphicon glyphicon-warning-sign">®stats_at_least_one®</span> 
        <div id="pieChartGeneral" class="pie"></div>
        <br />
        <div id="tableGeneral" class="table-responsive">
            <?php include_once template_getpath('div_stats_threads_table_generale.php'); ?>
        </div>
    </div>
</section>

<!-- M O N T H   S E A R C H -->
<section style="background-color: whitesmoke;" >
    <div class="container">
        <div class="page-header">
            <h2>®stats_month®</h2>
        </div>

        <div id="month-search">
            <input type="text" id="dpMonths" class="input-lg" data-date-format="mm/yyyy" 
                   value="<?php echo $todayMY; ?>" placeholder="Click me!" 
                   data-date-viewmode="years" data-date-minviewmode="months" readonly></input> 
            <a id="submit-month-search" class="btn btn-success btn-search"
               onclick="javascript:getStatsByMonth()"> <i class="icon-search icon-white"></i>®search®</a>               
        </div>
        <br/>
        <div id="month-stats">                
            <!-- Results go here -->
        </div>
        <label id="month-search-error" class="label label-important" style="display: none; font-size: 1em;">®stats_month_error®</label>
        <br/>
    </div>
</section>
<!-- S E A R C H   F O R   x L A S T   D A Y S -->
<section>
    <div class="container">
        <div class="page-header">
            <h2>®stats_n_days®</h2>
        </div>

        <div id="nDays-search">
            <input type="text" id="nDays" class="input-lg"></input> 
            <a id="submit-nDays-search" class="btn btn-success btn-search"
               onclick="javascript:getStatsByNDays()"> <i class="icon-search icon-white"></i>®search®</a>               
        </div>
        <br/>
        <div id="nDays-stats">                
            <!-- Results go here -->
        </div>
        <label id="nDays-search-error" class="label label-important">®stats_nan_error®</label>
        <br/>
    </div>
</section>
<section style="background-color: whitesmoke;">
    <div class="container">
        <div class="page-header">
            <h2>®stats_other®</h2>
        </div>

        <div>
            <form action="index.php?action=get_csv_assets" method="post" id="csv_assets_form" name="csv_assets_form" onsubmit="return false">
                <a id="submit-csvAssets-search" class="btn btn-info btn-search" 
                   onclick="document.csv_assets_form.submit();
            return false;"> <span class="icon-file icon-white"></span>®stats_discussions_by_asset®</a>
            </form>               
        </div>
        <br/>
    </div>
</section>
<script>

        var pieChartGeneral = jQuery.jqplot('pieChartGeneral', [dataArrayPieChart],
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
                });
</script>