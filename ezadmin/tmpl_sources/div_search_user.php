
<h4>®list_users_title®</h4>

<!-- Search form -->
<form method="POST" action="index.php" class="form-inline search_user">
    <input type="hidden" name="action" value="view_users" />
    
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php echo $input['col'] ?>" />
    <input type="hidden" name="order" value="<?php echo $input['order'] ?>" />
    
    <input class="input-large auto-clear placeholder" type="text" placeholder="®user_ID®" title="®user_ID®" name="user_ID" value="<?php echo $input['user_ID']; ?>" />
    <input class="input-large auto-clear placeholder" type="text" placeholder="®forename®" title="®forename®" name="forename" value="<?php echo $input['forename']; ?>" />
    <input class="input-large auto-clear placeholder" type="text" placeholder="®surname®" title="®surname®" name="surname" value="<?php echo $input['surname']; ?>" />

    <input type="submit" name="search" value="®search®" class="btn btn-primary">
    <input type="reset" name="reset" value="®reset®" class="btn"> <br />
    <div><a href="#" onclick="javascript:toggleVisibility('options_block');"><i class="icon-plus"></i> ®options®</a></div>
    <div class="row" style="display: none;" id="options_block">
        <div class="span1"></div>
        <div class="span2">
            <label class="control-label" style="display: inline-block;">
                ®origin®: 
            </label>
            <div class="controls">
            <label class="checkbox">
                <input type="checkbox" title="®intern®" name="intern" <?php echo isset($input['intern']) ? 'checked' : ''; ?> />
                ®intern®
            </label>
            </div>
            <div class="controls">
            <label class="checkbox">
                <input type="checkbox" title="®extern®" name="extern" <?php echo isset($input['extern']) ? 'checked' : ''; ?> />
                ®extern®
            </label>  
            </div>
        </div>
       
    <div class="span2">
        <label class="control-label" style="display: inline-block;">
        ®is_admin_title®: 
        </label>
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®is_admin®" name="is_admin" <?php echo isset($input['is_admin']) ? 'checked' : ''; ?> />
            ®is_admin®
        </label>
        </div>
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®is_not_admin®" name="is_not_admin" <?php echo isset($input['is_not_admin']) ? 'checked' : ''; ?> />
            ®is_not_admin®
        </label>
        </div>
    </div>
    </div>
</form>

<hr>
