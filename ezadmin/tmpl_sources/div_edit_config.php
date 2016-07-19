
<div class="page_title">®ezadmin_edit_config_title®</div>


<form class="form-horizontal" method="post" action="index.php?action=edit_config">
    
    <!-- Displays alert, if any -->
    <?php if(isset($alert)) { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
                    <?php echo $alert; ?>
        </div>
    <?php } ?>
    
    
    <!-- Enable/disable classrooms -->
    <!-- <div class="control-group">
        <label class="control-label">®classrooms_recording_enabled®</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="recording_enabled" id="recording_enable" value="enabled" <?php if($params['recorders_option']) echo 'checked="checked"'; ?> />
                ®enabled®
            </label>
            
            <label class="radio">
                <input type="radio" name="recording_enabled" id="recording_disable" value="disabled" <?php if(!$params['recorders_option']) echo 'checked="checked"'; ?> />
                ®disabled®
            </label>
        </div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="recording_enabled" id="recording_enable" <?php if($params['recorders_option']) echo 'checked="checked"'; ?> />®classrooms_recording_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <!-- Enable/disable adding users -->
    <!-- <div class="control-group">
        <label class="control-label">®add_users_enabled®</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="add_users_enabled" id="add_users_enable" value="enabled" <?php if($params['add_users_option']) echo 'checked="checked"'; ?> />
                ®enabled®
            </label>
            
            <label class="radio">
                <input type="radio" name="add_users_enabled" id="add_users_disable" value="disabled" <?php if(!$params['add_users_option']) echo 'checked="checked"'; ?> />
                ®disabled®
            </label>
        </div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="add_users_enabled" id="add_users_enable" <?php if($params['add_users_option']) echo 'checked="checked"'; ?> />®add_users_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <!-- Enable/disable classroom password storage -->
    <!-- <div class="control-group">
        <label class="control-label">®password_storage_enabled®</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="password_storage_enabled" id="password_storage_enable" value="enabled" <?php if($params['recorder_password_storage_option']) echo 'checked="checked"'; ?> />
                ®enabled®
            </label>
            
            <label class="radio">
                <input type="radio" name="password_storage_enabled" id="password_storage_disable" value="disabled" <?php if(!$params['recorder_password_storage_option']) echo 'checked="checked"'; ?> />
                ®disabled®
            </label>
        </div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="password_storage_enabled" id="password_storage_enable" <?php if($params['recorder_password_storage_option']) echo 'checked="checked"'; ?> />®password_storage_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <!-- Display courses by name or by code -->
    <!-- <div class="control-group">
        <label class="control-label">®how_to_display_courses®</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="how_to_display_courses" id="courses_by_code" value="course_code" <?php if(!$params['use_course_name']) echo 'checked="checked"'; ?> />
                ®display_course_by_code®
            </label>
            
            <label class="radio">
                <input type="radio" name="how_to_display_courses" id="courses_by_name" value="course_name" <?php if($params['use_course_name']) echo 'checked="checked"'; ?> />
                ®display_course_by_name®
            </label>
        </div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="courses_by_name" id="courses_by_name" <?php if($params['courses_by_name']) echo 'checked="checked"'; ?> />®settings_use_course_name®
                </label>
            </div>
        </div>
    </div>
    
    <!-- Display users by name or by code -->
    <!--
    <div class="control-group">
        <label class="control-label">®how_to_display_users®</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="how_to_display_users" id="users_by_ID" value="user_ID" <?php if(!$params['use_user_name']) echo 'checked="checked"'; ?> />
                ®display_course_by_code®
            </label>

            <label class="radio">
                <input type="radio" name="how_to_display_users" id="users_by_name" value="user_name" <?php if($params['use_user_name']) echo 'checked="checked"'; ?> />
                ®display_course_by_name®
            </label>
        </div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="users_by_name" id="users_by_name" <?php if($params['users_by_name']) echo 'checked="checked"'; ?> />®settings_use_user_name®
                </label>
            </div>
        </div>
    </div>
    
    <!-- Confirm -->
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-5">
            <input type="submit" name="confirm" value="®edit®" class="btn btn-success">
        </div>
    </div>
</form>
