
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


<h4>®list_classrooms_title®</h4>

<!-- Search form -->
<form method="POST" action="index.php?action=view_classrooms" class="form-inline search_classroom">
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php echo $input['col'] ?>" />
    <input type="hidden" name="order" value="<?php echo $input['order'] ?>" />
    
    <input class="input-large auto-clear placeholder" type="text" placeholder="®room_ID®" title="®room_ID®" name="room_ID" value="<?php echo $input['room_ID']; ?>" />
    <input class="input-large auto-clear placeholder" type="text" placeholder="®room_name®" title="®room_name®" name="name" value="<?php echo $input['name']; ?>" />
    <input class="input-large auto-clear placeholder" type="text" placeholder="®room_IP®" title="®room_IP®" name="IP" value="<?php echo $input['IP']; ?>" />

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
