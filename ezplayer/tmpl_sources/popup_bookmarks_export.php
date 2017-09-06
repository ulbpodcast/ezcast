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

<?php
include_once 'lib_print.php';
?> 

<h2><b style="text-transform:uppercase;"><?php echo suffix_remove($album); ?></b> // <?php echo get_album_title($album); ?></h2>
<?php if (isset($asset_meta['title'])) {
    ?>
    <h3><?php echo $asset_meta['title']; ?></h3>
<?php
} ?>
<br/><p>®Select_bookmarks_message®</p>
<a class="close-reveal-modal" href="javascript:close_popup();">&#215;</a>
<br/>

<?php if (isset($bookmarks) && count($bookmarks) > 0) {
        ?>
    <form action="index.php?action=bookmarks_export" method="post" id="select_export_bookmark_form" name="export_bookmark_form" onsubmit="return false">
        <input type="hidden" name="album" id="export_album" value="<?php if (isset($album)) {
            echo $album;
        } ?>"/>
        <input type="hidden" name="asset" id="export_asset" value="<?php if (isset($asset_meta['record_date'])) {
            echo $asset_meta['record_date'];
        }; ?>"/>
        <input type="hidden" name="target" id="export_toc_target" value="<?php echo $tab; ?>"/><br/>
        <ul>
            <li style="border-bottom: solid 1px #cccccc;"><input type="checkbox" onclick="toggle_checkboxes(this, 'export_selection[]')" name="check_all"/><span class="<?php echo(($tab == 'custom') ? 'blue-title' : 'orange-title'); ?>"><b>®Date®</b></span><span class="<?php echo(($tab == 'custom') ? 'blue-title' : 'orange-title'); ?>"><b>®Bookmark®</b></span></li>
            <?php foreach ($bookmarks as $index => $bookmark) {
            ?>
                <li>
                    <input style="float: left;" type="checkbox" name="export_selection[]" value="<?php echo $index ?>"/>
                    <div style="display: inline-block; width: 457px; padding-left: 8px;">
                        <span style="padding-left: 0px;"><b><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?></b></span>
                        <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                        <div class="right-arrow"></div>
                        <?php print_bookmark_title($bookmark['title']); ?> 
                    </div>
                </li>
            <?php
        } ?>
        </ul><br/>
        <a href="#" onclick="document.export_bookmark_form.submit(); close_popup();
                return false;" id="export_button" class="simple-button blue" title="®Export_selected_bookmarks®">®Export®</a>
        <a class="close-reveal-modal-button" href="javascript:close_popup();">®Cancel®</a>
    </form>
<?php
    } else {
        ?>
    ®No_bookmarks®
<?php
    } ?>
