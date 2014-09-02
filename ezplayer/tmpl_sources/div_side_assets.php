<?php
/*
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
 */
?>

<?php include_once 'lib_print.php'; ?>
<!-- #side_menu
    Contains the buttons used to navigate through the pages.
    #back_button : displayed on asset page and used to return to the current album
    #home_button : used to return to the home page
-->
<script>
    $('#assets_button, .bookmarks_button, .toc_button').localScroll({
        target:'#side_pane',
        axis: 'x'
    });
        
    <?php if (!acl_user_is_logged()){ ?>
        current_tab = 'toc';
    <?php } ?>
        
    if (current_tab == 'toc'){
        setActivePane('.toc_button');
        $('#side_pane').scrollTo('#album_toc');
    } else {
        setActivePane('.bookmarks_button');
        $('#side_pane').scrollTo('#album_bookmarks');
    }
    lvl = 2;
</script>

<div id="search">
    <?php include_once template_getpath('div_search.php'); ?>
</div>

<div id="side_menu">

    <?php if (acl_user_is_logged()) { ?>
        <div class="bookmarks_button active dir"><a onclick="setActivePane('.bookmarks_button')" href="#album_bookmarks" title="®Display_bookmarks®"></a></div>
        <?php
    }
    ?>
    <div class="toc_button dir"><a onclick="setActivePane('.toc_button');" href="#album_toc" title="®Display_toc®"></a></div>
    <div class="settings bookmarks"> 
        <a class="menu-button" title="®Bookmarks_actions®" onclick="$(this).toggleClass('active')" href="javascript:toggle('#bookmarks_actions');"></a>
        <a class="sort-button <?php echo acl_value_get("bookmarks_order"); ?>" title="®Reverse_bookmarks_order®" href="javascript:sort_bookmarks('bookmarks', '<?php echo (acl_value_get("bookmarks_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'assets');"></a>
        <ul id="bookmarks_actions">
            <li><a href="#" data-reveal-id="popup_export_bookmarks" title="®Export_album_bookmarks®">®Export_bookmarks®</a></li>  
            <li><a href="#" data-reveal-id="popup_import_bookmarks" onclick="makeRequest('index.php', '?action=view_import', 'popup_import_bookmarks');" title="®Import_album_bookmarks®">®Import_bookmarks®</a></li>
            <li><a href="#" data-reveal-id="popup_delete_bookmarks" title="®Delete_album_bookmarks®">®Delete_bookmarks®</a></li>                          
        </ul>
    </div>    
    <div class="settings toc">
        <a class="menu-button" title="®Toc_actions®" onclick="$(this).toggleClass('active')" href="javascript:toggle('#tocs_actions');"></a>
        <a class="sort-button <?php echo acl_value_get("toc_order"); ?>" title="®Reverse_bookmarks_order®" href="javascript:sort_bookmarks('toc', '<?php echo (acl_value_get("toc_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'assets');"></a>
        <ul id="tocs_actions">
            <li><a href="#" data-reveal-id="popup_export_toc" title="®Export_album_bookmarks®">®Export_bookmarks®</a></li>  
            <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) { ?>
                <li><a href="#" data-reveal-id="popup_import_bookmarks" onclick="makeRequest('index.php', '?action=view_import', 'popup_import_bookmarks');" title="®Import_album_bookmarks®">®Import_bookmarks®</a></li>
                <li><a href="#" data-reveal-id="popup_delete_tocs" title="®Delete_album_bookmarks®">®Delete_bookmarks®</a></li>                          
            <?php } ?>
        </ul>
    </div>
</div>
<?php require_once template_getpath('popup_import_bookmarks.php'); ?>
<?php require_once template_getpath('popup_export_bookmarks.php'); ?>
<?php require_once template_getpath('popup_delete_bookmarks.php'); ?>
<?php require_once template_getpath('popup_delete_tocs.php'); ?>
<?php require_once template_getpath('popup_export_toc.php'); ?>

