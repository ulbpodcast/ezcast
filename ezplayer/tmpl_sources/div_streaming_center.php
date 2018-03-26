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
?>
<script>
    lvl = 3;
    ezplayer_mode = '<?php echo $_SESSION['ezplayer_mode']; ?>';
    current_album='<?php echo $_SESSION['album']; ?>';
   current_asset='<?php echo $_SESSION['asset']; ?>';
    asset_token='<?php echo $_SESSION['asset_token']; ?>';

    history.pushState({"url": 'index.php?action=view_streaming&album=' + current_album + '&asset=' + current_asset + '&asset_token=' + '<?php echo $_SESSION['asset_token']; ?>'}, '', '');

    var chat_timer = window.setInterval(function () {
        if (ezplayer_mode == 'view_asset_streaming') {
            chat_messages_get_last();
        } else {
            clearInterval(chat_timer);
        }
    }, 8000);
    chat_scroll_to_end();
</script>

<?php
include_once 'lib_print.php';
?> 
<div class="search_wrapper streaming regular">
    <div id="search">
        <?php include_once template_getpath('div_search.php'); ?>
    </div>
</div>

<div id="main_player" class="small_display">
    <!-- #player_header : contains album title and asset title 
        If the current view is the home page, the header is empty
        If the current view is the album page, the header contains album title only
        If the current view is the asset page, the header contains album title and asset title -->
    <div id="site_map">
        <a class="home-link" href="index.php" title="®Back_to_home®">®Home®</a>    
        <?php
        if (acl_has_album_permissions($album)) {
            $token = acl_token_get($album);
            $token = $token['token']; ?>
            <div class="right-arrow"></div>
            <a  href="javascript:player_kill();show_album_assets('<?php echo $album; ?>', '<?php echo $token; ?>');" title="®Back_to_album®">(<?php echo suffix_remove($album); ?>) <?php echo get_album_title($album); ?></a>   
        <?php
        } ?>
        <div class="right-arrow"></div><?php print_info($asset_meta['title']); ?>
    </div>

    <div id="streaming_config_wrapper">        
        <?php
        if ($is_android) {
            include_once template_getpath("div_streaming_player_android.php");
        } else {
            global $streaming_video_player;
            include_once template_getpath("div_streaming_player_$streaming_video_player.php");
        }
        ?>
    </div>


    <div id="chat_container">
        <?php include_once template_getpath("div_chat.php"); ?>
    </div>
</div><!-- END of #main_player -->





