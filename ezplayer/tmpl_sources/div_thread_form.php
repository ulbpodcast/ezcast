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

<div class="form" id="thread_form">

    <div id="thread_form_header">
        <span class="thread-logo" style="padding-bottom: 8px;"></span>
        <span class="form_header_label">®Add_discussion®</span>
    </div>
    <div id='thread_form_wrapper'>

        <form action="index.php" method="post" id="submit_thread_form" onsubmit="return false">
            <input type="hidden" name="album" id="thread_album" value="<?php echo $album; ?>"/>
            <input type="hidden" name="asset" id="thread_asset" value="<?php echo $asset; ?>"/>
            <input type="hidden" name="assetTitle" id="thread_asset_title" value="<?php echo get_asset_title($album, $asset); ?>" />

            <br/>

            <!-- Title field -->           
            <label>®Title®&nbsp;:
                <span class="small">®Title_info®</span>
            </label>
            <input name="title" tabindex='18' id="thread_title" type="text" placeholder="®Discussion_title_placeholder®" maxlength="140"/>

            <!-- Timecode field -->           
            <label>®Timecode®&nbsp;:
                <span class="small">®Timecode_info®</span>
            </label>
            <input name="timecode" tabindex='19' id="thread_timecode" type="text" value="0" onblur="tinymce.get('thread_desc_tinymce').focus();
                    ;"/>

            <!-- Description field -->
            <label>®Message®&nbsp;:
                <span class="small">®Required®</span>
            </label>
            <div id="thread_description_wrapper">
                <input type="text" name="description" tabindex='20' id="thread_desc_tinymce" required/>
            </div>

            <!-- Visibility field --> 
            <input name="visibility" id="thread_visibility" type="checkbox" hidden/>
            <br/>
            <!-- Submit button -->
            <div class="cancelButton" style="margin-left: 480px;">
                <a class="button" tabindex='21' href="javascript: player_thread_form_hide(true);">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a class="button green2" tabindex='22' 
                   <?php
                   if (!acl_has_moderated_album() || acl_is_admin()) {
                       echo "href='javascript:popup_thread_visibility()' ";
                   } else {
                       echo "href='javascript:if(thread_form_check()) thread_form_submit()' ";
                   }
                   ?>
                   >®Post_discussion®</a>
            </div>
        </form>
    </div>
</div>

