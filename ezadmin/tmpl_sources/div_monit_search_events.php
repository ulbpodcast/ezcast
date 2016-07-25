<div class="page_title">®list_event_title®</div>

<form method="GET" class="search_event">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" 
           value="<?php if(isset($input) && array_key_exists('col', $input)) { echo $input['col']; } ?>" />
    <input type="hidden" name="order" 
           value="<?php if(isset($input) && array_key_exists('order', $input)) { echo $input['order']; } ?>" />
    
    
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
            var dateNow = new Date();
            
            $('#startDate').datetimepicker({
                showTodayButton: true, 
                showClose: true,
                sideBySide: true,
                format: 'DD/MM/YYYY HH:mm',
                useCurrent: true,
                defaultDate: dateNow
            });
            
            $('#endDate').datetimepicker({
                showTodayButton: true,
                showClose: true,
                sideBySide: true,
                format: 'DD/MM/YYYY HH:mm',
                defaultDate: dateNow
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
                <input type="text" class="form-control" name="classroom" id="classroom" placeholder="®monit_classroom®"
                    value="<?php if(isset($input) && array_key_exists('classroom', $input)) { echo $input['classroom']; } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="teacher">®monit_teacher®</label>
                <input type="text" class="form-control" name="teacher" id="teacher" placeholder="®monit_teacher®"
                    value="<?php if(isset($input) && array_key_exists('teacher', $input)) { echo $input['teacher']; } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="courses">®monit_courses®</label>
                <input type="text" class="form-control" name="courses" id="courses" placeholder="®monit_courses®"
                    value="<?php if(isset($input) && array_key_exists('origin', $input)) { echo $input['origin']; } ?>">
            </div>
        </div>
    </div>
    
    <!-- asset, origin, type_id, error level -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label class="sr-only" for="asset">®monit_asset®</label>
                <input type="text" class="form-control" name="asset" id="classroom" placeholder="®monit_asset®"
                    value="<?php if(isset($input) && array_key_exists('classroom', $input)) { echo $input['classroom']; } ?>">
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="origin"">®monit_origin®</label>
                <input type="text" class="form-control" name="origin" id="origin" placeholder="®monit_origin®"
                    value="<?php if(isset($input) && array_key_exists('origin', $input)) { echo $input['origin']; } ?>">
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="type_id">®monit_type_id®</label>
                <select name="type_id" class="form-control">
                    <option value=""></option>
                    <?php
                    foreach ($logger->event_type_id as $nameEventType => $num) {
                        echo '<option value='.$num.'">'.$nameEventType.'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="log_level" class="form-control">
                    <option value="" selected></option>
                    <option value="0">0 - Emergency</option>
                    <option value="1">1 - Alert</option>
                    <option value="2">2 - Critical</option>
                    <option value="3">3 - Error</option>
                    <option value="4">4 - Warning</option>
                    <option value="5">5 - Notice</option>
                    <option value="6">6 - Info</option>
                    <option value="7">7 - Debug</option>
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
                    value="<?php if(isset($input) && array_key_exists('context', $input)) { echo $input['context']; } ?>">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="message">®monit_message®</label>
                <input type="text" class="form-control" name="message" id="message" placeholder="®monit_message®"
                    value="<?php if(isset($input) && array_key_exists('message', $input)) { echo $input['message']; } ?>">
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
