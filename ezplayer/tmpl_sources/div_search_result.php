<!-- 
 * EZCAST EZplayer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
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
-->

<?php
include_once 'lib_print.php';
?> 
<script>
    var tab = <?php echo json_encode($words);?>;
    $('.result').highlight(tab);
</script>

    <h2><b>®search_results®</b></h2>
    <div class="search_result_wrapper">
<a class="close-reveal-modal" href="javascript:close_popup();">&#215;</a>
    <br/>
<?php if ((!isset($bookmarks) || sizeof($bookmarks) == 0)
        && (!isset($bookmarks_toc) || sizeof($bookmarks_toc) == 0)) {
    ?>
    <div class="no_content">®no_result®</div>
    <?php
} else {
    if (isset($bookmarks) && sizeof($bookmarks) > 0){
    ?>
    <b class="blue-title" style="font-size: 14px">®Personal® : </b>
    <ul class="search_result">
        <?php
        $album_ref = $bookmarks[0]['album'];
        $asset_ref = $bookmarks[0]['asset']; ?>
        <li class="album_result"><h4>(<?php echo suffix_remove($bookmarks[0]['album']); ?>) <?php echo get_album_title($bookmarks[0]['album']); ?></h4></li>
        <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($bookmarks[0]['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmarks[0]['album'], $bookmarks[0]['asset']); ?></li>
        <?php foreach ($bookmarks as $index => $bookmark) {
            if ($album_ref != $bookmark['album']){ ?>
        <li class="album_result"><h4>(<?php echo suffix_remove($bookmark['album']); ?>) <?php echo get_album_title($bookmark['album']); ?></h4></li>
        <?php }
            ?>
        <?php 
            if ($asset_ref != $bookmark['asset']){ ?>
        <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?></li>
        <?php }
            ?>
            <li id="result_<?php echo $index; ?>" class="result">

                <span class="more"><a class="more-button blue" onclick="toggle_detail('<?php echo $index; ?>', 'result', $(this));"></a></span>

                <a class="result_item" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>')">
                    <span class="timecode">(<?php print_time($bookmark['timecode']); ?>) </span><?php print_bookmark_title($bookmark['title']); ?>
                </a>
                <div class="result_detail" id="result_detail_<?php echo $index; ?>">
                    <div class="bookmark_info">
                        <b class="blue-title">®Description® :</b>
                        <?php print_info($bookmark['description']); ?>
                        <b class="blue-title" style="margin-top: 6px;">®Keywords® : </b>
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
<?php 
} ?>

<?php if ((isset($bookmarks) && sizeof($bookmarks) > 0)
        && (isset($bookmarks_toc) && sizeof($bookmarks_toc) > 0)) { ?>
    <div style="width:488px; border-top: double #CCCCCC 3px; margin: 16px 0px;"></div>
    <?php } ?>

 <?php   if (isset($bookmarks_toc) && sizeof($bookmarks_toc) > 0){ ?>
    <b class="orange-title" style="font-size: 14px">®Toc® : </b>
    <ul class="search_result">
        <?php
        $album_ref = $bookmarks_toc[0]['album'];
        $asset_ref = $bookmarks_toc[0]['asset']; ?>
        <li class="album_result"><h4>(<?php echo suffix_remove($bookmarks_toc[0]['album']); ?>) <?php echo get_album_title($bookmarks_toc[0]['album']); ?></h4></li>
        <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($bookmarks_toc[0]['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmarks_toc[0]['album'], $bookmarks_toc[0]['asset']); ?></li>
        <?php foreach ($bookmarks_toc as $index => $bookmark) {
            if ($album_ref != $bookmark['album']){ ?>
        <li class="album_result"><h4>(<?php echo suffix_remove($bookmark['album']); ?>) <?php echo get_album_title($bookmark['album']); ?></h4></li>
        <?php }
            ?>
        <?php 
            if ($asset_ref != $bookmark['asset']){ ?>
        <li class="asset_result"><div class="right-arrow"></div><?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?></li>
        <?php }
            ?>
            <li id="result_<?php echo $index; ?>" class="result">
                
                <span class="more"><a class="more-button orange" onclick="toggle_detail('<?php echo $index; ?>', 'result_toc', $(this));"></a></span>

                <a class="result_item orange" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>')">
                    <span class="timecode orange">(<?php print_time($bookmark['timecode']); ?>) </span><?php print_bookmark_title($bookmark['title']); ?>
                </a>
                <div class="result_detail" id="result_toc_detail_<?php echo $index; ?>">
                    <div class="bookmark_info">
                        <b class="orange-title">®Description® :</b>
                        <?php print_info($bookmark['description']); ?>
                        <b class="orange-title" style="margin-top: 6px;">®Keywords® : </b>
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
    <?php }
}
?>
    </div>