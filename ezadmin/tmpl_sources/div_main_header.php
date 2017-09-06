<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <!-- Note: for details about the layout and Boostrap framwork, see http://getbootstrap.com/ -->

        <title><?php
            global $appname;
            echo $appname;
            if (isset($_SESSION['changes_to_push'])) {
                echo ' (®unsaved_changes®)';
            }?></title>
        <meta name="description" content="EZAdmin is an application to manage EZCast" />
        <script type="text/javascript" src="js/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="js/moment.min.js"></script>
        <script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
        <script type="text/javascript" src="js/plugins/jqplot.pieRenderer.min.js"></script>
        <script type="text/javascript" src="js/plugins/jqplot.highlighter.js"></script>
        <script type="text/javascript" src="js/plugins/jqplot.enhancedPieLegendRenderer.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="bootstrap/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="js/typeahead.min.js"></script>
        <script type="text/javascript" src="js/js_utils.js"></script>
        
        <script src="js/stats.js"></script>
        
        <!-- Calendar -->
        <script src="js/angular.min.js"></script>
        <script src="bootstrap/js/ui-bootstrap-tpls.min.js"></script>
        <script src="bootstrap/js/angular-bootstrap-calendar.min.js"></script>
        
        <!-- HighCharts -->
        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        
        <link href="bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
        <link href="css/typeahead.min.css" rel="stylesheet">
        <link rel="stylesheet" href="bootstrap/css/bootstrap-datetimepicker.min.css" />
        <link href="bootstrap/css/bootstrap-select.min.css" rel="stylesheet"/>
        <link href="bootstrap/css/angular-bootstrap-calendar.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link type="text/css" href="commons/css/common_style.css" rel="stylesheet" />
        <link href="css/jquery.jqplot.min.css" rel="stylesheet"/>
        <link href="css/ezplayerStats.css" rel="stylesheet"/>
        
        <?php
            global $apache_documentroot;
            $custom_folder = "$apache_documentroot/ezadmin/css/custom/";
            $dir = new DirectoryIterator($custom_folder);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile()) {
                    echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$fileinfo->getFilename().'"/>';
                }
            }
        ?>
 
        
    </head>
    <body link="#000088" vlink="#000044" alink="#0000ff" 
        <?php if (isset($GLOBALS['debugmode']) && ($GLOBALS['debugmode'] == "devl")) {
            echo 'background="#99ff99"';
        } ?>>

        <div class="container_ezplayer">
        <?php include template_getpath('div_header.php'); ?>
            <div id="global">

        <div class="container">
            <div class="row">
<?php include template_getpath('div_main_menu.php'); ?>
                <div class="col-md-10">
                    <?php 
                    global $sample_config_version;
                    global $config_version;
                    if ($sample_config_version > $config_version) {
                        echo '<div class="alert alert-danger" role="alert">®new_config_version®</div>';
                    } elseif ($sample_config_version < $config_version) {
                        echo '<div class="alert alert-info" role="alert">®config_more_recent_version®</div>';
                    } ?>

