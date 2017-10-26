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

include_once 'lib_print.php';
?>
<!-- #side_menu
    Contains the buttons used to navigate through the pages.
    #back_button : displayed on asset page and used to return to the current album
    #home_button : used to return to the home page
-->

<script>
    $('.bookmarks_button, .toc_button').localScroll({
        target: '#side_pane',
        axis: 'x'
    });

    history.pushState({"url": 'index.php?action=view_album_assets&album=' + '<?php echo $_SESSION['album']; ?>' + '&token=' + '<?php echo $_SESSION['token']; ?>'}, '', '');

<?php if (!acl_user_is_logged() || ((!isset($personal_bookmarks) || sizeof($personal_bookmarks) == 0) && (isset($official_bookmarks)
        && sizeof($official_bookmarks) != 0))) {
    ?>
        current_tab = 'toc';
<?php
} ?>

    if (current_tab == 'toc') {
        setActivePane('.toc_button');
        $('#side_pane').scrollTo('#album_toc');
    } else {
        setActivePane('.bookmarks_button');
        $('#side_pane').scrollTo('#album_bookmarks');
    }
    lvl = 2;
    ezplayer_mode = '<?php echo $_SESSION['ezplayer_mode']; ?>';
</script>

<div id="search">
    <?php include_once template_getpath('div_search.php'); ?>
</div>

<div id="side_menu">

    <?php if (acl_user_is_logged()) {
        ?>
        <div class="bookmarks_button active dir"><a onclick="setActivePane('.bookmarks_button');
            server_trace(new Array('2', 'bookmarks_swap', current_album, current_asset, current_tab));" 
            href="#album_bookmarks" title="®Display_bookmarks®"></a></div>
            <?php
    }
        ?>
    <div class="toc_button dir">
        <a onclick="setActivePane('.toc_button');
            server_trace(new Array('2', 'bookmarks_swap', current_album, current_asset, current_tab));" href="#album_toc" 
            title="®Display_toc®">
        </a>
    </div>
    <div class="settings bookmarks"> 
        <a class="menu-button" title="®Bookmarks_actions®" onclick="$(this).toggleClass('active')" 
           href="javascript:toggle('#bookmarks_actions');">
        </a>
        <a class="sort-button <?php echo acl_value_get("personal_bm_order"); ?>" 
           title="®Reverse_bookmarks_order®" href="javascript:bookmarks_sort('personal', '<?php echo (acl_value_get("personal_bm_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'assets');">
        </a>
        <ul id="bookmarks_actions">
            <li>
                <a href="javascript:popup_bookmarks('<?php echo $album; ?>', '', 'custom', 'assets', 'export')" 
                   title="®Export_album_bookmarks®">
                    ®Export_bookmarks®
                </a>
            </li>  
            <li>
                <a href="javascript:popup_bookmarks_import();" title="®Import_album_bookmarks®">
                    ®Import_bookmarks®
                </a>
            </li>
            <li>
                <a href="javascript:popup_bookmarks('<?php echo $album; ?>', '', 'custom', 'assets', 'delete')" 
                   title="®Delete_album_bookmarks®">
                    ®Delete_bookmarks®
                </a>
            </li>                          
        </ul>
    </div>    
    <div class="settings toc">
        <a class="menu-button" title="®Toc_actions®" onclick="$(this).toggleClass('active')" href="javascript:toggle('#tocs_actions');">
        </a>
        <a class="sort-button <?php echo acl_value_get("official_bm_order"); ?>" title="®Reverse_bookmarks_order®" 
           href="javascript:bookmarks_sort('official', '<?php echo (acl_value_get("official_bm_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'assets');">
        </a>
        <ul id="tocs_actions">
            <li>
                <a href="javascript:popup_bookmarks('<?php echo $album; ?>', '', 'official', 'assets', 'export')" 
                   title="®Export_album_bookmarks®">
                    ®Export_bookmarks®
                </a>
            </li>  
            <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) {
            ?>
                <li>
                    <a href="javascript:popup_bookmarks_import();" title="®Import_album_bookmarks®">
                        ®Import_bookmarks®
                    </a>
                </li>
                <li>
                    <a href="javascript:popup_bookmarks('<?php echo $album; ?>', '', 'official', 'assets', 'delete')" 
                       title="®Delete_album_bookmarks®">
                        ®Delete_bookmarks®
                    </a>
                </li>
            <?php
        } ?>
        </ul>
    </div>
