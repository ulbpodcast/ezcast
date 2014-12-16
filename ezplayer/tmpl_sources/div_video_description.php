
<div class="asset_info">
    <div class="asset_title">
        <b><?php print_info(substr(get_user_friendly_date($asset_meta['record_date'], '/', false, get_lang(), false), 0, 10)); ?></b>
        <div class="right-arrow"></div>
        <?php print_info($asset_meta['title']); ?>
    </div>
    <div class="asset_author">{ <?php print_info($asset_meta['author']); ?> }</div>
    <div class="asset_details">
        <b class="green-title">®Description®:</b>
        <?php print_info($asset_meta['description']); ?>
    </div>
    <div>
        <?php
        if (acl_is_downloadable($album, $asset_meta['record_date'])) {
            if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'slide') {
                ?>
                <a class="button" href="#" data-reveal-id="popup_slide_link" onclick="server_trace(new Array('3', 'slide_download_open', current_album, current_asset, duration, time, type, quality));">®Download_slide®</a>
                <?php
            }
            if ($asset_meta['record_type'] == 'camslide' || $asset_meta['record_type'] == 'cam') {
                ?>
                <a class="button" href="#" data-reveal-id="popup_movie_link" onclick="server_trace(new Array('3', 'cam_download_open', current_album, current_asset, duration, time, type, quality));">®Download_movie®</a>
                <?php
            }
        }
        if (acl_has_album_moderation($album) || acl_is_admin()) {
            ?>
            <a class="button" href="#" data-reveal-id="popup_asset_link" onclick="server_trace(new Array('3', 'asset_share_open', current_album, current_asset, duration, time, type, quality));">®Share_asset®</a>
        <?php } ?>
    </div>
</div>