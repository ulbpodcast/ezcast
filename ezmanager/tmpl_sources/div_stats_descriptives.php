<?php 
/*
* EZCAST EZmanager 
*
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
* UI Design by Julien Di Pietrantonio
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

<div id="div_stats_descriptives">
    <div class="BlocPodcastMenu">
        <?php if(isset($stats['graph']['album']['display']) && $stats['graph']['album']['display']) { ?>
            <div id="containerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
            <br />
        <?php } ?>
        <?php if(isset($stats['graph']['video']['display']) && $stats['graph']['video']['display']) { ?>
            <div id="containerVideo" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
            <br />
        <?php } ?>
        <br />
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>®Stats_access_video_nbr®</td>
                    <td><?php echo $stats['descriptive']['access']; ?></td> 
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
                    <td>®Stats_threads_nbr®</td>
                    <td><?php echo $stats['descriptive']['threads']; ?></td> 
                </tr>
            </tbody>
        </table>
    </div>
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

<?php if(isset($stats['graph']['album']['display']) && $stats['graph']['album']['display']) { ?>
    // Month graphic
    Highcharts.stockChart('containerMonth', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: '®Graph_month_view®'
        },
        rangeSelector: {
            inputDateFormat: "%b %Y",
            inputEditDateFormat: "%Y-%m"
        },
        xAxis: {
            tickInterval: 30 * 24 * 3600 * 1000,
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%b \'%y',
                year: '%Y'
            }
        },
        yAxis: [{ // Primary yAxis
            title: {
                text: '®Graph_nbr_view®',
                margin: 40
            },
            labels: {
                align: "right",
                x: 25
            }

        }, { // Second yAxis
            gridLineWidth: 0,
            title: {
                text: '®Graph_nbr_thread®'
            },
            opposite: false
        }],
        tooltip: {
            shared: true,
            xDateFormat: '%B %Y'
        },
        series: [{
            name: '®Graph_nbr_total_view®',
            type: 'column',
            yAxis: 0,
            data: <?php echo $stats['graph']['album']['str_totalview']; ?>

        }, {
            name: '®Graph_nbr_unique_view®',
            type: 'column',
            yAxis: 0,
            data: <?php echo $stats['graph']['album']['str_uniqueview']; ?>

        }, {
            name: '®Graph_nbr_thread®',
            type: 'spline',
            yAxis: 1,
            data: <?php echo $stats['graph']['album']['str_comment']; ?>
        }]
    });
<?php } ?>


<?php if(isset($stats['graph']['video']['display']) && $stats['graph']['video']['display']) { ?>
    // Asset graphic
    Highcharts.chart('containerVideo', {
        title: {
            text: 'Vue par asset'
        },
        xAxis: {
            categories: <?php echo $stats['graph']['video']['str_all_asset']; ?>,
            min: 0,
            max: 15,
            scrollbar: {
                enabled: true
            }
        },
        yAxis: [{ // Primary yAxis
            title: {
                text: '®Graph_nbr_view®',
                margin: 40
            },
            labels: {
                align: "right",
                x: 25
            },
            opposite: true

        }, { // Second yAxis
            gridLineWidth: 0,
            title: {
                text: '®Graph_nbr_thread®'
            },
            opposite: false
        }],
        tooltip: {
            shared: true
        },
        series: [{
                type: 'column',
                name: '®Graph_nbr_total_view®',
                yAxis: 0,
                data: <?php echo $stats['graph']['video']['str_total_view']; ?>
            }, {
                type: 'column',
                name: '®Graph_nbr_unique_view®',
                yAxis: 0,
                data: <?php echo $stats['graph']['video']['str_unique_view']; ?>
            }, {
                type: 'spline',
                name: '®Graph_nbr_thread®',
                yAxis: 1,
                data: <?php echo $stats['graph']['video']['str_total_comment']; ?>
            }]
    });
<?php } ?>

</script>