<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
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

<div class="error_message">
    ®Unauthorized®
    <?php if (acl_has_album_permissions($album)) {
    ?>
        <div style="margin-top: 18px;">
            ®Error_permission_denied®
            <form action="index.php?action=bookmarks_album_export" method="post" name="export_album_bookmarks_form" onsubmit="return false">
                <input type="hidden" name="album" value="<?php echo $album; ?>"/>
            </form>
            <br/><br/>
                <a class="button" style="width:300px;" href="javascript: album_token_delete('<?php echo $album; ?>');">®Delete_album®</a>
                <a class="button" style="width:300px;" href="#" onclick="document.export_album_bookmarks_form.submit(); return false;">®Export_album_bookmarks®</a>
            
        </div>
    <?php
} ?>
    <div style="margin-top: 18px;">
        <a class="button" style="width:250px;" href="index.php">®Back_to_home®</a>
    </div>
</div>