</div>

<!-- #side_pane
the pane displays the list of all assets contained in the selected album
-->
<div id="side_pane">
    <div id="side-pane-scroll-area">
        <?php if (acl_user_is_logged()) {
            ?>
            <div class="side_pane_content" id="album_bookmarks">
                <div class="side_pane_up"><a href="javascript:bookmarks_scroll('down','.bookmark_scroll');"></a></div>
                <?php if (!isset($personal_bookmarks) || sizeof($personal_bookmarks) == 0) {
                ?>
                    <div class="no_content">®No_bookmarks®</div>
                    <?php
            } else {
                ?>
                    <ul class="bookmark_scroll">
                        <?php
                        foreach ($personal_bookmarks as $index => $bookmark) {
                            ?>
                            <li id="bookmark_<?php echo $index; ?>" class="blue level_<?php echo $bookmark['level']; ?>">

                                <a class="item blue" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>')">                                    
                                    <?php print_info(substr(get_user_friendly_date( $bookmark['asset'],'/', false, get_lang(), false), 0, 10)); ?> 
                                    <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                    <br/><b><?php print_bookmark_title($bookmark['title']); ?></b>
                                </a>
                                <span class="more"><a class="more-button" onclick="bookmark_more_toggle('<?php echo $index; ?>', 'bookmark', $(this));"></a></span>
                                <div class="bookmark_detail" id="bookmark_detail_<?php echo $index; ?>">
                                    <div class="bookmark_info">
                                        <div class="blue-title">®Description® :</div>
                                        <?php print_info($bookmark['description']); ?>
                                        <div class="blue-title" style="margin-top: 6px;">®Keywords® : </div>
                                        <?php print_search($bookmark['keywords']); ?>
                                    </div>
                                    <div class="bookmark_options">
                                        <a class="delete-button" title="®Delete_bookmark®" href="javascript:popup_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', 'custom', 'assets', 'remove')"></a>
                                    </div>
                                </div>
                            </li>
                            <?php
                        } ?>
                    </ul>
                <?php
            } ?>
                <div class="side_pane_down"><a href="javascript:bookmarks_scroll('up','.bookmark_scroll');"></a></div>
            </div>
        <?php
        } ?>
        <div class="side_pane_content" id="album_toc">
            <div class="side_pane_up"><a href="javascript:bookmarks_scroll('down','.toc_scroll');"></a></div>
            <?php if (!isset($official_bookmarks) || sizeof($official_bookmarks) == 0) {
            ?>
                <div class="no_content">®No_toc®</div>
                <?php
        } else {
            ?>
                <ul class="toc_scroll">
                    <?php
                    foreach ($official_bookmarks as $index => $bookmark) {
                        ?>
                        <li id="toc_<?php echo $index; ?>" class="orange level_<?php echo $bookmark['level']; ?>">

                            <a class="item orange" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>')">
                                <?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> 
                                    <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                <br/><b><?php print_bookmark_title($bookmark['title']); ?></b>
                            </a>
                            <span class="more">
                                <a class="more-button orange" onclick="bookmark_more_toggle('<?php echo $index; ?>', 'toc', $(this));">
                                </a>
                            </span>
                            <div class="bookmark_detail" id="toc_detail_<?php echo $index; ?>">
                                <div class="bookmark_info">
                                    <div class="orange-title">®Description® :</div>
                                    <?php print_info($bookmark['description']); ?>
                                    <div class="orange-title" style="margin-top: 6px;">®Keywords® : </div>
                                    <?php print_search($bookmark['keywords']); ?>
                                </div>
                                <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) {
                            ?>
                                    <div class="bookmark_options">
                                        <a class="delete-button" title="®Delete_bookmark®" 
                                           href="javascript:popup_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', 'official', 'assets', 'remove')">
                                        </a>
                                    </div>
                                <?php
                        } ?>
                            </div>
                        </li>
                        <?php
                    } ?>
                </ul>
            <?php
        }
            ?>
            <div class="side_pane_down"><a href="javascript:bookmarks_scroll('up','.toc_scroll');"></a></div>
        </div>
    </div>
</div>