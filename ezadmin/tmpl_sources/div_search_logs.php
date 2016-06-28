
<h4>®logs_title®</h4>

<!-- Search form -->
<form method="POST" action="index.php?action=view_logs" class="form-inline search_logs">  
    <input class="input input-medium auto-clear placeholder" type="text" placeholder="®date_start®" title="®date_start®" name="date_start" value="<?php echo $input['date_start']; ?>" />
    <input class="input input-medium auto-clear placeholder" type="text" placeholder="®date_end®" title="®date_end®" name="date_end" value="<?php echo $input['date_end']; ?>" />
    <select name="table" title="®table®">
        <option value="users">®table_users®</option>
        <option value="courses">®table_courses®</option>
        <option value="users_courses">®table_users_courses®</option>
        <option value="classrooms">®table_classrooms®</option>
        <option value="all" selected="selected">®table_all®</option>
    </select>
    <input class="input input-medium auto-clear placeholder" type="text" placeholder="®author®" title="®author®" name="author" value="<?php echo $input['author']; ?>" />
    
    <input type="hidden" name="post" value="logs" />
    <input type="hidden" name="page" value="1" />

    <input type="submit" name="search" value="®search®" class="btn btn-primary">
    <input type="reset" name="reset" value="®reset®" class="btn"> <br />
</form>

<hr>