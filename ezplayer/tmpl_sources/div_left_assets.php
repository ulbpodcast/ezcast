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
<!-- #site_map : contains album title and asset title 
        If the current view is the home page, the site map is empty
        If the current view is the album page, the site map contains album title only
        If the current view is the asset page, the site map contains album title and asset title -->
<div id="site_map">
    <a class="home-link" href="index.php" title="®Back_to_home®">®Home®</a>
    <div class="right-arrow"></div>
        (<?php if (isset($course_code_public) && $course_code_public!="") {
    echo $course_code_public;
} else {
    echo suffix_remove($album);
} ?>) 
        <?php echo get_album_title($album); ?>
</div>


<div id="main_player">

    <div id="assets">
        <div class="title">®Date® <span style="padding-left: 48px;">®Title®</span> 
        </div>
        <?php
        if (!isset($assets_list) || sizeof($assets_list) == 0) {
            ?>
            <div class="no_content">®No_content®</div>
            <?php
        } else {
            ?>
            <ul>
                <?php
                foreach ($assets_list as $index => $asset) {
                    if (isset($asset['metadata']['status']) && $asset['metadata']['status'] == 'processed') {
                        ?>
                        <li>
                            <a class="item" id="asset-<?php echo $asset['name']; ?>" 
                               href="javascript:show_asset_details('<?php echo $album; ?>', '<?php echo
                                $asset['name']; ?>', '<?php echo $asset['token']; ?>');">
                                <b><?php print_info(substr(get_user_friendly_date($asset['metadata']['record_date'],'/',false,get_lang(),false), 0, 10)); ?></b> 
                                <div style="display:inline-block; width: 16px; height:1px;"></div>
                                <?php echo $asset['metadata']['title']; ?>
                                <span class="<?php if (acl_show_notifications() && !acl_is_watched($album,$asset['metadata']['record_date'])) { echo 'new';} ?>" title="®New_video®"></span>
                            </a>       
                        </li>
                        <?php
                    } elseif (isset($asset['metadata']['origin']) && $asset['metadata']['origin'] == 'streaming') {
                        ?>
                        <li>
                            <a class="item" id="asset-<?php echo $asset['name']; ?>" 
                               href="javascript:show_asset_streaming('<?php echo $album; ?>', '<?php echo $asset['name']; ?>', '<?php echo $asset['token']; ?>');">
                                <b style="display: inline-block; width: 56px;">LIVE</b> 
                                <div style="display:inline-block; width: 16px; height:1px;"></div>
                                <?php echo $asset['metadata']['title']; ?>
                                <span class="live_stream" title="®Live_stream®"></span>
                            </a>       
                        </li>
                        <?php
                    }
                } ?>
            </ul>
            <?php
        }
        ?>
    </div>
</div>
