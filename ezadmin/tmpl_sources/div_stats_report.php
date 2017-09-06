
<div class="page_title">®report_title®</div>

<form method="GET" class="search_event pagination hidden-print" style="width: 100%;">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    
    
    <div class="form-group">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <label for="start_date">®from_date®</label>
                <div class='input-group date' id='start_date'>
                    <input type='text' name='start_date' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <label for="end_date">®to_date®</label>
                <div class='input-group date' id='end_date'>
                    <input type='text' name='end_date' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
    
            <div class="col-md-2">
                <div class="checkbox">
                    <br />
                    <label>
                        <input type="checkbox" name="general"
                            <?php if (isset($input) && array_key_exists('general', $input)) {
    echo 'checked';
} ?>> 
                        ®report_form_general®
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="checkbox">
                    <br />
                    <label>
                        <input type="checkbox" name="ezplayer" 
                            <?php if (isset($input) && array_key_exists('ezplayer', $input)) {
    echo 'checked';
} ?>> 
                        ®report_form_ezplayer®
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <br />
                <button type="submit" class="btn btn-block btn-success" 
                        data-loading-text="<img style='height: 16px;' src='img/loading_transparent.gif'/> ®loading®..."
                        onClick="$(this).button('loading');">
                    <span class="glyphicon glyphicon-refresh icon-white"></span> 
                    ®report_form_generate®
                </button>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(function () {
            
            $('#start_date').datetimepicker({
                showTodayButton: true, 
                showClose: true,
                sideBySide: true,
                format: 'YYYY-MM-DD',
                <?php
                if (isset($input) && array_key_exists('start_date', $input)) {
                    echo "defaultDate: new Date('".$input['start_date']."')";
                } else {
                    echo 'defaultDate: moment().subtract(6, \'month\')';
                }
                ?>
            });
            
            $('#end_date').datetimepicker({
                showTodayButton: true,
                showClose: true,
                sideBySide: true,
                format: 'YYYY-MM-DD',
                <?php
                if (isset($input) && array_key_exists('end_date', $input)) {
                    echo "defaultDate: new Date('".$input['end_date']."')";
                } else {
                    echo 'defaultDate: moment().add(1, \'days\')';
                }
                ?>
            });
            
            $("#start_date").on("dp.change", function (e) {
                $('#end_date').data("DateTimePicker").minDate(e.date);
            });
            $("#end_date").on("dp.change", function (e) {
                $('#start_date').data("DateTimePicker").maxDate(e.date);
            });
            
        });
        
    </script>
    
</form>

