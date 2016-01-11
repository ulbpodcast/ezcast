<h4>®create_course®</h4>
<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    
    <div class="control-group">
        <label for="course_code" class="control-label">®course_code®</label>
        <div class="controls">
            <input type="text" name="course_code" value="<?php echo $input['course_code']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="course_name" class="control-label">®course_name®</label>
        <div class="controls">
            <input type="text" name="course_name" value="<?php echo $input['course_name']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="shortname" class="control-label">®shortname®</label>
        <div class="controls">
            <input type="text" name="shortname" value="<?php echo $input['shortname']?>"/>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="create" value="®create®"/>
    </div>
</form>