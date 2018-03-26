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

<?php
$playbackRate = false;
switch (strtolower($_SESSION['browser_name'])) {
    case 'safari':
        if ($_SESSION['user_os'] != 'iOS') {
            $playbackRate = true;
        }
        break;
    case 'chrome':
        if ($_SESSION['browser_version'] >= 4) {
            $playbackRate = true;
        }
        break;
    case 'ie':
        if ($_SESSION['browser_version'] >= 9) {
            $playbackRate = true;
        }
        break;
    case 'firefox':
        if ($_SESSION['browser_version'] >= 22) {
            $playbackRate = true;
        }
        break;
}
?> 

<div id="main_player">
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
            <a  href="javascript:show_album_assets('<?php echo $album; ?>', '<?php echo $token; ?>');" 
                title="®Back_to_album®">
                (<?php if (isset($course_code_public) && $course_code_public!="") {
                echo $course_code_public;
            } else {
                echo suffix_remove($album);
            } ?> ) 
                <?php  echo get_album_title($album); ?>
            </a>
        <?php
        } ?>
        <div class="right-arrow"></div><?php print_info($asset_meta['title']); ?>
    </div>

    <div id="video_player" class="remove_full">
        <!-- #main_video : HTML5 video player.
            There is no selected source by default.
        -->
        <div id="video_shortcuts">
            <div class="shortcuts">
                <ul>
                    <li><span class="key space"></span><span>®key_space®</span></li>
                    <li><span class="key back-next"></span><span>®key_back®</span></li>
                    <li><span class="key speed"></span><span>®key_speed®</span></li>
                    <li><span class="key volume"></span><span>®key_volume®</span></li>
                    <li><span class="key m"></span><span>®key_m®</span></li>
                    <li><span class="key s"></span><span>®key_s®</span></li>
                    <li><span class="key n"></span><span>®key_n®</span></li>
                    <li><span class="key d"></span><span>®key_d®</span></li>
                    <li><span class="key shift"></span><span>®key_shift®</span></li>
                    <li><span class="key f"></span><span>®key_f®</span></li>
                </ul>
            </div>
            <div class="shortcuts_tab"><a href="javascript:player_shortcuts_toggle();"></a></div>
        </div>
 
        <?php if (acl_display_thread_notification()) {  ?>           
            <script>
                display_threads_notif = true;
            </script>
            <div id='video_notifications'>
                <div class='notifications_title'><b>®Current_discussions®</b></div>
                <div id='notifications'></div>
            </div>
        <?php  } ?>
       

        <video id="main_video" poster="./images/Generale/¤poster¤-<?php echo get_lang(); ?>.jpg" controls 
               controlsList="nodownload" src="<?php echo $asset_meta['src']; ?>" preload="auto" type="video/mp4">
            <source id="main_video_source" 
                    <?php if (array_key_exists('low_slide_src', $asset_meta)) { ?> high_slide_src="<?php echo $asset_meta['high_slide_src'] . '&origin=' . $appname; ?>" low_slide_src="<?php echo $asset_meta['low_slide_src'] . '&origin=' . $appname; ?>"<?php } ?>
        
                    <?php if (array_key_exists('low_cam_src', $asset_meta)) { ?> high_cam_src="<?php echo $asset_meta['high_cam_src'] . '&origin=' . $appname; ?>" low_cam_src="<?php echo $asset_meta['low_cam_src'] . '&origin=' . $appname; ?>" <?php
        } ?>>  
        </video>

        <?php if ($asset_meta['record_type'] == 'camslide') {
            ?>
            <video id="secondary_video" poster="./images/Generale/¤poster¤-<?php echo get_lang(); ?>.jpg" controls 
                   controlsList="nodownload" src="<?php echo $asset_meta['low_slide_src'] . '&origin=' . $appname; ?>" 
                   preload="metadata" type="video/mp4">
            </video>
        <?php
        } ?>

        <div id="load_warn">®Load_for_switch®</div>


        <script>
            type = 'cam';
<?php if (isset($_SESSION['loaded_type'])) {
            ?>
                type = '<?php echo $_SESSION['loaded_type']; ?>';
<?php
        } else {
            if (isset($asset_meta['record_type']) && $asset_meta['record_type'] != 'camslide') {
                ?>
                    type = '<?php echo $asset_meta['record_type'] ?>';
    <?php
            }
        }
?>
            time = <?php echo(isset($timecode) ? $timecode : 0) ?>;

            player_prepare('low', type, time);
        </script>

<?php require template_getpath('div_bookmark_form.php'); ?>
<?php require template_getpath('div_thread_form.php'); ?>

        <div class="video_controls">
            <ul>
<?php if ($playbackRate) {
    ?>
                    <li>
                        <a id="toggleRate" href="javascript:player_playbackspeed_toggle();" title="®Change_speedrate®">1.0x</a> 
                    </li>
                    <?php
}
                if (isset($asset_meta['record_type']) && $asset_meta['record_type'] == 'camslide') {
                    ?>
                    <li>
                        <a class="movie-button active" title="®Watch_video®" href="javascript:player_video_type_set('cam');"></a>
                        <a class="slide-button" title="®Watch_slide®" href="javascript:player_video_type_set('slide');"></a>
                    </li>
<?php
                } ?>
                <li>
                    <a class="high-button" title="®Watch_high®" href="javascript:player_video_quality_set('high');"></a>
                    <a class="low-button active" title="®Watch_low®" href="javascript:player_video_quality_set('low');"></a>
                </li>
                    <?php if (acl_user_is_logged()) {
                    ?>
                    <li>
                        <?php if (acl_has_album_permissions($album)) {
                        ?>
                        <a class="add-bookmark-button" title="®Add_bookmark®" href="javascript:player_bookmark_form_toggle('personal');"></a>
                        <?php
                    }
                    if (acl_has_album_moderation($album) || acl_is_admin()) {
                        ?>
                            <a class="add-toc-button" title="®Add_toc®" href="javascript:player_bookmark_form_toggle('official');"></a>
                            <?php
                    }
                    if (acl_display_threads()) {
                        ?>
                            <a class="add-thread-button" title="®Add_discussion®" href="javascript:player_thread_form_toggle();"></a>
                    <?php
                    } ?>
                    </li>
<?php
                } ?>                
                <li>
                    <a class="share-button" href="javascript:popup_asset(current_album, current_asset, time, type, 'share_time')" title="®Share_time®" 
                       onclick="player_video_link()"></a>
                </li>      
                <li>
                    <a class="fullscreen-button" href="javascript:player_video_fullscreen(!fullscreen);" title="®Toggle_fullscreen®" ></a>
                </li>   
                <li>
                    <a class="panel-button" href="javascript:player_bookmarks_panel_toggle();" title="®Display_tab®" ></a>
                </li>
            </ul>
        </div>
    </div> <!-- END VIDEO PLAYER -->


</div><!-- END of #main_player -->
