<div class="page_title">®create_classroom®</div>

<form method="POST" class="form-horizontal">
    
    <?php if(isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
            <?php echo $error; ?>
        </div>
    <?php } ?>
    
    <div class="form-group">
        <label for="room_ID" class="col-sm-2 control-label">®classroom_id®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="room_ID" value="<?php if(isset($input['room_ID'])) echo $input['room_ID']?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">®classroom_name®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="name" value="<?php if(isset($input['name'])) echo $input['name']?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ip" class="col-sm-2 control-label">®classroom_ip®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="ip" value="<?php if(isset($input['ip']))echo $input['ip']?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ip_remote" class="col-sm-2 control-label">®classroom_remote_ip®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="ip_remote" value="<?php if(isset($input['ip_remote']))echo $input['ip_remote']?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="enabled" <?php echo (isset($input['enabled']) && $input['enabled']) ? 'checked' : ''?>/>®classroom_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <input type="submit" class="btn btn-success" name="create" value="®create®"/>
        </div>
    </div>
</form>