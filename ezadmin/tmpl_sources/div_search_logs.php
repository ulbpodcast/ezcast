
<div class="page_title">®logs_title®</div>

<!-- Search form -->
<form method="POST" action="index.php?action=view_logs" class="form-inline search_logs">  
    <div class="form-group">
        <input class="form-control input input-medium auto-clear placeholder" type="text" placeholder="®date_start®" 
               title="®date_start®" name="date_start" 
               value="<?php if(isset($input) && array_key_exists('date_start', $input)) { echo $input['date_start']; } ?>" />
        <br />
        <input class="form-control input input-medium auto-clear placeholder" type="text" placeholder="®date_end®" 
               title="®date_end®" name="date_end" 
               value="<?php if(isset($input) && array_key_exists('date_end', $input)) { echo $input['date_end']; } ?>" />
    </div>
        
    <div class="form-group">
        <?php
        // Create variable to specific whitch option is selected
        $selected = '';
        if(isset($input) && array_key_exists('table', $input)) {
            $selected = $input['table'];
        }
        ?>
        <select name="table" class="form-control" title="®table®">
            <option value="users" <?php if($selected == 'users') { echo 'selected'; }?>>
                ®table_users®
            </option>
            <option value="courses" <?php if($selected == 'courses') { echo 'selected'; }?>>
                ®table_courses®
            </option>
            <option value="users_courses" <?php if($selected == 'users_courses') { echo 'selected'; }?>>
                ®table_users_courses®
            </option>
            <option value="classrooms" <?php if($selected == 'classrooms') { echo 'selected'; }?>>
                ®table_classrooms®
            </option>
            <option value="all" <?php if($selected == '' || $selected == 'all') { echo 'selected'; }?> >
                ®table_all®
            </option>
        </select>

        <input class="form-control input input-medium auto-clear placeholder" type="text" placeholder="®author®" 
               title="®author®" name="author" 
               value="<?php if(isset($input) && array_key_exists('author', $input)) { echo $input['author']; } ?>" />
    </div>
        
    <input type="hidden" name="post" value="logs" />
    <input type="hidden" name="page" value="1" />

    <div class="form-group text-center">
        <input type="submit" name="search" value="®search®" class="btn btn-primary">
        <input type="reset" name="reset" value="®reset®" class="btn btn-default"> <br />
    </div>
</form>

<hr>