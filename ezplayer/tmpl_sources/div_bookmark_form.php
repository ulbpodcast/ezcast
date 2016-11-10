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

<div class="form" id="bookmark_form">
    <div id="bookmark_form_header" class="bookmark-color">
        <span id="bookmark_form_header_logo" class="bookmark-logo"></span>
        <span class="form_header_label" >®Add_bookmark®</span>
    </div>
    <div id="bookmark_form_header" class="toc-color">
        <span id="bookmark_form_header_logo" class="toc-logo"></span>
        <span class="form_header_label" >®Add_toc®</span>
    </div>
    <div id='bookmark_form_wrapper'>
        <form action="index.php" method="post" id="submit_bookmark_form" onsubmit="return false">
            <input type="hidden" name="album" id="bookmark_album" value="<?php echo $album; ?>"/>
            <input type="hidden" name="asset" id="bookmark_asset" value="<?php echo $asset; ?>"/>
            <!-- bookmark_type and bookmark_source are filled in in player.js (show_bookmark_form(...)) -->
            <input type="hidden" name="type" id="bookmark_type" value=""/>
            <input type="hidden" name="source" id="bookmark_source" value=""/><br/>

            <br/>

            <!-- Title field -->           
            <label>®Title®&nbsp;:
                <span class="small">®Title_info®</span>
            </label>
            <input name="title" tabindex='11' id="bookmark_title" type="text" maxlength="70"/>


            <!-- keywords field -->
            <label>®Keywords®&nbsp;:
                <span class="small">®Keywords_info®</span>
            </label>
            <input name="keywords" tabindex='13' id="bookmark_keywords" type="text"/>

            <!-- Description field -->
            <label>®Description®&nbsp;:
                <span class="small">®optional®</span>
            </label>
            <textarea name="description" tabindex='12' id="bookmark_description" rows="4" ></textarea>

            <br/>

            <!-- level field -->
            <label>®Level®&nbsp;:
                <span class="small">®Level_info®</span>
            </label>
            <input type="number" name="level" tabindex='14' id="bookmark_level" min="1" max="3" value="1"/>

            <!-- Timecode field -->           
            <label>®Timecode®&nbsp;:
                <span class="small">®Timecode_info®</span>
            </label>
            <input name="timecode" tabindex='15' id="bookmark_timecode" type="number" value="0" required/>

            <br/><br/>
            <!-- Submit button -->
            <div class="cancelButton">
                <a class="button" tabindex='16' href="javascript: player_bookmark_form_hide(true);">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a id="subBtn" class="button" tabindex='17' href="javascript: if(bookmark_form_check()) bookmark_form_submit();">®submit_bookmark®</a>
            </div>
            <br />
        </form>
    </div>
</div>
<script>
    $('#bookmark_form input').keydown(function (e) {
        if (e.keyCode == 13) {
            if (bookmark_form_check())
                bookmark_form_submit();
        }
    });
</script>


