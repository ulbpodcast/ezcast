
<div class="page_title">®list_classrooms_title®</div>

<!-- Search form -->
<form method="POST" action="index.php?action=view_classrooms" class="form-inline search_classroom">
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php echo $input['col'] ?>" />
    <input type="hidden" name="order" value="<?php echo $input['order'] ?>" />
    
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®room_ID®" title="®room_ID®" name="room_ID" value="<?php if(isset($input) && isset($input['room_ID'])) echo  $input['room_ID']; ?>" />
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®room_name®" title="®room_name®" name="name" value="<?php if(isset($input) && isset($input['name'])) echo $input['name']; ?>" />
    <input class="form-control input-large auto-clear placeholder" type="text" placeholder="®room_IP®" title="®room_IP®" name="IP" value="<?php if(isset($input) && isset($input['IP'])) echo $input['IP']; ?>" />

    <input type="submit" name="search" value="®search®" class="btn btn-primary">
    <input type="reset" name="reset" value="®reset®" class="btn"> <br />
    
    <!--
    <fieldset style="display:inline-block;">
        ®room_enabled®: 
        <label class="checkbox">
            <input type="checkbox" title="®enabled®" name="enabled" <?php echo isset($input['enabled']) ? 'checked' : ''; ?> />
            ®yes®
        </label>
        <label class="checkbox">
            <input type="checkbox" title="®disabled®" name="not_enabled" <?php echo isset($input['not_enabled']) ? 'checked' : ''; ?> />
            ®no®
        </label>
    </fieldset>
    -->
</form>

<hr>
