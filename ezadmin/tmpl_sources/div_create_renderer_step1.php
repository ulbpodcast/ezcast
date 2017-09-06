<div class="page_title">®create_renderer_step_1®</div>

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
    
    
    <input type="hidden" name="renderer_step" value="1"/>
    
    <div class="form-group">
        <label for="renderer_name" class="col-md-2 control-label">®renderer_name®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="renderer_name" 
                   value="<?php if (isset($input) && array_key_exists('renderer_name', $input)) {
        echo $input['renderer_name'];
    } ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="renderer_address" class="col-md-2 control-label">®renderer_address®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="renderer_address" 
                   value="<?php if (isset($input) && array_key_exists('renderer_address', $input)) {
        echo $input['renderer_address'];
    }?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="renderer_user" class="col-md-2 control-label">®renderer_user®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="renderer_user" 
                   value="<?php if (isset($input) && array_key_exists('renderer_user', $input)) {
        echo $input['renderer_user'];
    } ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="enabled" 
                        <?php if (isset($input) && array_key_exists('enabled', $input)) {
        echo $input['enabled'] ? 'checked' : '';
    } ?>/>
                    ®renderer_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <input type="submit" class="btn btn-primary" value="®continue®"/>
        </div>
    </div>
</form>