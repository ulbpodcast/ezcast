
<div class="page_title">®list_users_title®</div>

<!-- Search form -->
<form method="POST" action="index.php" class="form-inline search_user">
    <input type="hidden" name="action" value="view_users" />
    
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php if (isset($input) && isset($input['col'])) {
    echo $input['col'];
} ?>" />
    <input type="hidden" name="order" value="<?php if (isset($input) && isset($input['order'])) {
    echo $input['order'];
} ?>" />
    
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®user_ID®" title="®user_ID®" name="user_ID" value="<?php if (isset($input) && isset($input['user_ID'])) {
    echo $input['user_ID'];
} ?>" />
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®forename®" title="®forename®" name="forename" value="<?php if (isset($input) && isset($input['forename'])) {
    echo $input['forename'];
} ?>" />
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®surname®" title="®surname®" name="surname" value="<?php if (isset($input) && isset($input['surname'])) {
    echo $input['surname'];
} ?>" />
    
    <input type="submit" name="search" value="®search®" class="btn btn-primary">
    <input type="reset" name="reset" value="®reset®" class="btn"> <br />
    <div><a href="#" onclick="javascript:toggleVisibility('options_block');"><span class="glyphicon glyphicon-plus"></span> ®options®</a></div>
    <div class="row" style="display: none;" id="options_block">
        
        <div class="col-md-2 col-md-offset-1">
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
        
        <div class="col-md-2">
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
