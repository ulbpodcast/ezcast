<b class="green2-title" style="font-size: 14px" onclick="$('#threads_result').toggle();">®Discussions®</b>
<div id="threads_result">
    <ul class="search_result">
        <?php
        $album_ref = '';
        $asset_ref = '';

        foreach ($search_result_threads as $album => $album_threads) {
            if ($album_ref != $album) {
                ?>
                <li class="album_result">(<?php echo suffix_remove($album); ?>) <?php echo get_album_title($album); ?></li>
                <?php
            }

            $token = acl_token_get($album);
            $token = $token['token'];

            foreach ($album_threads as $asset => $asset_threads) {
                ?>
                <?php if ($asset_ref != $asset) {
                    ?>
                    <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($asset, '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($album, $asset); ?></li>

                    <?php
                }

                foreach ($asset_threads as $thread_id => $thread) {
                    ?>
                    <li id="result_thread_<?php echo $thread_id; ?>" class="result">

                        <span class="more"><a class="more-button green2" onclick="bookmark_more_toggle('<?php echo $thread_id; ?>', 'result_thread', $(this));"></a></span>

                        <a class="result_item" onclick="show_thread('<?php echo $album; ?>', '<?php echo $asset; ?>', <?php echo $thread['timecode']; ?>, '<?php echo $thread_id; ?>', '')">
                            <span class="timecode green2">(<?php print_time($thread['timecode']); ?>) </span><?php echo $thread['title']; ?>
                        </a>
                        <div class="result_detail" id="result_thread_detail_<?php echo $thread_id; ?>">
                            <div class="bookmark_info thread" onclick="show_thread('<?php echo $album; ?>', '<?php echo $asset; ?>', <?php echo $thread['timecode']; ?>, '<?php echo $thread_id; ?>', '')">
                                <?php echo nl2br(html_entity_decode($thread['message'])); ?>  
                            </div>
                            <?php foreach ($thread['comments'] as $comment_id => $comment) {
                        ?>
                                <div class="bookmark_info comment" onclick="show_thread('<?php echo $album; ?>', '<?php echo $asset; ?>', <?php echo $thread['timecode']; ?>, '<?php echo $thread_id; ?>', '<?php echo $comment_id; ?>')">
                                    <?php echo nl2br(html_entity_decode($comment)); ?>  
                                </div>
                            <?php
                    } ?>
                        </div>
                    </li>
                    <?php
                }
                $asset_ref = $asset;
            }
            $album_ref = $album;
        }
        ?>
    </ul>
</div>