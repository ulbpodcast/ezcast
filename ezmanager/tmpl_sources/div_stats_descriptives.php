<div id="div_stats_descriptives">
    <div class="BlocPodcastMenu">
        <?php if (isset($stats['graph']['album']['display']) && $stats['graph']['album']['display']) {
    ?>
            <div id="containerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
            <br />
            <hr />
            <br />
        <?php
} ?>
        <?php if (isset($stats['graph']['video']['display']) && $stats['graph']['video']['display']) {
        ?>
            <div id="containerVideo" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
            <br />
            <hr />
            <br />
        <?php
    } ?>
        <br />
        <h4>®Stats_table_info®</h4>
        <br />
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>®Stats_access_video_nbr®</td>
                    <td><?php echo $stats['descriptive']['access']; ?></td> 
                </tr>
                <tr>
                    <td>®Stats_view_total®</td>
                    <td><?php echo $stats['calculate']['view_total']; ?></td> 
                </tr>
                <tr>
                    <td>®Stats_view_unique_total®</td>
                    <td><?php echo $stats['calculate']['view_unique']; ?></td> 
                </tr>
                <tr>
                    <td>®Stats_bookmark_official_nbr®</td>
                    <td><?php echo $stats['descriptive']['bookmark_official']; ?></td> 
                </tr>
                <tr>
                    <td>®Stats_bookmark_personal_nbr®</td>
                    <td><?php echo $stats['descriptive']['bookmark_personal']; ?></td> 
                </tr>
                <tr>
                    <td>®Stats_bookmarks_view_ratio®</td>
                    <td><?php echo $stats['calculate']['bookmarks_view_ratio']; ?> %</td> 
                </tr>
                <tr>
                    <td>®Stats_threads_nbr®</td>
                    <td><?php echo $stats['descriptive']['threads']; ?></td> 
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="popover fade bottom" role="tooltip" id="stats_tooltip" style="display: block;position: fixed;">
    <div class="arrow" style="left: 50%;"></div>
    <div class="popover-content"></div>
</div>

<script>
Highcharts.setOptions({
    lang: {
        months: ["®month_01®", "®month_02®", "®month_03®", "®month_04®", "®month_05®", "®month_06®", "®month_07®", 
            "®month_08®", "®month_09®", "®month_10®", "®month_11®", "®month_12®"],
        shortMonths: ["®short_month_01®", "®short_month_02®", "®short_month_03®", "®short_month_04®", "®short_month_05®", 
            "®short_month_06®", "®short_month_07®", "®short_month_08®", "®short_month_09®", "®short_month_10®", 
            "®short_month_11®", "®short_month_12®"]
    }
});

<?php if (isset($stats['graph']['album']['display']) && $stats['graph']['album']['display']) {
        ?>
    // Month graphic
    Highcharts.stockChart('containerMonth', {
        chart: {
            zoomType: 'x',
            backgroundColor: 'rgba(255, 255, 255, 0)'
        },
        title: {
            text: '®Graph_month_view®',
            align: 'left'
        },
        rangeSelector: {
            inputDateFormat: "%b %Y",
            inputEditDateFormat: "%Y-%m",
            buttonTheme: {
                width: null,
                padding: 2
            },
            buttons: [{
                    type: 'month',
                    count: 3,
                    text: '3 ®month®'
                }, {
                    type: 'month',
                    count: 6,
                    text: '6 ®month®'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1 ®year®'
                }, {
                    type: 'all',
                    text: '®all®'
                }]
        },
        xAxis: {
            tickInterval: 30 * 24 * 3600 * 1000, // 30 days in milliseconds
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%b \'%y',
                year: '%Y'
            },
            title: {
                text: '®Graph_nbr_view_x®'
            },
            units: [
               //how to show months? Don't show units for now until we find out
               [
                    'month',
                    [1,2,3,4,5,6,7,8,9,10,11,12]
               ]
            ],
        },
        yAxis: {
            title: {
                text: '®Graph_nbr_view_y®'
            },
            labels: {
                align: "right"
            },
            opposite: false
        },
        legend: {
            enabled: true,
            layout: 'horizontal',
            verticalAlign: 'bottom'
        },
        tooltip: {
            shared: true,
            xDateFormat: '%B %Y'
        },
        series: [{
            name: '®Graph_nbr_total_view®',
            type: 'column',
            color: '#064c93',
            data: <?php echo $stats['graph']['album']['str_totalview']; ?>
        }, {
            name: '®Graph_nbr_unique_view®',
            type: 'column',
            color: '#454445',
            data: <?php echo $stats['graph']['album']['str_uniqueview']; ?>
        }]
    });
<?php
    } ?>


<?php if (isset($stats['graph']['video']['display']) && $stats['graph']['video']['display']) {
        ?>
    var allAssets = <?php echo $stats['graph']['video']['str_all_asset']; ?>;
    
    // Asset graphic
    Highcharts.chart('containerVideo', {
        chart: {
            zoomType: 'x',
            backgroundColor: 'rgba(255, 255, 255, 0)'
        },
        title: {
            text: '®Graph_video_view®',
            align: 'left'
        },
        xAxis: {
            categories: allAssets,
            min: 0,
            max: Math.min(allAssets.length-1, 15),
            scrollbar: {
                enabled: true
            }
        },
        yAxis: { // Primary yAxis
            title: {
                text: '®Graph_nbr_view_y®'
            },
            labels: {
                align: "right"
            },
            opposite: false,
            tickInterval: 1,
        },
        tooltip: {
            shared: true
        },
        series: [{
                type: 'column',
                name: '®Graph_nbr_total_view®',
                color: '#064c93',
                data: <?php echo $stats['graph']['video']['str_total_view']; ?>
            }, {
                type: 'column',
                name: '®Graph_nbr_unique_view®',
                color: '#454445',
                data: <?php echo $stats['graph']['video']['str_unique_view']; ?>
            }]
    });
<?php
    } ?>

var containerMonth = $('#containerMonth');
var containerVideo = $('#containerVideo');

containerMonth.on('mouseenter', '.highcharts-legend-item', display_tooltip);
containerMonth.on('mouseleave','.highcharts-legend-item', hide_tooltip);

containerVideo.on('mouseenter', '.highcharts-legend-item', display_tooltip);
containerVideo.on('mouseleave','.highcharts-legend-item', hide_tooltip);

function display_tooltip(event) {
    var seriesName = $(event.currentTarget).text();
    var tooltip = $('#stats_tooltip');
    var rectTooltip = tooltip[0].getBoundingClientRect();
    var distanceToMiddle = (rectTooltip.right-rectTooltip.left)/2;
    var rectLegende = $(event.currentTarget)[0].getBoundingClientRect();
    var middleLegend = rectLegende.left + (rectLegende.right-rectLegende.left)/2;
    var displayText = "";
    
    switch(seriesName) {
        case '®Graph_nbr_total_view®':
            displayText = '®Graph_nbr_total_view_tooltip®';
            break;
        
        case '®Graph_nbr_unique_view®':
            displayText = '®Graph_nbr_unique_view_tooltip®';
            break;
        
        default:
            return;
    }
    
    $('#stats_tooltip .popover-content').text(displayText);
    tooltip.css({left:middleLegend-distanceToMiddle, top:rectLegende.bottom});
    tooltip.addClass('in');
};

function hide_tooltip(event) {
    $('#stats_tooltip').removeClass('in');
}


</script>