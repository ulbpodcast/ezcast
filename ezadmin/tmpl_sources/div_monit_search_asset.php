<div class="page_title">®track_asset_title®</div>

<form method="GET" class="search_asset pagination" style="width: 100%;">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    
    
    <!-- Date -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <label for="startDate">®from_date®</label>
                <div class='input-group date' id='startDate'>
                    <input type='text' name='startDate' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <label for="endDate">®to_date®</label>
                <div class='input-group date' id='endDate'>
                    <input type='text' name='endDate' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <label for="status">®monit_status®</label>
                <select name="status" class="form-control">
                    <option value="" selected></option>
                    <?php
                    foreach (EventStatus::getAllEventStatus() as $status) {
                        echo '<option value="'.$status.'"';
                        if(isset($input) && array_key_exists('status', $input) && 
                                $input['status'] != "" && $input['status'] == $status) {
                            echo ' selected';
                        }
                        echo '>'.$status.
                            '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for=""></label>
                <button type="submit" class="btn btn-block btn-success">
                    <span class="glyphicon glyphicon-search icon-white"></span> 
                    ®search®
                </button>
            </div>
            <div class="col-md-1">
                <label for=""></label><br />
                <button type="button" class="btn btn-primary" 
                        data-toggle="modal" data-target="#help_modal">
                    <span class="glyphicon glyphicon-info-sign icon-white"></span> 
                </button>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(function () {
            
            $('#startDate').datetimepicker({
                showTodayButton: true, 
                showClose: true,
                sideBySide: true,
                format: 'YYYY-MM-DD HH:mm',
                <?php
                if(isset($input) && array_key_exists('startDate', $input)) {
                    echo "defaultDate: new Date('".$input['startDate']."')";
                } else {
                    echo 'defaultDate: moment().subtract(4, \'month\')';
                }
                ?>
            });
            
            $('#endDate').datetimepicker({
                showTodayButton: true,
                showClose: true,
                sideBySide: true,
                format: 'YYYY-MM-DD HH:mm',
                <?php
                if(isset($input) && array_key_exists('endDate', $input)) {
                    echo "defaultDate: new Date('".$input['endDate']."')";
                } else {
                    echo 'defaultDate: moment().add(1, \'days\')';
                }
                ?>
            });
            
            $("#startDate").on("dp.change", function (e) {
                $('#endDate').data("DateTimePicker").minDate(e.date);
            });
            $("#endDate").on("dp.change", function (e) {
                $('#startDate').data("DateTimePicker").maxDate(e.date);
            });
            
        });
        
    </script>
    
    
</form>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="help_modal" tabindex="-1" role="dialog" aria-labelledby="Help_Modal_Label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="®monit_help_status_close®">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="Help_Modal_Label">®monit_help_status_title®</h4>
                </div>
                <div class="modal-body">
                    <br />
                    <h3>®monit_help_status_automatic®</h3>
                    <h4>AUTO_SUCCESS</h4>
                    <p>®monit_help_status_auto_success®</p>
                    <h4>AUTO_SUCCESS_ERRORS</h4>
                    <p>®monit_help_status_auto_success_error®</p>
                    <h4>AUTO_SUCCESS_WARNINGS</h4>
                    <p>®monit_help_status_auto_success_warnings®</p>
                    <h4>AUTO_FAILURE</h4>
                    <p>®monit_help_status_auto_failure®</p>
                    <br />
                    <h3>®monit_help_status_manual®</h3>
                    <h4>MANUAL_OK</h4>
                    <p>®monit_help_status_manual_ok®</p>
                    <h4>MANUAL_PARTIAL_OK</h4>
                    <p>®monit_help_status_manual_partial_ok®</p>
                    <h4>MANUAL_FAILURE</h4>
                    <p>®monit_help_status_manual_failure®</p>
                    <h4>MANUAL_IGNORE</h4>
                    <p>®monit_help_status_manual_ignore®</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">®monit_help_status_close®</button>
                </div>
        </div>
    </div>
</div>
