
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

<h4>®create_classroom®</h4>

<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    
    <div class="control-group">
        <label for="room_ID" class="control-label">®classroom_id®</label>
        <div class="controls">
            <input type="text" name="room_ID" value="<?php echo $input['room_ID']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="name" class="control-label">®classroom_name®</label>
        <div class="controls">
            <input type="text" name="name" value="<?php echo $input['name']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="ip" class="control-label">®classroom_ip®</label>
        <div class="controls">
            <input type="text" name="ip" value="<?php echo $input['ip']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="ip_remote" class="control-label">®classroom_remote_ip®</label>
        <div class="controls">
            <input type="text" name="ip_remote" value="<?php echo $input['ip_remote']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <div class="controls">
            <label class="checkbox"><input type="checkbox" name="enabled" <?php echo $input['enabled'] ? 'checked' : ''?>/>®classroom_enabled®</label>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="create" value="®create®"/>
    </div>
</form>