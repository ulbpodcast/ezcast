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
                $status = (isset($metadata['status'])) ? $metadata['status'] : '';

                // We want the date to be displayed in format dd-mm-YYYY
                // However, get_user_friendly_date returns dd-mm-YYYY-HH-ii, so we need to remove the last part
                $date = get_user_friendly_date($metadata['record_date'], '-', false);
                $date = substr($date, 0, -6);
                if (isset($metadata['origin']) && $metadata['origin'] === 'streaming') {
                    ?>
                    <div>
                        <div id="asset_<?php echo $asset_name; ?>" class="ListButon ButtonTriangleProcessing"> 
                            <a href="javascript:show_asset_details('<?php echo $album_name_full . "', '" . $asset_name; ?>');"> 
                                LIVE
                                <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title"> 
                                    | <?php echo htmlspecialchars($title); ?>
                                </span> 
                            </a> 
                        </div>
                        <div id="asset_<?php echo $asset_name ?>_clic" class="ListButon ButtonTriangleClicProcessing" style="display:none"> 
                            <a href="javascript:show_asset_details('<?php echo $album_name_full . "', '" . $asset_name; ?>');" >
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
                <?php
                } else {
                    ?>
                    <div>
                        <button role="button" id="is_downloadable_<?php echo $asset_name; ?>" 
                            title="®Allow_download®" class="btn btn-xs download_small_button <?php 
                                echo (!isset($metadata['downloadable']) || $metadata['downloadable'] !== 'false') ? "btn-success" : "btn-danger"; ?>"
                            onclick="update_download('<?php echo $album_name . (($public_album) ? '-pub' : '-priv') . "', '" .
                                $asset_name; ?>')">
                            <?php if (!isset($metadata['downloadable']) || $metadata['downloadable'] !== 'false') {
                                    echo "®Download_allowed®";
                                } else {
                                    echo "®Download_forbidden®";
                                } ?>
                        </button>
                        <?php if (isset($metadata['scheduled']) && $metadata["scheduled"] == true) {
                                    ?>
                            <img src="images/page4/sched.png" style="float: right; width: 24px; padding: 3px;" 
                                title="<?php echo $metadata['schedule_date']; ?>">
                        <?php
                                } ?>
                        <div id="asset_<?php echo $asset_name; ?>_line"  class="ListButon StatusButton StatusButton<?php 
                            if ($status == 'failed') {
                                echo 'Error';
                            } elseif ($status == 'processing') {
                                echo 'Processing';
                            } ?>">
                            <a href="javascript:show_asset_details('<?php echo $album_name_full . "', '" . $asset_name; ?>');"
                               <?php if ($status == 'failed') {
                                echo 'style="color: #d9534f;" ';
                            } elseif ($status == 'processing') {
                                echo 'style="color: #5cb85c;" ';
                            } ?> > 
                                <span 
                                    <?php if ($status == 'failed') {
                                echo 'class="glyphicon glyphicon-warning-sign" ';
                            } elseif ($status == 'processing') {
                                echo 'class="glyphicon glyphicon-refresh" ';
                            } else {
                                echo 'class="glyphicon glyphicon-triangle glyphicon-triangle-right" ';
                            } ?>
                                    id="asset_<?php echo $asset_name; ?>_glyphicon" 
                                    aria-hidden="true"></span>
                                <?php echo $date; ?> 
                                <span class="TitrePodcast" id="asset_<?php echo $asset_name; ?>_title"> 
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
            } // Foreach
        } // If asset?>
    </div>
</div>

<script>
    function update_download(album, asset) {
        var button = $('.download_small_button#is_downloadable_' + asset);
        if(button.hasClass('btn-success')) {
            button.removeClass('btn-success');
            button.addClass('btn-danger');
            button.text("®Download_forbidden®");
        } else {
            button.addClass('btn-success');
            button.removeClass('btn-danger');
            button.text("®Download_allowed®");
        }
        
        console.log('asset_downloadable_set ' + album + ', ' + asset);
        asset_downloadable_set(album, asset);
    }
</script>
