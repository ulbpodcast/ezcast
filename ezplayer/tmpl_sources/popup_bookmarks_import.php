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

<?php include_once 'lib_print.php'; ?>

<a class="close-reveal-modal" href="javascript:close_popup();">&#215;</a>
<h3>®Available_bookmarks®</h3><br/>
<p>®Select_imported_bookmarks®</p>
<br/>
<?php if (isset($imported_bookmarks) && count($imported_bookmarks) > 0) {
    ?>
    <form action="index.php?action=bookmarks_import" method="post" id="select_import_bookmark_form" name="import_bookmark_form" onsubmit="return false">
        <input type="hidden" name="imported_bookmarks" value="<?php echo htmlspecialchars(json_encode($imported_bookmarks)); ?>"
               <input type="hidden" name="album" id="import_album" value="<?php echo $album; ?>"/>
        <input type="hidden" name="asset" id="import_asset" value="<?php echo $asset; ?>"/><br/>
        <input type="hidden" name="target" id="import_target" value="<?php echo $_SESSION['target']; ?>" />
        <ul>
            <li style="border-bottom: solid 1px #cccccc;"><input type="checkbox" onclick="toggle_checkboxes(this, 'import_selection[]')" name="check_all"/><span class="blue-title"><b>®Date®</b></span><span class="blue-title"><b>®Bookmark®</b></span></li>

            <?php
            $count = 0;
    $source = (isset($asset) && ($asset != '')) ? 'details' : 'assets';
    foreach ($imported_bookmarks as $index => $bookmark) {
        // only display bookmarks that are related to the selected album and/or asset
        if ($bookmark['album'] == $album && (!isset($asset) || ($asset == '') || $bookmark['asset'] == $asset)) {
            ++$count; ?>
                    <li>
                        <input style="float: left;" type="checkbox" name="import_selection[]" value="<?php echo $index ?>"/>
                        <?php if (($_SESSION['target'] == 'custom' && user_prefs_asset_bookmark_exists($_SESSION['user_login'], $bookmark['album'], $bookmark['asset'], $bookmark['timecode']))
                                || ($_SESSION['target'] == 'official' && toc_asset_bookmark_exists($bookmark['album'], $bookmark['asset'], $bookmark['timecode']))) {
                ?>

                            <div style="display: inline-block; width: 457px; padding-left: 8px; color:#ff0000;">
                                <a class="tooltip"><span style="padding-left: 0px;"><b><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?></b></span>
                                    <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                    <div class="right-arrow"></div>
                                        <?php print_bookmark_title($bookmark['title']); ?> 

                                    <div class="tip"><b class="green-title">®Replace® :</b>
                                        <?php
                                        if ($_SESSION['target'] == 'official') {
                                            $personal_bookmark = toc_asset_bookmark_get($bookmark['album'], $bookmark['asset'], $bookmark['timecode']);
                                        } else {
                                            $personal_bookmark = user_prefs_asset_bookmark_get($_SESSION['user_login'], $bookmark['album'], $bookmark['asset'], $bookmark['timecode']);
                                        } ?>
                                        <div style="padding: 5px 0px;"><?php print_bookmark_title($personal_bookmark['title']); ?> (<?php print_time($personal_bookmark['timecode']); ?>)</div>
                                        <div style="border-top:1px dotted #cccccc; width: 400px; padding-top: 8px">
                                            <b>®Description®: </b><?php print_info($personal_bookmark['description']); ?><br/>
                                            <b>®Keywords®: </b><?php print_info($personal_bookmark['keywords']); ?>
                                        </div>
                                    </div></a>
                            </div>
                            <?php
            } else {
                ?>
                            <div style="display: inline-block; width: 457px; padding-left: 8px;">
                                <span style="padding-left: 0px;"><b><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?></b></span>
                            <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                <div class="right-arrow"></div>
                        <?php print_bookmark_title($bookmark['title']); ?> 
                            </div>
                    <?php
            } ?>
                    </li>
        <?php
        }
    }
    if ($count == 0) {
        ?>
                <br/>
                ®No_eligible_bookmarks®
    <?php
    } ?>
        </ul><br/>
        <a href="#" onclick="bookmarks_import_form_submit('<?php echo $source; ?>');" id="import_button" class="simple-button blue" title="®Import_selected_bookmarks®">®Import®</a>
        <a class="close-reveal-modal-button" href="javascript:close_popup();">®Cancel®</a>
    </form>
<?php
} else {
        ?>
    ®No_bookmarks®
<?php
    } ?>

