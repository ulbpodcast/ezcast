<div class="page_title">®create_classroom®</div>

<form method="POST" class="form-horizontal">
    
    <?php if ($error) {
    ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
            <?php echo $error; ?>
        </div>
    <?php
} ?>
    
    <div class="form-group">
        <label for="room_ID" class="col-sm-2 control-label">®classroom_id®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="room_ID" value="<?php echo $room_ID; ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">®classroom_name®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="name" value="<?php echo $name; ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ip" class="col-sm-2 control-label">®classroom_ip®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="ip" value="<?php echo $ip; ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ip_remote" class="col-sm-2 control-label">®classroom_remote_ip®</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" name="ip_remote" value="<?php echo $ip_remote; ?>"/>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="enabled" <?php $enabled ? 'checked' : ''?>/>®classroom_enabled®
                </label>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ignore_ssh_check" <?php $ignore_ssh_check ? 'checked' : ''?>/>®classroom_ignore_ssh_check®
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