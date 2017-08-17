<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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

// Before calling this template, please declare the following variables:
// - $asset_name The asset (technical) name
// - $title The asset (user-friendly) title
// - $description The asset's description
// - $public_album Set to true if the album is public, false otherwise (used for the "publish/unpublish option)
// - $has_cam Set to true if the asset has a "live action" media
// - $has_slides Set to true if the asset has a "slides" media
// - $type Can be "cam", "slide" or "camslide"
// - $date The date the asset was created, in a user-friendly format
// - $duration The duration of the asset
// - $filesize_[cam|slides] An assoc array (indexes 'low' and 'high') with the filesize for both the videos 1 and 2, as a string (including the unit!)
//     Example : $filesize_cam['high'] = '640 Mo' means that the live-action video weights 640Mo in HD
// - $dimensions_[cam|slides] An assoc array with the dimensions of the video, as a string
//     Example : $dimensions_cam['high'] = '1024 x 768' means the video in the left column in high-res is in 1024*768
// - $urls An assoc array (indexes 'low' and 'high') with the URL to the asset (web_distribute.php)
// - $embeds An assoc array with embed codes for the assets



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
                        echo '<a href="index.php?action=show_popup&amp;popup=delete_asset&amp;title='.$title.
                            '&amp;album='.$album.'&amp;asset='.$asset_name.'" data-remove="false" data-toggle="modal" '.
                            'data-target="#modal">';
                    } else {
                        echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                            ' data-toggle="modal" data-target="#modal">';
                    } ?>
                    ®Delete®</a>

                </span></li>
            <li>
                <span class="BoutonEditer">
                    <a href="javascript:show_edit_form('<?php echo $asset; ?>');">
                        ®Edit®
                    </a>
                </span>
            </li>
            <li><span class="BoutonDeplacer">
                    <?php if ($status != 'processing' && $status != 'error') {
                        echo '<a href="index.php?action=show_popup&amp;popup=move_asset&amp;album='.$album.
                            '&amp;asset='.$asset_name.'" data-remove="false" data-toggle="modal" '.
                            'data-target="#modal">';
                    } else {
                        echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                            ' data-toggle="modal" data-target="#modal">';
                    } ?>
                    ®Move®</a>
                </span></li>
				
				<?php 
				global $enable_copy_asset;
					if($enable_copy_asset){?>
						<li><span class="BoutonCopier">
							<?php if ($status != 'processing' && $status != 'error') { ?>
								<a href="javascript:show_popup_from_inner_div('#popup_copy_asset_<?php echo $asset_name; ?>');">®copy®</a>
								<?php
							} else {
								?>
								<a href="javascript:show_popup_from_inner_div('#popup_not_available');">®copy®</a>
								<?php
							}
							?>
							</span></li>
				<?php } ?>
				
            <?php if ($public_album) { ?>
                <li>
                    <span class="BoutonDeplacerAlbumPrive">
                        <?php if ($status != 'processing' && $status != 'error') {
                            echo '<a href="index.php?action=show_popup&amp;popup=unpublish_asset&amp;title='.$title.
                                '&amp;album='.$album.'&amp;asset='.$asset_name.'" data-remove="false" data-toggle="modal" '.
                                'data-target="#modal">';
                        } else {
                            echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                            ' data-toggle="modal" data-target="#modal">';
                        } ?>
                        ®Unpublish®</a>
                    </span>
                </li>
            <?php } else { ?>
                <li>
                    <span class="BoutonDeplacerAlbumPublic">
                        <?php if ($status != 'processing' && $status != 'error') {
                            echo '<a href="index.php?action=show_popup&amp;popup=publish_asset&amp;title='.$title.
                                '&amp;album='.$album.'&amp;asset='.$asset_name.'" data-remove="false" data-toggle="modal" '.
                                'data-target="#modal">';
                        } else {
                            echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                                ' data-toggle="modal" data-target="#modal">';
                        } ?>
                        ®Publish®</a>
                    </span>
                </li>
            <?php } ?>
            <li>
                <span class="BoutonProgrammer">
                    <?php if ($status != 'processing' && $status != 'error') {
                        echo '<a href="index.php?action=show_popup&amp;popup=schedule_asset&amp;album='.$album.
                            '&amp;asset='.$asset_name.'" data-remove="false" data-toggle="modal" '.
                            'data-target="#modal">';
                    } else {
                        echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                        ' data-toggle="modal" data-target="#modal">';
                    } ?>
                    ®Program®</a>
                </span>
            </li> 
            <?php 
            // add un flag title_up_to_date in metadata. get this info. If not show the button => do that in controller, not tmpl
            global $update_title;
            if ($update_title == 'manual' && isset($asset_metadata['add_title'])) { ?>
                <li>
                    <span class="BoutonRegenTitle">
                        <?php if($status != 'processing' && $status != 'error') {
                            echo '<a href="index.php?action=show_popup&amp;popup=regen_title&amp;album='.$album.
                            '&amp;asset='.$asset_name.'&amp;title='.$title.'" data-remove="false" data-toggle="modal" '.
                            'data-target="#modal">';
                        } else {
                            echo '<a href="index.php?action=show_popup&amp;popup=popup_not_available" data-remove="false"' .
                            ' data-toggle="modal" data-target="#modal">';
                        } ?>
                        ®Regen_Intro®</a>
                    </span>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<!-- bouton supp + editer + 2 X Deplacer [Fin]  -->

<!-- Colonne 1 information podcast -->

<div class="col-sm-4" style="padding-left: 0px;">
    <h1>®Information®</h1>
    <ul class="list-unstyled">
        <li>
            <span class="infospodast">®Title®</span><br />
            <span id="title_<?php echo $asset; ?>"><?php print_info($title); ?></span>
            <span id="title_<?php echo $asset; ?>_edit" style="display: none;">
                <input type="text" name="title" id="title_<?php echo $asset; ?>_input" class="form-control input-sm"
                       value="<?php echo htmlentities($title, ENT_COMPAT, "UTF-8"); ?>" />
            </span>
        </li>
        <li>
            <span class="infospodast">®Description®</span><br />
            <span id="description_<?php echo $asset; ?>"><?php print_info($description); ?></span>
            <span id="description_<?php echo $asset; ?>_edit" style="display: none;">
                <textarea name="description" class="form-control input-sm" style="resize: vertical;"
                          id="description_<?php echo $asset; ?>_input"><?php echo htmlentities($description, ENT_COMPAT, "UTF-8"); 
                ?></textarea>
            </span>
        </li>
        <li>
            <span class="infospodast">®Author®</span><br />
            <?php print_info($author); ?>
        </li>
        <li>
            <span class="infospodast">®Date®</span><br />
            <?php print_info($date); ?>
        </li>
        <li>
            <span class="infospodast">®Origin®</span><br />
            <?php if ($origin == 'SUBMIT') { echo '®Manual_submit®'; } else { print_info($origin); } ?>
        </li>

        <?php if ($status == 'processing') { ?>
            <li>
                <span class="infospodast">®Status®</span><br />
                <span style="color: #75940a;">®Processing®</span>
            </li>
        <?php } else if ($status == 'failed') { ?>
            <li>
                <span class="infospodast">®Status®</span><br />
                <span style="color: red;">®Processing_error®</span>
            </li>
        <?php } else { ?>
            <li>
                <span class="infospodast">®Length®</span><br />
                <?php print_info($duration); ?>
            </li>
            <li>
                <span class="infospodast">®Type®</span><br /> 
                <?php print_info($record_type); ?>
            </li>
        <?php }

        if ($origin == 'SUBMIT') { ?>
            <li>
                <span class="infospodast">®Filename®</span><br />
                <?php print_info($file_name); ?>
            </li>
        <?php } else if (strtolower($origin) === 'streaming') { ?>
            <li>
                <span class="BoutonEZplayer"> 
                    <a href="index.php?action=show_popup&amp;popup=ezplayer_link&amp;album=<?php echo $album; ?>&amp;asset=<?php 
                        echo $asset; ?>" 
                    data-remote="false" data-toggle="modal" data-target="#modal">
                    EZplayer
                    </a>
                </span>
            </li>
        <?php } ?>
    </ul>
    <!-- Edit form (submit/cancel buttons) -->
    <div class="btn-group" role="group" id="<?php echo $asset; ?>_submit" style="display: none;">
        <input type="button" class="btn btn-default btn-xs" id="<?php echo $asset; ?>_cancel_button" 
            onclick="show_edit_form('<?php echo $asset; ?>');" value="®Cancel®" />
        <input type="button" class="btn btn-default btn-xs" id="<?php echo $asset ?>_submit_button" 
            onclick="edit_asset_data('<?php echo $album; ?>', '<?php echo $asset; ?>');" value="®Update®" />
    </div>
    <br />
    <br />
</div>

<!-- Colonne 1 information podcast [Fin] -->

<!-- Colonne 2 information podcast -->
<div class="col-sm-4">

<?php
// If there were two media, we spread them in two columns.
// This is the first one (the second one is below), i.e. the video
if ($has_cam && $has_slides && $status != 'processing' && $status != 'failed' && strtolower($origin) !== 'streaming') {
    require 'div_media_details_camera.php';
} // Fin colonne 2
?>
</div>

<!-- Colonne 3 information podcast -->

<div class="col-sm-4">
<?php
// If there is only one media, we display it in the right column.
// 3 possibles scenarios: there was only a slides video, or there were 2 videos
//    That happens if $has_slides is true (inside the "if")
// There was only a cam video. In that case $has_slides is false, so the content of the "else" is displayed
if ($status != 'processing' && $status != 'failed' && strtolower($origin) !== 'streaming') {
    if ($has_slides) {
        require 'div_media_details_slides.php';
    } else {
        require 'div_media_details_camera.php';
    }
} ?>
</div>

<div style="clear: both;"></div>
