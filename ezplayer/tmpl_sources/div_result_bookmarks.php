<b class="blue-title" style="font-size: 14px" onclick="$('#bookmarks_result').toggle();">®Personal® </b>
<div id ="bookmarks_result">
<ul class="search_result">
    <?php
    $album_ref = '';
    $asset_ref = '';
    foreach ($bookmarks as $index => $bookmark) {
        if ($album_ref != $bookmark['album']) {
            ?>
            <li class="album_result">(<?php echo suffix_remove($bookmark['album']); ?>) <?php echo get_album_title($bookmark['album']); ?></li>
        <?php
        } ?>
        <?php if ($asset_ref != $bookmark['asset']) {
            ?>
            <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?></li>
        <?php
        } ?>
        <li id="result_<?php echo $index; ?>" class="result">

            <span class="more"><a class="more-button blue" onclick="bookmark_more_toggle('<?php echo $index; ?>', 'result', $(this));"></a></span>

            <a class="result_item" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>')">
                <span class="timecode">(<?php print_time($bookmark['timecode']); ?>) </span><?php print_bookmark_title($bookmark['title']); ?>
            </a>
            <div class="result_detail" id="result_detail_<?php echo $index; ?>">
                <div class="bookmark_info">
                    <div class="blue-title">®Description® :</div>
                    <?php print_info($bookmark['description']); ?>
                    <div class="blue-title" style="margin-top: 6px;">®Keywords® : </div>
                    <?php print_info($bookmark['keywords']); ?>
                </div>
            </div>
        </li>
        <?php
        $album_ref = $bookmark['album'];
        $asset_ref = $bookmark['asset'];
    }
    ?>
</ul>
</div>