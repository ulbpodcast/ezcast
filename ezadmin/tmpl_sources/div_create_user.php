
<h4>®create_user®</h4>
<form method="POST" class="form-horizontal">
        
        <?php if($error) { ?>
            <div class="alert alert-error"><?php echo $error ?></div>
        <?php } ?>
    
	<div class="control-group">
		<label for="user_ID" class="control-label">®user_ID®</label>
		<div class="controls">
			<input type="text" name="user_ID" value="<?php echo $input['user_ID']?>"/>
		</div>
	</div>

	<div class="control-group">
		<label for="surname" class="control-label">®surname®</label>
		<div class="controls">
			<input type="text" name="surname" value="<?php echo $input['surname']?>"/>
		</div>
	</div>

	<div class="control-group">
		<label for="forename" class="control-label">®forename®</label>
		<div class="controls">
			<input type="text" name="forename" value="<?php echo $input['forename']?>"/>
		</div>
	</div>

	<div class="control-group">
		<label for="recorder_passwd" class="control-label">®recorder_passwd®</label>
		<div class="controls">
			<input type="password" name="recorder_passwd" value="<?php echo $input['recorder_passwd']?>"/>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="checkbox">®is_admin_title®<input type="checkbox" name="permissions" <?php echo $input['permissions'] == 1 ? 'checked' : '' ?>/></label>
		</div>
	</div>
            
        <div class="control-group">
		<div class="controls">
			<label class="checkbox">®is_ezadmin_title®<input type="checkbox" name="is_ezadmin" <?php echo $input['is_ezadmin'] == 1 ? 'checked' : '' ?>/></label>
		</div>
	</div>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="create" value="®create®"/>
	</div>

</form>