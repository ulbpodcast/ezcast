<div class="page_title">®create_course®</div>
<form method="POST" class="form-horizontal">
    
    <?php if (isset($error)) {
    ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
            <?php echo $error; ?>
        </div>
    <?php
} ?>
    
    <div class="form-group">
        <label for="course_code" class="col-md-2 control-label">®course_code®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="course_code" value="<?php if (isset($input['course_code'])) {
        echo $input['course_code'];
    }?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="course_name" class="col-md-2 control-label">®course_name®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="course_name" value="<?php if (isset($input['course_name'])) {
        echo $input['course_name'];
    }?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="course_name" class="col-md-2 control-label">®recorders®</label>
        <div class="col-sm-5">
            <input type="checkbox" name="in_recorders" />
        </div>
    </div>
	
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <input type="submit" class="btn btn-success" name="create" value="®create®"/>
        </div>
    </div>
</form>
