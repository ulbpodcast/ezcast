<div class="page_title">®classroom_calendar_title®</div>

<form method="GET" class="pagination" style="width: 100%">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    
    
    <div class="form-group">
        <div class="row">
            <div class="col-md-4 col-md-offset-1">
                <label class="sr-only" for="classroom">®monit_classroom®</label>
                <input type="text" class="form-control" name="classroom" id="context" placeholder="®monit_classroom®"
                    value="<?php if(isset($input) && array_key_exists('classroom', $input)) { echo $input['classroom']; } ?>"
                    required >
            </div>
            <div class="col-md-2">
                <label class="sr-only" for="nweek">®monit_nbr_week®</label>
                <input type="number" min="0" class="form-control" name="nweek" id="nweek" placeholder="®monit_nbr_week®"
                    value="<?php if(isset($input) && array_key_exists('nweek', $input)) { echo $input['nweek']; } ?>"
                    required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-block btn-success">
                    <span class="glyphicon glyphicon-search icon-white"></span> 
                    ®search®
                </button>
            </div>
        </div>
    </div>
</form>

