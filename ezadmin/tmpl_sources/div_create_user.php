    
<div class="page_title">®create_user®</div>

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
        <label for="user_ID" class="col-sm-3 control-label">®user_ID®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="user_ID" 
                value="<?php if (isset($input) && array_key_exists('user_ID', $input)) {
        echo $input['user_ID'];
    } ?>"/>
        </div>
    </div>
            
    <div class="form-group">
        <label for="surname" class="col-sm-3 control-label">®surname®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="surname" 
                value="<?php if (isset($input) && array_key_exists('surname', $input)) {
        echo $input['surname'];
    } ?>"/>
        </div>
    </div>
            
    <div class="form-group">
        <label for="forename" class="col-sm-3 control-label">®forename®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="forename" 
                   value="<?php if (isset($input) && array_key_exists('forename', $input)) {
        echo $input['forename'];
    }?>"/>
        </div>
    </div>
            
    <div class="form-group">
        <label for="recorder_passwd" class="col-sm-3 control-label">®recorder_passwd®</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" name="recorder_passwd" 
                   value="<?php if (isset($input) && array_key_exists('recorder_passwd', $input)) {
        echo $input['recorder_passwd'];
    }?>"/>
        </div>
    </div>
            
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-3">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="permissions" 
                        <?php if (isset($input) && array_key_exists('permissions', $input)) {
        echo $input['permissions'] == 1 ? 'checked' : '';
    } ?>/>
                    ®is_admin_title®
                </label>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-3">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_ezadmin" 
                        <?php if (isset($input) && array_key_exists('is_ezadmin', $input)) {
        echo $input['is_ezadmin'] == 1 ? 'checked' : '';
    } ?>/>
                    ®is_ezadmin_title®
                </label>
            </div>
        </div>
    </div>
            
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <input type="submit" class="btn btn-success" name="create" value="®create®"/>
        </div>
    </div>
            
</form>