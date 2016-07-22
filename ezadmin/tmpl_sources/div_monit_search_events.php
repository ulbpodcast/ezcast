<div class="page_title">®list_event_title®</div>

<form method="GET">
    
    <!-- Date -->
    <div class="form-group">
        <div class="row">
            <div class="col-md-5 col-sm-12">
                <div class='input-group date' id='startDate'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-md-5 col-md-offset-2 col-sm-12">
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
        
//        
        
        
    </script>
    
    
    
    <!-- Classroom, teacher, less -->
    
    <!-- Error level -->
    
    
</form>