<!-- #side_pane
the pane displays the list of all assets contained in the selected album
-->
<div id="side_pane">
    <div id="side-pane-scroll-area">
        <?php if (acl_user_is_logged()) { ?>
            <div class="side_pane_content" id="album_bookmarks">
                <div class="side_pane_up"><a href="javascript:scroll('down','.bookmark_scroll');"></a></div>
                <?php if (!isset($album_bookmarks) || sizeof($album_bookmarks) == 0) {
                    ?>
                    <div class="no_content">®No_bookmarks®</div>
                    <?php
                } else {
                    ?>
                    <ul class="bookmark_scroll">
                        <?php
                        foreach ($album_bookmarks as $index => $bookmark) {
                            ?>
                            <li id="bookmark_<?php echo $index; ?>" class="blue level_<?php echo $bookmark['level']; ?>">
                                
                                <a class="item blue" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>')">                                    
                                    <?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                    <br/><b><?php print_bookmark_title($bookmark['title']); ?></b>
                                </a>
                                <span class="more"><a class="more-button" onclick="toggle_detail('<?php echo $index; ?>', 'bookmark', $(this));"></a></span>
                                <div class="bookmark_detail" id="bookmark_detail_<?php echo $index; ?>">
                                    <div class="bookmark_info">
                                        <b class="blue-title">®Description® :</b>
                                        <?php print_info($bookmark['description']); ?>
                                        <b class="blue-title" style="margin-top: 6px;">®Keywords® : </b>
                                        <?php print_search($bookmark['keywords']); ?>
                                    </div>
                                    <div class="bookmark_options">
                                        <?php $call = 'assets'; ?>
                                        <a class="delete-button" title="®Delete_bookmark®" href="#" data-reveal-id="popup_delete_bookmark_<?php echo $index ?>"></a>
                                    </div>
                                </div>
                            </li>
                            <?php
                            require template_getpath('popup_delete_bookmark.php');
                        }
                        ?>
                    </ul>
                <?php }
                ?>
                <div class="side_pane_down"><a href="javascript:scroll('up','.bookmark_scroll');"></a></div>
            </div>
        <?php } ?>
        <div class="side_pane_content" id="album_toc">
            <div class="side_pane_up"><a href="javascript:scroll('down','.toc_scroll');"></a></div>
            <?php if (!isset($toc_bookmarks) || sizeof($toc_bookmarks) == 0) {
                ?>
                <div class="no_content">®No_toc®</div>
                <?php
            } else {
                ?>
                <ul class="toc_scroll">
                    <?php
                    foreach ($toc_bookmarks as $index => $bookmark) {
                        ?>
                        <li id="toc_<?php echo $index; ?>" class="orange level_<?php echo $bookmark['level']; ?>">

                            <a class="item orange" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>')">
                                    <?php print_info(substr(get_user_friendly_date($bookmark['asset'], '/', false, get_lang(), false), 0, 10)); ?> <?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?>
                                    <br/><b><?php print_bookmark_title($bookmark['title']); ?></b>
                            </a>
                            <span class="more"><a class="more-button orange" onclick="toggle_detail('<?php echo $index; ?>', 'toc', $(this));"></a></span>
                            <div class="bookmark_detail" id="toc_detail_<?php echo $index; ?>">
                                <div class="bookmark_info">
                                    <b class="orange-title">®Description® :</b>
                                    <?php print_info($bookmark['description']); ?>
                                    <b class="orange-title" style="margin-top: 6px;">®Keywords® : </b>
                                    <?php print_search($bookmark['keywords']); ?>
                                </div>
                                <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) { ?>
                                    <div class="bookmark_options">
                                        <?php $call = 'assets'; ?>
                                        <a class="delete-button" title="®Delete_bookmark®" href="#" data-reveal-id="popup_delete_toc_<?php echo $index ?>"></a>
                                    </div>
                                <?php } ?>
                            </div>
                        </li>
                        <?php
                        require template_getpath('popup_delete_toc.php');
                    }
                    ?>
                </ul>
            <?php }
            ?>
            <div class="side_pane_down"><a href="javascript:scroll('up','.toc_scroll');"></a></div>
        </div>
    </div>
</div>