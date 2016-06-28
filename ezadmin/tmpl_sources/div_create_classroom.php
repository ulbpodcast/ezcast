<h4>®create_classroom®</h4>

<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    
    <div class="control-group">
        <label for="room_ID" class="control-label">®classroom_id®</label>
        <div class="controls">
            <input type="text" name="room_ID" value="<?php echo $input['room_ID']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="name" class="control-label">®classroom_name®</label>
        <div class="controls">
            <input type="text" name="name" value="<?php echo $input['name']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="ip" class="control-label">®classroom_ip®</label>
        <div class="controls">
            <input type="text" name="ip" value="<?php echo $input['ip']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="ip_remote" class="control-label">®classroom_remote_ip®</label>
        <div class="controls">
            <input type="text" name="ip_remote" value="<?php echo $input['ip_remote']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <div class="controls">
            <label class="checkbox"><input type="checkbox" name="enabled" <?php echo $input['enabled'] ? 'checked' : ''?>/>®classroom_enabled®</label>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="create" value="®create®"/>
    </div>
</form>