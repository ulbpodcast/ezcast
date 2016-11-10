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
?>

<!--
Before calling this template, please define a $assets array, containing
all the assets for the selected album, and the metadata thereof (ordered in chronological order)
-->
<div id="div_asset_list">
    <div class="BlocPodcastMenu">
        <?php
        require_once 'lib_various.php';
        // Note: we can't use foreach() here because assets are ordered by date, oldest first,
        // and we want the assets to be displayed with the newest first
        if (isset($assets) && !empty($assets)) {
            foreach ($assets as $asset) {
                $asset_name = $asset['name'];
                $metadata = $asset['metadata'];
                // Testing purpose only: if the title does not exist, we replace it by
                // the asset (raw) name
                $title = (isset($metadata['title'])) ? $metadata['title'] : $asset_name;
                $status = $metadata['status'];

                // We want the date to be displayed in format dd-mm-YYYY
                // However, get_user_friendly_date returns dd-mm-YYYY-HH-ii, so we need to remove the last part
                $date = get_user_friendly_date($metadata['record_date'], '-', false);
                $date = substr($date, 0, -6);
                if ($metadata['origin'] === 'streaming'){ ?>
                                    <div>
                  
                    <div id="asset_<?php echo $asset_name; ?>" 
                         class="BoutonTriangleProcessing"> 
                        <a href="javascript:show_asset_details('<?php echo $album_name_full; ?>', '<?php echo $asset_name; ?>');"> 
                            LIVE
                            <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title"> 
                                | <?php echo htmlspecialchars($title); ?>
                            </span> 
                        </a> 
                    </div>
                    <div id="asset_<?php echo $asset_name ?>_clic" 
                         class="BoutonTriangleClicProcessing" style="display:none"> 
                        <a href="javascript:show_asset_details('<?php echo $album_name_full; ?>', '<?php echo $asset_name; ?>');" >
                            LIVE
                            <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title_clic"> 
                                | <?php echo htmlspecialchars($title); ?>
                            </span> 
                        </a>
                    </div>
                    <div id="asset_<?php echo $asset_name; ?>_details" class="asset_details" style="display: none;">
                        <!-- Asset details go here -->
                    </div>
                </div>
                <?php } else {
                ?>
                <div>
                    <input class="custom-checkbox" name="asset_downloadable" id="is_downloadable_<?php echo $asset_name; ?>" title="®Allow_download®" type="checkbox" 
                           onchange="asset_downloadable_set('<?php echo $album_name . (($public_album) ? '-pub' : '-priv'); ?>', '<?php echo $asset_name; ?>')" <?php echo ($metadata['downloadable'] !== 'false') ? 'checked' : '' ?>
                           >                    
                    <label onclick="$('#is_downloadable_<?php echo $asset_name; ?>').click();" title="®Allow_download®" >
                    </label>
                    <?php if(isset($metadata['scheduled']) && $metadata["scheduled"] == true){ ?>
                    <img src="images/page4/sched.png" style="float: right; width: 15px; padding: 3px;" title="<?php echo $metadata['schedule_date']; ?>">
                    <?php } ?>
                    <div id="asset_<?php echo $asset_name; ?>" 
                         class="BoutonTriangle<?php if ($status == 'failed')
                       echo 'Error';
                   else if ($status == 'processing')
                       echo 'Processing';
                           ?>"> 
                        <a href="javascript:show_asset_details('<?php echo $album_name_full; ?>', '<?php echo $asset_name; ?>');"> 
        <?php echo $date; ?> 
                            <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title"> 
                                | <?php echo htmlspecialchars($title); ?>
                            </span> 
                        </a> 
                    </div>
                    <div id="asset_<?php echo $asset_name ?>_clic" 
                         class="BoutonTriangleClic<?php if ($status == 'failed')
            echo 'Error';
        else if ($status == 'processing')
            echo 'Processing';
        ?>" style="display:none"> 
                        <a href="javascript:show_asset_details('<?php echo $album_name_full; ?>', '<?php echo $asset_name; ?>');" >
        <?php echo $date; ?>
                            <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title_clic"> 
                                | <?php echo htmlspecialchars($title); ?>
                            </span> 
                        </a>
                    </div>
                    <div id="asset_<?php echo $asset_name; ?>_details" class="asset_details" style="display: none;">
                        <!-- Asset details go here -->
                    </div>
                </div>
        <?php
        }
    }
}
?>
    </div>
</div>