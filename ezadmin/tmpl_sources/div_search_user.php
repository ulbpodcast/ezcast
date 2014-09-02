
<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
*
* This software is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

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
