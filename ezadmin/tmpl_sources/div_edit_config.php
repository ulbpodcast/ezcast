
<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
*
* This software is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

<form class="form-horizontal" method="post" action="index.php?action=edit_config">
    <legend>®ezadmin_edit_config_title®</legend>
    
    <?php echo $alert; ?> <!-- Displays alert, if any -->
    
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
    <div class="control-group">
        <label class="checkbox">
            <input type="checkbox" name="recording_enabled" id="recording_enable" <?php if($params['recorders_option']) echo 'checked="checked"'; ?> />®classrooms_recording_enabled®
        </label>
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
    <div class="control-group">
        <label class="checkbox">
            <input type="checkbox" name="add_users_enabled" id="add_users_enable" <?php if($params['add_users_option']) echo 'checked="checked"'; ?> />®add_users_enabled®
        </label>
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
    <div class="control-group">
        <label class="checkbox">
            <input type="checkbox" name="password_storage_enabled" id="password_storage_enable" <?php if($params['recorder_password_storage_option']) echo 'checked="checked"'; ?> />®password_storage_enabled®
        </label>
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
    <div class="control-group">
        <label class="checkbox">
            <input type="checkbox" name="courses_by_name" id="courses_by_name" <?php if($params['courses_by_name']) echo 'checked="checked"'; ?> />®settings_use_course_name®
        </label>
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
    <div class="control-group">
        <label class="checkbox">
            <input type="checkbox" name="users_by_name" id="users_by_name" <?php if($params['users_by_name']) echo 'checked="checked"'; ?> />®settings_use_user_name®
        </label>
    </div>
    
    <!-- Confirm -->
    <input type="submit" name="confirm" value="®edit®" class="btn btn-primary">
</form>
