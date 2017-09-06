
<div class="asset_info">
    <b class="asset_title"><?php print_info(substr(get_user_friendly_date($asset_meta['record_date'], '/', false, get_lang(), false), 0, 10)); ?></b>
    <div class="right-arrow"></div>
    <div class="asset_details_header">
        <div class="asset_title">
            <?php print_info($asset_meta['title']); ?>
        </div>
        <div class="asset_author">{ <?php print_info($asset_meta['author']); ?> }</div>
    </div>
    <div class="download-button">

            <?php
            if (!isset($asset_meta['downloadable']) || $asset_meta['downloadable'] !== 'false') {
                if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'cam') {
                    ?>
                    <a class="button <?php echo $asset_meta['record_type'] == 'camslide' ? 'left-side' : ''?>" 
                       href="javascript:popup_asset(current_album, current_asset, time, 'cam', 'share_link')" 
                       onclick="server_trace(new Array('3', 'cam_download_open', current_album, current_asset, duration, time, type, quality));">
                        ®Download_movie®
                    </a>
                <?php
                }
                if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'slide') {
                    ?>
                    <a class="button <?php echo $asset_meta['record_type'] == 'camslide' ? 'right-side' : ''?>" 
                       href="javascript:popup_asset(current_album, current_asset, time, 'slide', 'share_link')" 
                       onclick="server_trace(new Array('3', 'slide_download_open', current_album, current_asset, duration, time, type, quality));">
                           <?php echo $asset_meta['record_type'] == 'camslide' ? '®The_slides®' : '®Download_slide®'?>
                    </a>
                <?php
                }
            }
            ?>

    </div>
    <?php if (isset($asset_meta['description']) && !empty($asset_meta['description'])) {
                ?>
        <div class="asset_details">
            <b class="green-title">®Description®:</b>
            <?php print_info($asset_meta['description']); ?>
        </div>
    <?php
            } ?>

</div>