<br /><br />
<?php if (array_key_exists('post', $input)) {
                    ?>

<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    ®report_info_date®
    <?php echo date("d/m/y", strtotime($input['start_date'])); ?> ®report_info_date_end®
    <?php echo date("d/m/y", strtotime($input['end_date'])); ?>
</div>

<h4>Général</h4>
<table class="table table-bordered table-hover"> 
    <thead> 
        <tr> 
            <th class="col-md-10"></th> 
            <?php if ($general) {
                        ?>
            <th class="col-md-1">®report_column_general®</th> 
            <?php
                    } ?>
            <th class="col-md-1">®report_column_date®</th> 
        </tr> 
    </thead> 
    <tbody> 
        <tr data-toggle="collapse" data-target=".list_all_author" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_all_author"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_all_author®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_author(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_author(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_all_author" style="padding: 5px" id="list_all_author" 
                     aria-labelledby="list_all_author">
                    <?php $i=0;
                    foreach ($report->get_date_list_author() as $author => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$author.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_submit_author" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_submit_author"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_submit_author®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_submit_author(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_submit_author(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_submit_author" style="padding: 5px" id="list_submit_author" 
                     aria-labelledby="list_submit_author">
                    <?php $i=0;
                    foreach ($report->get_date_list_submit_author() as $author => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$author.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_record_author" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_record_author"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_record_author®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_record_author(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_record_author(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_record_author" style="padding: 5px" id="list_record_author" 
                     aria-labelledby="list_record_author">
                    <?php $i=0;
                    foreach ($report->get_date_list_record_author() as $author => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$author.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_cours" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_cours"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_list_cours®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_cours(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_cours(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_cours" style="padding: 5px" id="list_cours" 
                     aria-labelledby="list_cours">
                    <?php $i=0;
                    foreach ($report->get_date_list_cours() as $cours => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$cours.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_cours_submit" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_cours_submit"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_list_cours_submit®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_cours_submit(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_cours_submit(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_cours_submit" style="padding: 5px" id="list_cours_submit" 
                     aria-labelledby="list_cours_submit">
                    <?php $i=0;
                    foreach ($report->get_date_list_cours_submit() as $cours => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$cours.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_cours_record" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_cours_record"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_row_list_cours_record®
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_nbr_list_all_cours_record(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_nbr_date_list_cours_record(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_cours_record" style="padding: 5px" id="list_cours_record" 
                     aria-labelledby="list_cours_record">
                    <?php $i=0;
                    foreach ($report->get_date_list_cours_record() as $cours => $nbr) {
                        if (++$i > $MAX_DETAILS_LIST) {
                            break;
                        }
                        echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                        echo '<div class="col-md-10">'.$cours.'</div>';
                    }
                    if ($i >= $MAX_DETAILS_LIST) {
                        echo '<div class="col-md-10 col-md-offset-2">...</div>';
                    } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr style="height: 15px;"></tr>
        
        <tr> 
            <td>
                ®report_row_total_asset®
                <p class="help-block" style="margin: 1px;">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    ®report_row_no_delete_test®
                </p>
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_count_total_asset(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_date_count_asset(); ?></td> 
        </tr>
        <tr> 
            <td>
                ®report_row_asset_submit®
                <p class="help-block" style="margin: 1px;">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    ®report_row_no_delete_test®
                </p>
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_count_submit_asset(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_date_count_submit_asset(); ?></td> 
        </tr>
        <tr> 
            <td>
                ®report_row_asset_record®
                <p class="help-block" style="margin: 1px;">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    ®report_row_no_delete_test®
                </p>
            </td> 
            <?php if ($general) {
                        ?>
            <td><?php echo $report->get_count_record_asset(); ?></td>
            <?php
                    } ?>
            <td><?php echo $report->get_date_count_record_asset(); ?></td> 
        </tr>
        
    </tbody> 
</table>

<?php if ($ezplayer) {
                        ?>
<br />
<h4>EZPlayer</h4>
<table class="table table-bordered table-hover"> 
    <thead> 
        <tr> 
            <th class="col-md-10"></th> 
            <?php if ($general) {
                            ?>
            <th class="col-md-1">®report_column_general®</th> 
            <?php
                        } ?>
            <th class="col-md-1">®report_column_date®</th> 
        </tr> 
    </thead> 
    <tbody> 
        <tr> 
            <td>
                ®report_ezplayer_row_total_users®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_nbr_total_user(); ?></td>
            <?php
                        } ?>
            <td>/</td> 
        </tr>
        <tr> 
            <td>
                ®report_ezplayer_row_total_thread®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_total_thread(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_date_total_thread(); ?></td> 
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_cours_thread" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_cours_thread"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_ezplayer_row_course_thread®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_nbr_list_cours_thread(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_nbr_date_cours_thread(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_cours_thread" style="padding: 5px" id="list_cours_thread" 
                     aria-labelledby="list_cours_thread">
                    <?php $i=0;
                        foreach ($report->get_ezplayer_date_cours_thread() as $cours => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'
                            . '<a class="hidden-print-link" target="_blank" '
                                . 'href="index.php?action=view_course_details&course_code='.$cours.'" >'
                            .$cours
                            . '</a></div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_total_comment®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_total_comment(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_date_nbr_comment(); ?></td> 
        </tr>
        
        <tr data-toggle="collapse" data-target=".list_cours_comment" class="collapsed accordion-toggle" 
            aria-expanded="false" aria-controls="list_cours_comment"> 
            <td>
                <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                ®report_ezplayer_row_course_comment®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_nbr_list_cours_comment(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_nbr_date_cours_comment(); ?></td> 
        </tr>
        <tr>
            <td colspan="3" class="hidden_row">
                <div class="accordian-body collapse list_cours_comment" style="padding: 5px" id="list_cours_comment" 
                     aria-labelledby="list_cours_comment">
                    <?php $i=0;
                        foreach ($report->get_ezplayer_date_cours_comment() as $cours => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'
                            . '<a href="index.php?action=view_course_details&course_code='.$cours.'"'
                                . ' class="hidden-print-link" target="_blank">'
                            .$cours
                            . '</a></div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                    <div class="col-md-12"><br /></div>
                </div>
            </td>
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_total_bookmark®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_total_bookmark(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_date_total_bookmark(); ?></td> 
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_bookmark_official®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_total_offi_bookmark(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_date_offi_bookmark(); ?></td> 
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_bookmark_personal®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_total_pers_bookmark(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_date_pers_bookmark(); ?></td> 
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_bookmark_user_official®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_nbr_list_user_offi_bookmark(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_nbr_date_user_offi_bookmark(); ?></td> 
        </tr>
        
        <tr> 
            <td>
                ®report_ezplayer_row_bookmark_user_personal®
            </td>
            <?php if ($general) {
                            ?>
            <td><?php echo $report->get_ezplayer_nbr_list_user_pers_bookmark(); ?></td>
            <?php
                        } ?>
            <td><?php echo $report->get_ezplayer_nbr_date_user_pers_bookmark(); ?></td> 
        </tr>
        
    </tbody>
</table>
<?php
                    } ?>

<h3>®report_title_date_info®</h3>

<?php if (!empty($allClassRoom)) {
                        ?>
<div class="col-md-12">
    <h5>®report_classroom_utilisation®</h5>
    <table class="table table-bordered table-hover"> 
        <thead> 
            <tr> 
                <th class="col-md-10">®report_classroom®</th> 
                <th class="col-md-1">®report_record_number®</th> 
                <th class="col-md-1">®report_record_time®</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php foreach ($allClassRoom as $classroom => $value) {
                            echo '<tr>';
                            echo '<td>'.$classroom.'</td>';
                            echo '<td>'.$value['nbr'].'</td>';
                            echo '<td>'.convert_seconds($value['time']).'</td>';
                        } ?>
            <tr class="warning"> 
                <td>
                    ®report_total_submit®
                </td>
                <td><?php echo $totalSubmit['nbr']; ?></td>
                <td><?php echo convert_seconds($totalSubmit['time']); ?></td> 
            </tr>
            <tr class="warning"> 
                <td>
                    ®report_total_classroom®
                </td>
                <td><?php echo $totalClassroom['nbr']; ?></td>
                <td><?php echo convert_seconds($totalClassroom['time']); ?></td> 
            </tr>
            <tr class="danger"> 
                <td>
                    ®report_total®
                </td>
                <td><?php echo($totalSubmit['nbr']+$totalClassroom['nbr']); ?></td>
                <td><?php echo convert_seconds($totalSubmit['time']+$totalClassroom['time']); ?></td> 
            </tr>
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <br />
    <div class="progress">
        <div class="progress-bar progress-bar-success" style="width: <?php echo $percentSubmit; ?>%">
            <?php echo $percentSubmit; ?>% ®report_video_submit®
        </div>
        <div class="progress-bar progress-bar-info" style="width: <?php echo $percentClassrooms; ?>%">
            <?php echo $percentClassrooms; ?>% ®report_video_record®
        </div>
    </div>
</div>

<div class="col-md-10">
    <div id="container_classroom_util" style="width: 100%; height: 400px; margin: 0 auto"></div>

    <script>
    $(function () {
        $('#container_classroom_util').highcharts({
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: "®report_classroom_utilisation®"
            },
            xAxis: [{
                categories: ['<?php echo implode("', '", array_keys($allClassRoom)); ?>'],
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: "®report_record_number®",
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: "®report_record_time®",
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    formatter: function() {
                        return convert_seconds(this.value);
                    },
                    /*format: '{value}',*/
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true,
                formatter: function() {
                    var hour, min, sec;
                    hour = Math.floor(this.points[0].y/3600);
                    min = Math.floor((this.points[0].y/60)%60);
                    sec = this.points[0].y%60;
                    return '<b>' + this.x + ':</b><br />' +
                            "®report_record_time®: <b>" + 
                            convert_seconds(this.points[0].y) + '</b><br /><b>' + 
                            this.points[1].y + '</b> ®report_nbr_record®' + 
                            ((this.points[1].y > 1) ? 's' : '');
                }
            },
            series: [{
                type: 'column',
                name: "<?php echo ucfirst("®report_record_time®"); ?>",
                yAxis: 1,
                data: [<?php echo implode(', ', array_map(function ($ar) {
                            return $ar['time'];
                        }, $allClassRoom)); ?>]

            }, {
                type: 'column',
                name: "<?php echo ucfirst("®report_nbr_record®"); ?>",
                data: [<?php echo implode(', ', array_map(function ($ar) {
                            return $ar['nbr'];
                        }, $allClassRoom)); ?>]
            }]
        });
    });
    
    function convert_seconds(duration) {
        var hour, min, sec;
        hour = Math.floor(duration/3600);
        min = Math.floor((duration/60)%60);
        sec = duration%60;
        
        if(hour > 0) {
            if(min < 10) {
                min = '0' + min;
            }
            if(sec < 10) {
                sec = '0' + sec;
            }
            return hour + ':' + min + ':' + sec;
        } else if(min > 0) {
            if(sec < 10) {
                sec = '0' + sec;
            }
            return min + ':' + sec;
        } else {
            return sec + 's';
        }
    }
    
    
    </script>
</div>

<div class="col-md-2 col-print-md-12">
    <h5>®report_classroom_unused®</h5>
    <ul>
    <?php foreach ($classroom_not_use as $classroom) {
                            echo '<li>'.$classroom.'</li>';
                        } ?>
    </ul>
</div>


<?php
                    } ?>

<?php if ($ezplayer) {
                        ?>
<div class="col-md-12"><br />
    <h4>®report_form_ezplayer®</h4>
    <table class="table table-bordered table-hover"> 
        <thead> 
            <tr> 
                <th class="col-md-10"></th> 
                <th class="col-md-1">®report_row_value®</th> 
            </tr> 
        </thead> 
        <tbody> 
            <tr data-toggle="collapse" data-target=".list_user_login" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="list_user_login"> 
                <td>
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_user_login®
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_list_user_login(); ?></td> 
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse list_user_login" style="padding: 5px" id="list_user_login" 
                         aria-labelledby="list_user_login">
                        <div class="col-md-2"><b>®report_nbr_connexion®</b></div>
                        <div class="col-md-10"><b>®report_user®</b></div>
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_list_user_login() as $user => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.$user.'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr> 
                <td>
                    ®report_row_nbr_anonym_user®
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_list_ip_login(); ?></td> 
            </tr>
            
            <tr data-toggle="collapse" data-target=".list_user_browser" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="list_user_browser"> 
                <td colspan="2">
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_user_browser®
                </td>
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse list_user_browser" style="padding: 5px" id="list_user_browser" 
                         aria-labelledby="list_user_browser">
                        <div class="col-md-2 text-right"><b>®report_nbr_connexion®</b></div>
                        <div class="col-md-2 col-md-offset-1"><b>®report_browser®</b></div>
                        <div class="col-md-7"><b>®report_os®</b></div>
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_list_user_browser() as $browser => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            $strBrowser = explode('|', $browser);
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-1">'.calcul_percent($nbr, $totalBrowser).'%</div>';
                            echo '<div class="col-md-2">'.$strBrowser[0].'</div>';
                            echo '<div class="col-md-7">'.$strBrowser[1].'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr data-toggle="collapse" data-target=".list_album" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="list_album"> 
                <td>
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_list_album®
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_list_album(); ?></td> 
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse list_album" style="padding: 5px" id="list_album" 
                         aria-labelledby="list_album">
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_list_album() as $album => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.$album.'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr data-toggle="collapse" data-target=".list_album_click" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="list_album_click"> 
                <td>
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_album_click®
                    <p class="help-block" style="margin: 1px;">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        ®report_row_album_click_info®
                    </p>
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_list_album_click(); ?></td> 
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse list_album_click" style="padding: 5px" id="list_album_click" 
                         aria-labelledby="list_album_click">
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_list_album_click() as $album => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.$album.'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr> 
                <td>
                    ®report_row_nbr_unique_asset®
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_unique_asset(); ?></td> 
            </tr>
            
            <tr> 
                <td>
                    ®report_row_nbr_asset®
                    <p class="help-block" style="margin: 1px;">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        ®report_warning_asset®
                    </p>
                </td>
                <td><?php echo $report->get_ezplayer_nbr_date_asset(); ?></td> 
            </tr>
            
            <tr data-toggle="collapse" data-target=".unique_asset" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="unique_asset"> 
                <td colspan="2">
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_list_asset®
                </td>
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse unique_asset" style="padding: 5px" id="unique_asset" 
                         aria-labelledby="unique_asset">
                        <div class="col-md-2"><b>®report_row_view_number®</b></div>
                        <div class="col-md-10"><b>®report_asset®</b></div>
                        <div class="col-md-12"></div>
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_unique_asset() as $asset => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.
                                    '<a href="./index.php?action=view_events&post=&asset='.$asset.'"'
                                    . 'target="_blank" class="hidden-print-link" >'.
                                    $asset
                                    .'</a></div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr data-toggle="collapse" data-target=".cours_pers_bookmark" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="cours_pers_bookmark"> 
                <td colspan="2">
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_cours_bookmark_personal®
                </td>
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse cours_pers_bookmark" style="padding: 5px" id="cours_pers_bookmark" 
                         aria-labelledby="cours_pers_bookmark">
                        <div class="col-md-2"><b>®report_bookmark_add®</b></div>
                        <div class="col-md-10"><b>®report_cours®</b></div>
                        <div class="col-md-12"></div>
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_cours_pers_bookmark() as $cours => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.$cours.'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
            <tr data-toggle="collapse" data-target=".user_offi_bookmark" class="collapsed accordion-toggle" 
                aria-expanded="false" aria-controls="user_offi_bookmark"> 
                <td colspan="2">
                    <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                    ®report_row_cours_bookmark_official®
                </td>
            </tr>
            <tr>
                <td colspan="3" class="hidden_row">
                    <div class="accordian-body collapse user_offi_bookmark" style="padding: 5px" id="user_offi_bookmark" 
                         aria-labelledby="user_offi_bookmark">
                        <div class="col-md-2"><b>®report_bookmark_add®</b></div>
                        <div class="col-md-10"><b>®report_user®</b></div>
                        <?php $i=0;
                        foreach ($report->get_ezplayer_date_user_offi_bookmark() as $user => $nbr) {
                            if (++$i > $MAX_DETAILS_LIST) {
                                break;
                            }
                            echo '<div class="col-md-1 col-md-offset-1">'.$nbr.'</div>';
                            echo '<div class="col-md-10">'.$user.'</div>';
                        }
                        if ($i >= $MAX_DETAILS_LIST) {
                            echo '<div class="col-md-10 col-md-offset-2">...</div>';
                        } ?>
                        <div class="col-md-12"><br /></div>
                    </div>
                </td>
            </tr>
            
        </tbody>
    </table>
    
    <?php if ($json_view_asset_data != "[[]]") {
                            ?>
    <div class="col-md-12">
        <br /><br />
        <div id="container_asset_view" style="height: 500px; width: 100%"></div>
        
        <script>
        $(function() {
            // Create the chart
            $('#container_asset_view').highcharts('StockChart', {
                rangeSelector : {
                    selected : 1
                },
                title : {
                    text : "®report_asset_distribution®"
                },
                series : [{
                    type: 'column',
                    name : "®report_asset_number®",
                    data: <?php echo $json_view_asset_data; ?>,
                    dataGrouping: {
                        approximation: "sum",
                        enabled: true,
                        forced: true,
                        units: [['month',[1]]]
                    }
                }]
            });
        });
        </script>
        <br />
        <br />
    </div>
    
    <?php
                        }
                        echo '</div>';
                    } ?>

<div id="container_status_percent" class="col-md-6 col-print-md-12 text-center" style="height: 400px; margin: 0 auto"></div>
<script>
$(function () {
    $('#container_status_percent').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        colors: ['#5cb85c', 'red', '#777'],
        title: {
            text: "®report_status_percent_success®",
            align: 'center',
            verticalAlign: 'middle',
            y: 40
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white',
                        textShadow: '0px 1px 2px black'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%']
            }
        },
        series: [{
            type: 'pie',
            innerSize: '50%',
            data: [
                ["®report_status_success®",   <?php echo $percentSuccess; ?>],
                ["®report_status_error®",       <?php echo $percentError; ?>]
            ]
        }]
    });
});    
</script>

<div id="container_camslide_graph" class="col-md-6 col-print-md-12 text-center" style="height: 400px; margin: 0 auto"></div>
<script>
$(function () {
    $('#container_camslide_graph').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: "®report_camslide_distribution®"
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        return this.y > 1 ? (this.y + ' %'): null;
                    },
                    color: '#ffffff',
                    distance: -30
                },
                showInLegend: true
            }
        },
        series: [{
            colorByPoint: true,
            data: [
                <?php $i = 0;
                    foreach ($nbr_camslide as $infos) {
                        ++$i;
                        if ($i > 1) {
                            echo ',';
                        }
                        echo '{';
                        echo 'name: "'.ucfirst($infos['cam_slide']).'",';
                        echo 'y: '.calcul_percent($infos['total_type'], $total_nbr_camslide);
                        echo '}';
                    } ?>
                ]
        }]
    });
});    
</script>

<div class="col-md-12">
    <br /><br /><br />
    <div id="container_status_date" style="height: 500px; width: 100%"></div>
    <br /><br /><br />
</div>
<script>
$(function() {
    // Create the chart
    $('#container_status_date').highcharts('StockChart', {
        chart: {
            type: 'area'
        },
        rangeSelector : {
            selected : 1
        },
        title : {
            text : "®report_status_sum®"
        },
        legend: {
            enabled: true,
            align: 'right',
            layout: 'vertical',
            verticalAlign: 'top',
            y: 100,
            shadow: true
        },
        colors: ['#5cb85c', 'red', '#777'],
        series : [{
                name : "®report_status_success®",
                data: <?php echo $json_status_date_success; ?>,
                dataGrouping: {
                    approximation: "sum",
                    enabled: true,
                    forced: true,
                    units: [['month',[1]]]
                }
            }, {
                name : "®report_status_error®",
                data: <?php echo $json_status_date_error; ?>,
                dataGrouping: {
                    approximation: "sum",
                    enabled: true,
                    forced: true,
                    units: [['month',[1]]]
                }
            }]
    });
});
</script>

<?php if ($ezplayer) {
                        ?>
<div class="col-md-6 col-print-md-12 add-print-space">
    <div id="container_browser" style="height: 500px; width: 100%"></div>
</div>
<script>
$(function() {
    // Build the chart
    $('#container_browser').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: "®report_browser_graphic®"
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +'</b><br />' + this.y +' %' : null;
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            colorByPoint: true,
            data: [<?php $i =0;
                        foreach ($report->get_ezplayer_date_list_user_system() as $browser => $nbr) {
                            ++$i;
                            if ($i > 1) {
                                echo ',';
                            }
                            echo '{';
                            echo 'name: "'.$browser.'",';
                            echo 'y: '.calcul_percent($nbr, $totalBrowser);
                            echo '}';
                        } ?> ]
            }]
    });
});
</script>

<div class="col-md-6 col-print-md-12">
    <div id="container_os" style="height: 500px; width: 100%"></div>
    <br /><br /><br />
</div>
<script>
$(function() {
    // Build the chart
    $('#container_os').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: "®report_os_graphic®"
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +'</b><br />' + this.y +' %' : null;
                    }
                    /*format: '<b>{point.name}</b><br />{point.percentage:.1f} %'*/
                },
                showInLegend: true
            }
        },
        series: [{
            colorByPoint: true,
            data: [<?php $i =0;
                        foreach ($report->get_ezplayer_date_list_user_os() as $browser => $nbr) {
                            ++$i;
                            if ($i > 1) {
                                echo ',';
                            }
                            echo '{';
                            echo 'name: "'.$browser.'",';
                            echo 'y: '.calcul_percent($nbr, $totalBrowser);
                            echo '}';
                        } ?> ]
            }]
    });
});
</script>

<?php
                    }
                } // if post
