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
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <label for="endDate">®to_date®</label>
                <div class='input-group date' id='endDate'>
                    <input type='text' class="form-control" />
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
                <input type="text" class="form-control" name="classroom" id="classroom" placeholder="®monit_classroom®">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="classroom">®monit_teacher®</label>
                <input type="text" class="form-control" name="teacher" id="classroom" placeholder="®monit_teacher®">
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="classroom">®monit_courses®</label>
                <input type="text" class="form-control" name="courses" id="classroom" placeholder="®monit_courses®">
            </div>
        </div>
    </div>
    
    
    <!-- Error level -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <select name="log_level" class="form-control">
                    <option value="0">0 - Emergency</option>
                    <option value="1">1 - Alert</option>
                    <option value="2">2 - Critical</option>
                    <option value="3" selected>3 - Error</option>
                    <option value="4">4 - Warning</option>
                    <option value="5">5 - Notice</option>
                    <option value="6">6 - Info</option>
                    <option value="7">7 - Debug</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="classroom">®monit_context®</label>
                <input type="text" class="form-control" name="context" id="classroom" placeholder="®monit_context®">
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
