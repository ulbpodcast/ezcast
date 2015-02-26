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

    /*  $(document).ready(function() { // problem : catch click on video controls
     
     $("#main_video").click(function() {
     toggle_play();
     });
     }); */
    current_album = '<?php echo $_SESSION['album']; ?>'
    current_asset = '<?php echo $_SESSION['asset']; ?>'

    history.pushState({"url": 'index.php?action=view_asset_details&album=' + current_album + '&asset=' + current_asset + '&asset_token=' + '<?php echo $_SESSION['asset_token']; ?>'}, '', '');

<?php if ((!isset($asset_bookmarks) || sizeof($asset_bookmarks) == 0) && (!isset($toc_bookmarks) || sizeof($toc_bookmarks) == 0)) { ?>
        show_panel = false;
<?php } else { ?>
        show_panel = true;
<?php }
      if (!acl_user_is_logged() || ((!isset($asset_bookmarks) || sizeof($asset_bookmarks) == 0) && (isset($toc_bookmarks) && sizeof($toc_bookmarks) != 0))) { ?>
        current_tab = 'toc';
<?php } ?>
    $(document).ready(function () {
        if (current_tab == 'toc') {
            setActivePane('.toc_button');
            $('#side_pane').scrollTo('#album_toc');
        } else {
            setActivePane('.bookmarks_button');
            $('#side_pane').scrollTo('#album_bookmarks');
        }
        if (show_panel){
            panel_show();
        } else {
            panel_hide();
        }
    });
    lvl = 3;
    is_lecturer = false;
    is_logged = false;
<?php if (acl_user_is_logged()) { ?>
        is_logged = true;
    <?php if (acl_has_album_moderation($album)) { ?>;
            is_lecturer = true;
    <?php }
}
?>
    $("video").bind("pause", function (e) {
        paused = ($('video')[1]) ? $('video')[1].paused : true;
        if (($('video')[0].paused && paused) || shortcuts)
            $(".shortcuts_tab").css('display', 'block');
        if (!trace_pause) {
            origin = get_origin();
            server_trace(new Array('4', 'video_pause', current_album, current_asset, duration, time, type, quality, origin));
        } else {
            trace_pause = false;
        }
    });
    $("video").bind("play", function (e) {
        if (!shortcuts)
            $(".shortcuts_tab").css('display', 'none');
        if (!trace_pause) {
            origin = get_origin();
            server_trace(new Array('4', 'video_play', current_album, current_asset, duration, time, type, quality, origin));
        } else {
            trace_pause = false;
        }
    });
    $('video').bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
        var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
        var fullscreen = state ? true : false;
        if (fullscreen) {
            server_trace(new Array('4', 'browser_fullscreen_enter', current_album, current_asset, duration, time, type, quality));

        } else {
            server_trace(new Array('4', 'browser_fullscreen_exit', current_album, current_asset, duration, time, type, quality));
        }
    });

</script>

<div id="search">
<?php include_once template_getpath('div_search.php'); ?>
</div>

