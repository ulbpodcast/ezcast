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

<form id="search_form" method="post" action="index.php?action=threads_bookmarks_search" onsubmit="return false">
    <input type="text" name="search" tabindex='0' id="main_search"/>
    <a id="main_search_button" class="search_button" onclick="search_form_check();"></a>
    <a id="more_search_button" onclick="$('#advanced_search').slideToggle(200);"></a>
    <div id="advanced_search">
        <div class="search-label">®Search_in® : </div>
        <div id="target">
            <label ><input tabindex='1' onclick="search_form_setup();" type="radio" name="target" checked="checked" value="global"><span>®All®</span></label>
            <label><input tabindex='2' id="album_radio" onclick="search_form_setup();" type="radio" name="target" value="album"><span>®Albums®</span></label>
            <label><input tabindex='3' id="current_radio" onclick="search_form_setup();" type="radio" name="target" value="current"><span>®Current®</span></label>
        </div>  
        <?php
        $search_albums = acl_authorized_albums_list();
        if (!isset($search_albums) || count($search_albums) < 1) {
            ?>
            <ul class="search_albums"><li style="border:none;">®No_album®</li></ul>
        <?php
        } else {
            ?>
            <ul class="search_albums">
                <?php foreach ($search_albums as $selected_album) {
                ?>
                    <li><label><input tabindex='4' type="checkbox" name="albums[]" <?php if ($_SESSION['album'] == '' || $_SESSION['album'] == $selected_album) {
                    echo 'checked';
                } ?>  value="<?php echo $selected_album ?>"/><?php echo get_album_title($selected_album); ?></label></li>
                <?php
            } ?>
            </ul>
        <?php
        } ?>
        <div class="search_current">
            <?php if (isset($_SESSION['album']) && $_SESSION['album'] != '') {
            ?>
                ®Search_album®
                <?php if (isset($_SESSION['asset']) && $_SESSION['asset'] != '') {
                ?>
                    ®Search_asset®
                <?php
            } ?>
            <?php
        } else {
            ?>
                ®Search_global®
            <?php
        } ?>
        </div> 
        <div class="search-label">®Search_among® : </div>
        <div class="search_cat">
            <div class="search_fields">
                <label style="display:block;">
                    <input tabindex='5' id="cb_toc" type="checkbox" checked="checked" onclick="javascript:search_options_adjust()" 
                           name="tab[]" value="official"/>®Toc®</label>
                <label style="display:block;">
                    <input tabindex='6' id="cb_bookmark" onclick="javascript:search_options_adjust()" 
                           type="checkbox" checked="checked" name="tab[]" value="custom"/>®Personal®</label>
                <label style="display:block;">
                    <input tabindex='7' id="cb_threads" type="checkbox" onclick="javascript:search_options_adjust()" 
                           checked="checked" name="tab[]" value="threads"/>®Discussions®</label>
            </div>
        </div>
        <div id="search_bookmarks">
            <div class="search-label">®Bookmark®</div>
            <div class="search_fields">
                <label style="display:block;"><input tabindex='8' type="checkbox" checked="checked" name="fields[]" value="title"/>®Title®</label>
                <label style="display:block;"><input tabindex='9' type="checkbox" checked="checked" name="fields[]" value="description"/>®Description®</label>
                <label style="display:block;"><input tabindex='10' type="checkbox" checked="checked" name="fields[]" value="keywords"/>®Keywords®</label>
                <label>®Level® : </label>
                <input tabindex='11' type="number" name="level" min="0" max="3" value="0"/>
            </div>
        </div>
        <div id="search_threads">
            <div class="search-label">®Discussions®</div>
            <div class="search_fields">
                <label style="display:block;">
                    <input tabindex='11' type="checkbox" id="discussuion_title" checked="checked" name="fields_thread[]" value="title"/>®Title®</label>
                <label style="display:block;">
                    <input tabindex='12' type="checkbox" id="discussion_comment" checked="checked" name="fields_thread[]" value="message"/>®Comment®</label>
            </div>
        </div>
    </div>
</form>
<script>
    $('#search_form input').keydown(function(e) {
        if (e.keyCode == 13) {
            search_form_check();
        }
    });
</script>


