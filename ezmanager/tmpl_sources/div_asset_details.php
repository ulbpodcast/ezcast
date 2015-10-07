<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 		    Arnaud Wijns <awijns@ulb.ac.be>
 *                   Antoine Dewilde
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

<!--
Before calling this template, please declare the following variables:
- $asset_name The asset (technical) name
- $title The asset (user-friendly) title
- $description The asset's description
- $public_album Set to true if the album is public, false otherwise (used for the "publish/unpublish option)
- $has_cam Set to true if the asset has a "live action" media
- $has_slides Set to true if the asset has a "slides" media
- $type Can be "cam", "slide" or "camslide"
- $date The date the asset was created, in a user-friendly format
- $duration The duration of the asset
- $filesize_[cam|slides] An assoc array (indexes 'low' and 'high') with the filesize for both the videos 1 and 2, as a string (including the unit!)
    Example : $filesize_cam['high'] = '640 Mo' means that the live-action video weights 640Mo in HD
- $dimensions_[cam|slides] An assoc array with the dimensions of the video, as a string
    Example : $dimensions_cam['high'] = '1024 x 768' means the video in the left column in high-res is in 1024*768
- $urls An assoc array (indexes 'low' and 'high') with the URL to the asset (web_distribute.php)
- $embeds An assoc array with embed codes for the assets
-->
<?php

/**
 * Helper function, used for pretty print.
 * @param type $info 
 */
function print_info($info, $suffix = '') {
    if (isset($info) && $info !== '')
        echo htmlspecialchars($info) . $suffix;
    else
        echo '®Not_available®';
}
?>

<?php if (strtolower($origin) !== 'streaming') { ?>
    <div class="LigneBouton">
        <ul>
            <li><span class="BoutonSuppAlbum">
                    <?php if ($status != 'processing' && $status != 'error') {
                        ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_delete_asset_<?php echo $asset_name; ?>');">®Delete®</a>
                        <?php
                    } else {
                        ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_not_available');">®Delete®</a>
                        <?php
                    }
                    ?>

                </span></li>
            <li><span class="BoutonEditer"><a href="javascript:show_edit_form('<?php echo $asset; ?>');">®Edit®</a></span></li>
            <li><span class="BoutonDeplacer">
                    <?php if ($status != 'processing' && $status != 'error') { ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_move_asset_<?php echo $asset_name; ?>');">®Move®</a>
                        <?php
                    } else {
                        ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_not_available');">®Move®</a>
                        <?php
                    }
                    ?>
                </span></li>
            <?php if ($public_album) { ?>
                <li><span class="BoutonDeplacerAlbumPrive">
                        <?php
                        if ($status != 'processing' && $status != 'error') {
                            ?>
                            <a href="javascript:show_popup_from_inner_div('#popup_unpublish_asset_<?php echo $asset_name; ?>');">®Unpublish®</a>
                            <?php
                        } else {
                            ?>
                            <a href="javascript:show_popup_from_inner_div('#popup_not_available');">®Unpublish®</a>
                            <?php
                        }
                        ?>
                    </span></li>
                    <?php } else { ?>
                <li><span class="BoutonDeplacerAlbumPublic">
                <?php if ($status != 'processing' && $status != 'error') { ?>
                            <a href="javascript:show_popup_from_inner_div('#popup_publish_asset_<?php echo $asset_name; ?>');">®Publish®</a>
                            <?php
                        } else {
                            ?>
                            <a href="javascript:show_popup_from_inner_div('#popup_not_available');">®Publish®</a>
                            <?php
                        }
                        ?>
                    </span></li>
                    <?php } ?>            
                    <li><span class="BoutonProgrammer">
                    <?php if ($status != 'processing' && $status != 'error') { ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_schedule_<?php echo $asset_name; ?>');">®Program®</a>
                        <?php
                    } else {
                        ?>
                        <a href="javascript:show_popup_from_inner_div('#popup_not_available');">®Program®</a>
                        <?php
                    }
                    ?>
                </span></li>
        </ul>
    </div>
<?php } ?>

<!-- bouton supp + editer + 2 X Deplacer [Fin]  -->

<!-- Colonne 1 information podcast -->

<div class="Colonne-Un-Information">
    <h1>®Information®</h1>
    <p>
        <span class="infospodast">®Title®&nbsp;:</span> 
        <span id="title_<?php echo $asset; ?>"><?php print_info($title); ?></span>
        <!-- Edit form -->
        <span id="title_<?php echo $asset; ?>_edit" style="display: none;"><input type="text" name="title" id="title_<?php echo $asset; ?>_input" value="<?php echo htmlentities($title, ENT_COMPAT, "UTF-8"); ?>" /></span>
    </p>
    <p>
        <span class="infospodast">®Description®&nbsp;:</span> 
        <span id="description_<?php echo $asset; ?>"><?php print_info($description); ?></span>
        <!-- Edit form -->
        <span id="description_<?php echo $asset; ?>_edit" style="display: none;"><textarea name="description" id="description_<?php echo $asset; ?>_input"><?php echo htmlentities($description, ENT_COMPAT, "UTF-8"); ?></textarea></span>

    </p>
    <p><span class="infospodast">®Author®&nbsp;:</span> <?php print_info($author); ?></p>
    <p><span class="infospodast">®Date®&nbsp;:</span> <?php print_info($date); ?></p>
    <p><span class="infospodast">®Origin®&nbsp;:</span> <?php if ($origin == 'SUBMIT') echo '®Manual_submit®';
else print_info($origin); ?></p>
<?php if ($status == 'processing') {
    ?>
        <p><span class="infospodast">®Status®&nbsp;:</span> <span style="color: #75940a;">®Processing®</span></p>
        <?php
    } else if ($status == 'failed') {
        ?>
        <p><span class="infospodast">®Status®&nbsp;:</span> <span style="color: red;">®Processing_error®</span></p>
        <?php
    } else {
        ?>
        <p><span class="infospodast">®Length®&nbsp;:</span> <?php print_info($duration); ?></p>
        <?php
    }

    if ($origin == 'SUBMIT') {
        ?>
        <p><span class="infospodast">®Filename®&nbsp;:</span> <?php print_info($file_name); ?></p>
        <?php } else if (strtolower($origin) === 'streaming') {
        ?>
        <span class="BoutonEZplayer"> <a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=ezplayer_link&amp;album=<?php echo $album; ?>&amp;asset=<?php echo $asset; ?>');">EZplayer</a> </span>
    <?php }
    ?>
    <!-- Edit form (submit/cancel buttons) -->
    <div id="<?php echo $asset; ?>_submit" style="display: none; width: 200px; text-align: right; padding-right: 10px;">
        <input type="button" id="<?php echo $asset; ?>_cancel_button" onclick="show_edit_form('<?php echo $asset; ?>');" value="®Cancel®" />
        <input type="button" id="<?php echo $asset ?>_submit_button" onclick="edit_asset_data('<?php echo $album; ?>', '<?php echo $asset; ?>');" value="®Update®" />
    </div>
</div>

<!-- Colonne 1 information podcast [Fin] -->

<!-- Colonne 2 information podcast -->

<?php
// If there were two media, we spread them in two columns.
// This is the first one (the second one is below), i.e. the video
if ($has_cam && $has_slides && $status != 'processing' && $status != 'failed' && strtolower($origin) !== 'streaming') {
    ?>
    <div class="Colonne-Deux-Video">
    <?php require 'div_media_details_camera.php'; ?>
    </div>
    <?php
} // Fin colonne 2
?>


<!-- Colonne 3 information podcast -->

<?php
// If there is only one media, we display it in the right column.
// 3 possibles scenarios: there was only a slides video, or there were 2 videos
//    That happens if $has_slides is true (inside the "if")
// There was only a cam video. In that case $has_slides is false, so the content of the "else" is displayed
if ($status != 'processing' && $status != 'failed' && strtolower($origin) !== 'streaming') {
    ?>
    <div class="Colonne-Trois-Diaporama">                                
    <?php
    if ($has_slides) {
        require 'div_media_details_slides.php';
    } else {
        require 'div_media_details_camera.php';
    }
    ?>
    </div><!-- fin colonne trois-->    
        <?php }
    ?>
<div style="clear: both;"></div>
<!-- Popup -->
<div style="display: none;">
<?php include 'popup_delete_asset.php'; ?>
<?php include 'popup_move_asset.php'; ?>
<?php include 'popup_publish_asset.php'; ?>
    <?php include 'popup_unpublish_asset.php'; ?>
    <?php include 'popup_schedule.php' ?>
    <?php include 'popup_media_url.php' ?>
    <?php include_once 'popup_not_available_while_processing.php'; ?>
</div>