<div id="side_wrapper">
    <div id="side_menu">

                                                <?php if ($is_bookmark) { ?>
            <div class="bookmarks_button active"><a href="#asset_bookmarks" onclick="setActivePane('.bookmarks_button');
                    server_trace(new Array('3', 'bookmarks_swap', current_album, current_asset, current_tab));" title="®Display_asset_bookmarks®"></a></div>
                <?php
            }
            ?>
        <div class='toc_button'><a href="#album_toc" onclick="setActivePane('.toc_button');
                server_trace(new Array('3', 'bookmarks_swap', current_album, current_asset, current_tab));" title="®Display_toc®"></a></div>
        <div class="settings bookmarks">
            <a class="menu-button" title="®Bookmarks_actions®" onclick="$(this).toggleClass('active')" href="javascript:toggle('#bookmarks_actions');"></a>
            <a class="sort-button <?php echo acl_value_get("bookmarks_order"); ?>" title="®Reverse_bookmarks_order®" href="javascript:sort_bookmarks('bookmarks', '<?php echo (acl_value_get("bookmarks_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'details');"></a>
            <ul id="bookmarks_actions">
                <li><a href="javascript:bookmarks_popup('<?php echo $_SESSION['album']; ?>', '<?php echo $_SESSION['asset']; ?>', 'custom', 'details', 'export')" title="®Export_asset_bookmarks®">®Export_bookmarks®</a></li> 
                <li><a href="#" data-reveal-id="popup_import_bookmarks" onclick="makeRequest('index.php', '?action=view_import', 'popup_import_bookmarks');" title="®Import_asset_bookmarks®">®Import_bookmarks®</a></li>
                <li><a href="javascript:bookmarks_popup('<?php echo $_SESSION['album']; ?>', '<?php echo $_SESSION['asset']; ?>', 'custom', 'details', 'delete')" title="®Delete_asset_bookmarks®">®Delete_bookmarks®</a></li>                    
            </ul>
        </div>
        <div class="settings toc">
            <a class="menu-button" title="®Toc_actions®" onclick="$(this).toggleClass('active')" href="javascript:toggle('#tocs_actions');"></a>
            <a class="sort-button <?php echo acl_value_get("toc_order"); ?>" title="®Reverse_bookmarks_order®" href="javascript:sort_bookmarks('toc', '<?php echo (acl_value_get("toc_order") == "chron") ? "reverse_chron" : "chron"; ?>', 'details');"></a>
            <ul id="tocs_actions">            
                <li><a href="javascript:bookmarks_popup('<?php echo $_SESSION['album']; ?>', '<?php echo $_SESSION['asset']; ?>', 'official', 'details', 'export')" title="®Export_asset_bookmarks®">®Export_bookmarks®</a></li>  
<?php if (acl_user_is_logged() && acl_has_album_moderation($album)) { ?>
                    <li><a href="#" data-reveal-id="popup_import_bookmarks" onclick="makeRequest('index.php', '?action=view_import', 'popup_import_bookmarks');" title="®Import_asset_bookmarks®">®Import_bookmarks®</a></li>
                    <li><a href="javascript:bookmarks_popup('<?php echo $_SESSION['album']; ?>', '<?php echo $_SESSION['asset']; ?>', 'official', 'details', 'delete')" title="®Delete_asset_bookmarks®">®Delete_bookmarks®</a></li>                          
<?php } ?>
            </ul>
        </div>
    </div>


    <!-- #side_pane
        Contains different panes adapted for each view
    the pane displays information about that asset
    -->
    <div id="side_pane">
        <div id="side-pane-scroll-area">

