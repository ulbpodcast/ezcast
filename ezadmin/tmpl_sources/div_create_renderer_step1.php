<h4>®create_renderer_step_1®</h4>

<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    <input type="hidden" name="renderer_step" value="1"/>
    <div class="control-group">
        <label for="renderer_name" class="control-label">®renderer_name®</label>
        <div class="controls">
            <input type="text" name="renderer_name" value="<?php echo $input['renderer_name']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_address" class="control-label">®renderer_address®</label>
        <div class="controls">
            <input type="text" name="renderer_address" value="<?php echo $input['renderer_address']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_user" class="control-label">®renderer_user®</label>
        <div class="controls">
            <input type="text" name="renderer_user" value="<?php echo $input['renderer_user']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <div class="controls">
            <label class="checkbox"><input type="checkbox" name="enabled" <?php echo $input['enabled'] ? 'checked' : ''?>/>®renderer_enabled®</label>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="®continue®"/>
    </div>
</form>