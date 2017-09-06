
<script>
    var ind = 0;
    var dataArrayPieChart = new Array();
    var result = null;
</script>
<?php


echo '<script>';
foreach ($allAlbums as $albumArr) {
    $nbThreads = stat_threads_count_by_album($albumArr["albumName"]); ?>

        dataArrayPieChart[ind] = [<?php echo json_encode($albumArr["albumName"]); ?>, <?php echo $nbThreads; ?>];
        ind += 1;
    <?php
}
echo '</script>';
##### ##########################################################################
?>
</script>

<div class="page_title">®stats_comment_title®</div>


<h4>®stats_general_title®</h4>
<div class="container">
    <h5>®stats_since® <?php echo "[$minCreationDate]"//echo $minDateFr->format('l j F Y');?></h5>
    <p><b><?php echo $threadsCount; ?></b> ®stats_discussions_created®</p>
    <p><b><?php echo $commentsCount; ?></b> ®stats_comments_created®</p>
</div>
<br/>
<h4>®discussions_comments_by_album®</h4>
<p class="help-block">
    <span class="glyphicon glyphicon-warning-sign">®stats_at_least_one®</span> 
</p>
<center>
    <div id="pieChartGeneral" class="pie"></div>
</center>
<br />
<div id="tableGeneral" class="table-responsive stats">
    <?php include_once template_getpath('div_stats_threads_table_generale.php'); ?>
</div>

<!-- M O N T H   S E A R C H -->
<div class="jumbotron stats">
    <div class="page-header">
        <h4>®stats_month®</h4>
    </div>

    <div id="month-search form-inline">
        <div class="form-group col-md-6">
            <div class='input-group date' id='datetimepickerMonths'>
                <input type='text' class="form-control" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar">
                    </span>
                </span>
            </div>
<!--            <input type="text" id="dpMonths" class="form-control" data-date-format="mm/yyyy" 
                   value="<?php //echo $todayMY;?>" placeholdorm-controler="Click me!" 
                   data-date-viewmode="years" data-date-minviewmode="months" />-->
        </div>
        <a id="submit-month-search" class="btn btn-success btn-search" 
           onclick="javascript:getStatsByMonth()"> <span class="glyphicon glyphicon-search icon-white"></span> ®search®</a>               
    </div>
    <br/>
    <div id="month-stats">                
        <!-- Results go here -->
    </div>
    <label id="month-search-error" class="label label-important" style="display: none; font-size: 1em;">®stats_month_error®</label>
    <br/>
</div>

<!-- S E A R C H   F O R   x L A S T   D A Y S -->
<br />
<h4>®stats_n_days®</h4>

<div id="nDays-search form-inline">
    <div class="form-group col-md-3">  
        <input type="number" min="1" id="nDays" class="form-control"></input> 
    </div>
    <a id="submit-nDays-search" class="btn btn-success btn-search"
       onclick="javascript:getStatsByNDays()"> <span class="glyphicon glyphicon-search icon-white"></span> ®search®</a>               
</div>
<br/>
<div id="nDays-stats" class="stats">                
    <!-- Results go here -->
</div>
<label id="nDays-search-error" class="label label-important">®stats_nan_error®</label>
<br/>

<div class="jumbotron stats">
    <h4>®stats_other®</h4>

    <div>
        <form action="index.php?action=get_csv_assets" method="post" id="csv_assets_form" name="csv_assets_form" onsubmit="return false">
            <a id="submit-csvAssets-search" class="btn btn-info btn-search" 
               onclick="document.csv_assets_form.submit();
                       return false;"> <span class="icon-file icon-white"></span>®stats_discussions_by_asset®</a>
        </form>               
    </div>
    <br/>
</div>


<script>
    
    $(function () {
        $('#datetimepickerMonths').datetimepicker({
            viewMode: 'years',
            format: 'MM/YYYY'
        });
    });
    
    
    var pieChartGeneral = jQuery.jqplot('pieChartGeneral', [dataArrayPieChart],
            {
                defaultHeight: 1200,
                defaultWidth: 1200,
                title: '®stats_discussions_by_album®',
                seriesDefaults: {
                    // Make this a pie chart.
                    renderer: jQuery.jqplot.PieRenderer,
                    rendererOptions: {
                        sliceMargin: 1,
                        showDataLabels: true
                    }
                },
                legend: {
                    show: false
                },
                highlighter: {
                    show: true,
                    useAxesFormatters: false,
                    tooltipFormatString: '%s',
                    sizeAdjust: 1.5
                }
            });
</script>