<?php if ($is_bookmark) { ?>
                <div class="side_pane_content" id="asset_bookmarks">
                    <div class="side_pane_up"><a href="javascript:scroll('down','.bookmark_scroll');"></a></div>
                    <?php
                    if (!isset($asset_bookmarks) || sizeof($asset_bookmarks) == 0) {
                        ?>
                        <div class="no_content">®No_bookmarks®</div>
                        <?php
                    } else {
                        ?>
                        <ul class="bookmark_scroll">
                            <?php
                            foreach ($asset_bookmarks as $index => $bookmark) {
                                ?>
                                <li id="bookmark_<?php echo $index; ?>" class="blue level_<?php echo $bookmark['level']; ?>">
                                    <form action="index.php" method="post" id="submit_bookmark_form_<?php echo $index; ?>" onsubmit="return false">

                                        <a class="item blue" href="javascript:seek_video(<?php echo $bookmark['timecode'] ?>, '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>');">  
                                            <span class="timecode">(<?php print_time($bookmark['timecode']); ?>) </span>
                                            <span id="bookmark<?php echo $index; ?>"><b><?php print_bookmark_title($bookmark['title']); ?></b></span>                                      
                                            <input name="title" id="bookmark_title_<?php echo $index; ?>" type="text" maxlength="70"/>
                                        </a>
                                        <span class="more"><a class="more-button small" onclick="toggle_detail('<?php echo $index; ?>', 'bookmark', $(this));"></a></span>
                                        <div class="bookmark_detail" id="bookmark_detail_<?php echo $index; ?>">
                                            <div class="bookmark_info" id="bookmark_info_<?php echo $index; ?>">
                                                <div class="blue-title">®Description® :</div>
                                                <?php print_info($bookmark['description']); ?>
                                                <div class="blue-title" style="margin-top: 6px;">®Keywords® : </div>
            <?php print_search($bookmark['keywords']); ?>
                                            </div>
                                            <div class="edit_bookmark_form" id="edit_bookmark_<?php echo $index; ?>">            
                                                <input type="hidden" name="album" id="bookmark_album_<?php echo $index; ?>" value="<?php echo $bookmark['album']; ?>"/>
                                                <input type="hidden" name="asset" id="bookmark_asset_<?php echo $index; ?>" value="<?php echo $bookmark['asset']; ?>"/>
                                                <input type="hidden" name="source" id="bookmark_source_<?php echo $index; ?>" value="custom"/><br/>
                                                <input type="hidden" name="timecode" id="bookmark_timecode_<?php echo $index; ?>" value="<?php echo $bookmark['timecode']; ?>"/>
                                                <input type="hidden" name="type" id="bookmark_type_<?php echo $index; ?>" value="<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>"/>
                                                <div class="blue-title">®Description® :</div>
                                                <textarea name="description" id="bookmark_description_<?php echo $index; ?>" rows="4" ></textarea>
                                                <div class="blue-title" style="margin-top: 6px;">®Keywords® : </div>
                                                <input name="keywords" id="bookmark_keywords_<?php echo $index; ?>" type="text"/>
                                                <div class="blue-title" style="margin-top: 6px;">®Level® : </div>
                                                <input type="number" name="level" id="bookmark_level_<?php echo $index; ?>" min="1" max="3" value="1"/>
                                                <!-- Submit button -->
                                                <br/>
                                                <div class="editButtons">
                                                    <a class="button" href="javascript: toggle_edit_bookmark_form('<?php echo $index; ?>', 'bookmark');">®Cancel®</a>                                        
                                                    <a class="button blue" href="javascript: if(check_edit_bookmark_form('<?php echo $index; ?>', 'bookmark')) submit_edit_bookmark_form('<?php echo $index; ?>', 'bookmark');">®Submit®</a>
                                                </div>
                                                <br />
                                            </div>
                                            <div class="bookmark_options">
                                        <a class="delete-button" title="®Delete_bookmark®" href="javascript:bookmark_popup('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', 'custom', 'details', 'remove')"></a>
                                                <a class="edit-button" title="®Edit_bookmark®" href="javascript:edit_bookmark('<?php echo $index; ?>', 'bookmark', '<?php echo htmlspecialchars(str_replace("'", "\'", $bookmark['title'])) ?>', '<?php echo htmlspecialchars(str_replace(array('"', "'"), array("", "\'"), $bookmark['description'])) ?>', '<?php echo htmlspecialchars(str_replace("'", "\'", $bookmark['keywords'])) ?>', '<?php echo $bookmark['level'] ?>', '<?php echo $bookmark['timecode'] ?>', 'custom');"></a>
                                                <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) { ?>
                                                    <a class="copy-button" title="®Copy_bookmark®"  href="javascript:bookmark_popup('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', 'custom', 'details', 'copy')""></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                    </form>
                                </li>
                                    <?php if ($timecode == $bookmark['timecode']) { ?>
                                    <script>
                                        toggle_detail('<?php echo $index; ?>', 'bookmark', $("#bookmark_<?php echo $index; ?> .more a"));</script>
                                    <?php
                                }
                            }
                            ?>
                        </ul>  

                        <?php
                    }
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
                                <form action="index.php" method="post" id="submit_toc_form_<?php echo $index; ?>" onsubmit="return false">

                                        <?php if ($bookmark['asset'] == $asset) { ?>
                                        <a class="item orange" href="javascript:seek_video(<?php echo $bookmark['timecode'] ?>, '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>');">
                                            <?php } else { ?>
                                            <a class="item orange" href="javascript:show_asset_bookmark('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', '<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>')">
        <?php } ?>
                                            <span class="timecode orange">(<?php print_time($bookmark['timecode']); ?>) </span>
                                            <span id="toc<?php echo $index; ?>"><b><?php print_bookmark_title($bookmark['title']); ?></b></span>                                      
                                            <input name="title" id="toc_title_<?php echo $index; ?>" type="text" maxlength="70"/>
                                        </a>
                                        <span class="more"><a class="more-button small orange" onclick="toggle_detail('<?php echo $index; ?>', 'toc', $(this));"></a></span>
                                        <div class="bookmark_detail" id="toc_detail_<?php echo $index; ?>">
                                            <div class="bookmark_info" id="toc_info_<?php echo $index; ?>">
                                                <div class="orange-title">®Description® :</div>
                                                <?php print_info($bookmark['description']); ?>
                                                <div class="orange-title" style="margin-top: 6px;">®Keywords® : </div>
        <?php print_search($bookmark['keywords']); ?>
                                            </div>

                                            <div class="edit_bookmark_form" id="edit_toc_<?php echo $index; ?>">            
                                                <input type="hidden" name="album" id="toc_album_<?php echo $index; ?>" value="<?php echo $bookmark['album']; ?>"/>
                                                <input type="hidden" name="asset" id="toc_asset_<?php echo $index; ?>" value="<?php echo $bookmark['asset']; ?>"/>
                                                <input type="hidden" name="source" id="toc_source_<?php echo $index; ?>" value="official"/><br/>
                                                <input type="hidden" name="timecode" id="toc_timecode_<?php echo $index; ?>" value="<?php echo $bookmark['timecode']; ?>"/>
                                                <input type="hidden" name="type" id="toc_type_<?php echo $index; ?>" value="<?php echo (isset($bookmark['type'])) ? $bookmark['type'] : ''; ?>"/>
                                                <div class="orange-title">®Description® :</div>
                                                <textarea name="description" id="toc_description_<?php echo $index; ?>" rows="4" ></textarea>
                                                <div class="orange-title" style="margin-top: 6px;">®Keywords® : </div>
                                                <input name="keywords" id="toc_keywords_<?php echo $index; ?>" type="text"/>
                                                <div class="orange-title" style="margin-top: 6px;">®Level® : </div>
                                                <input type="number" name="level" id="toc_level_<?php echo $index; ?>" min="1" max="3" value="1"/>
                                                <!-- Submit button -->
                                                <br/>
                                                <div class="editButtons">
                                                    <a class="button" href="javascript: toggle_edit_bookmark_form('<?php echo $index; ?>', 'toc');">®Cancel®</a>
                                                    <a class="button orange" href="javascript: if(check_edit_bookmark_form('<?php echo $index; ?>', 'toc')) submit_edit_bookmark_form('<?php echo $index; ?>', 'toc');">®Submit®</a>
                                                </div>
                                                <br />
                                            </div>
                                                <?php if (acl_user_is_logged() && acl_has_album_moderation($album)) { ?>
                                                <div class="bookmark_options">
                                        <a class="delete-button" title="®Delete_bookmark®" href="javascript:bookmark_popup('<?php echo $bookmark['album']; ?>', '<?php echo $bookmark['asset']; ?>', '<?php echo $bookmark['timecode']; ?>', 'official', 'details', 'remove')"></a>
                                                    <a class="edit-button orange" title="®Edit_bookmark®" href="javascript:edit_bookmark('<?php echo $index; ?>', 'toc', '<?php echo htmlspecialchars(str_replace("'", "\'", $bookmark['title'])) ?>', '<?php echo htmlspecialchars(str_replace(array('"', "'"), array("", "\'"), $bookmark['description'])) ?>', '<?php echo htmlspecialchars(str_replace("'", "\'", $bookmark['keywords'])) ?>', '<?php echo $bookmark['level'] ?>', '<?php echo $bookmark['timecode'] ?>', 'custom');"></a>
                                                </div>
        <?php } ?>
                                        </div>
                                </form>
                            </li>
                            <?php if ($timecode == $bookmark['timecode']) { ?>
                                <script>toggle_detail('<?php echo $index; ?>', 'toc', $("#toc_<?php echo $index; ?> .more a"));</script>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                <?php }
                ?>
                <div class="side_pane_down"><a href="javascript:scroll('up','.toc_scroll');"></a></div>
            </div>
        </div>
    </div>
</div>


