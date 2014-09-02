
<?php
/* EZCAST EZadmin 
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

<h4>®create_course®</h4>
<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    
    <div class="control-group">
        <label for="course_code" class="control-label">®course_code®</label>
        <div class="controls">
            <input type="text" name="course_code" value="<?php echo $input['course_code']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="course_name" class="control-label">®course_name®</label>
        <div class="controls">
            <input type="text" name="course_name" value="<?php echo $input['course_name']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="shortname" class="control-label">®shortname®</label>
        <div class="controls">
            <input type="text" name="shortname" value="<?php echo $input['shortname']?>"/>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="create" value="®create®"/>
    </div>
</form>