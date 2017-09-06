<div class="page_title">®list_event_title®</div>

<form method="GET" class="search_event pagination" style="width: 100%;">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    
    
    <!-- Date -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <label for="startDate">®from_date®</label>
                <div class='input-group date' id='startDate'>
                    <input type='text' name='startDate' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <label for="endDate">®to_date®</label>
                <div class='input-group date' id='endDate'>
                    <input type='text' name='endDate' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
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
                if (isset($input) && array_key_exists('startDate', $input) && $input['startDate'] != "") {
                    echo "defaultDate: new Date('".$input['startDate']."')";
                } else {
                    echo 'defaultDate: moment().subtract(7, \'days\')';
                }
                ?>
            });
            
            $('#endDate').datetimepicker({
                showTodayButton: true,
                showClose: true,
                sideBySide: true,
                format: 'YYYY-MM-DD HH:mm',
                <?php
                if (isset($input) && array_key_exists('endDate', $input)) {
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
    
    <!-- Classroom, teacher, courses -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <label class="sr-only" for="classroom">®monit_classroom®</label>
                <input type="text" class="form-control classroomSuggest" name="classroom" id="classroom" placeholder="®monit_classroom®"
                    value="<?php if (isset($input) && array_key_exists('classroom', $input)) {
                    echo $input['classroom'];
                } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="teacher">®monit_author®</label>
                <input type="text" class="form-control" name="teacher" id="teacher" placeholder="®monit_author®"
                    value="<?php if (isset($input) && array_key_exists('teacher', $input)) {
                    echo $input['teacher'];
                } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="courses">®monit_courses®</label>
                <input type="text" class="form-control" name="courses" id="courses" placeholder="®monit_courses®"
                    value="<?php if (isset($input) && array_key_exists('courses', $input)) {
                    echo $input['courses'];
                } ?>">
            </div>
        </div>
    </div>
    
    <!-- asset, origin, type_id, error level -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label class="sr-only" for="asset">®monit_asset®</label>
                <input type="text" class="form-control" name="asset" id="asset" placeholder="®monit_asset®"
                    value="<?php if (isset($input) && array_key_exists('asset', $input)) {
                    echo $input['asset'];
                } ?>">
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="origin"">®monit_origin®</label>
                <input type="text" class="form-control" name="origin" id="origin" placeholder="®monit_origin®"
                    value="<?php if (isset($input) && array_key_exists('origin', $input)) {
                    echo $input['origin'];
                } ?>">
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="type_id">®monit_type_id®</label>
                <select name="type_id" class="form-control">
                    <option value="" selected></option>
                    <?php
                    foreach (EventType::$event_type_id as $nameEventType => $num) {
                        echo '<option value="'.$num.'"';
                        if (isset($input) && array_key_exists('type_id', $input) &&
                                $input['type_id'] != "" && $input['type_id'] == $num) {
                            echo ' selected';
                        }
                        echo '>'.$nameEventType.
                            '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <select name="log_level[]" class="form-control selectpicker" multiple data-actions-box="true"
                        title="®monit_select_loglevel®">
                    <?php foreach (LogLevel::$log_levels as $nameLog => $lvlLog) {
                        $nameLevel = $lvlLog . " - " . ucfirst($nameLog);
                        echo '<option value="'.$lvlLog.'" ';
                        echo 'data-content="<span class=\'label label-'.$nameLog.'\'>'.$nameLevel.'</span>"';
                        if (isset($input) &&
                            (isset($logLevel_default_max_selected) && $lvlLog <= $logLevel_default_max_selected)
                                ||
                            (is_array($input['log_level']) &&
                            in_array($lvlLog, $input['log_level']) && $input['log_level'][0] != null)) {
                            echo 'selected';
                        }
                        echo '> '.$nameLevel. '</option>';
                    } ?>
                </select>
            </div>
        </div>
    </div>
    
    
    <!-- Context, message and submit -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <label class="sr-only" for="context">®monit_context®</label>
                <input type="text" class="form-control" name="context" id="context" placeholder="®monit_context®"
                    value="<?php if (isset($input) && array_key_exists('context', $input)) {
                        echo $input['context'];
                    } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="message">®monit_message®</label>
                <input type="text" class="form-control" name="message" id="message" placeholder="®monit_message®"
                    value="<?php if (isset($input) && array_key_exists('message', $input)) {
                        echo $input['message'];
                    } ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-block btn-success">
                    <span class="glyphicon glyphicon-search icon-white"></span> 
                    ®search®
                </button>
            </div>
        </div>
    </div>
    
</form>


<script>
$('.classroomSuggest').typeahead({
  hint: true,
  highlight: true,
  minLength: 0
},
{
  name: 'classroom',
  source: substringMatcher(<?php echo $js_classroom; ?>)
});
</script>