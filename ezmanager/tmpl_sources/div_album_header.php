<!--
Before calling this template, please define the following variables:
- $album The (technical) album name, i.e. including suffix
- $album_name The (user-friendly) album name
- $title The album description/long name
- $public_album Set to "true" if the album is public, false otherwise
- $hd_rss_url URL to the HD RSS feed
- $sd_rss_url URL to the SD RSS feed
-->
<div id="div_album_header">
    <div class="BlocInfoAlbum">
            <div class="ButtonInfoAlbum"> 
                <span class="TitreCour">
                    <?php echo (isset($course_code_public) && $course_code_public !="") ? $course_code_public : $album_id; ?> |
                    <?php echo $title; ?> | 
                    <?php echo ($public_album) ? '®Public_album®' : '®Private_album®'; ?>
                </span>
                
                <!-- drop-down menu -->
                <div id="advanced_menu">
                    <div class="btn-group" role="group">
                        
                        <a href="index.php?action=view_submit_media" class="btn btn-default" role="button"
                            data-remote="false" data-toggle="modal" data-target="#modal" > 
                            <img src="images/page4/iconUp.png" style="height: 18px;" />
                            <span class="TitrePodcast"> 
                                ®Submit_record®
                            </span> 
                        </a>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" 
                                  aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-background-icon-button ButtonSuppAlbum">
                                    <a href="index.php?action=show_popup&amp;popup=delete_album&amp;album=<?php 
                                            echo rawurlencode(trim($album_name)); ?>&amp;album_id=<?php echo rawurlencode(trim($album_id)); ?>" 
                                        data-remote="false" data-toggle="modal" data-target="#modal">
                                        ®Delete_album®
                                    </a>
                                </li>
                                <li class="dropdown-background-icon-button ButtonEdit">
                                    <a href="index.php?action=view_edit_album" data-remote="false" data-toggle="modal" 
                                        data-target="#modal">
                                        ®Edit_album®
                                    </a>
                                </li>
                                <?php if ($enable_moderator) {
                                                ?> 
                                    <li class="dropdown-background-icon-button ButtonModerator">
                                        <a href="index.php?action=view_list_moderator" data-toggle="modal"
                                            data-target="#modal">
                                            ®Moderator_manage®
                                        </a>
                                    </li> 
                                <?php
                                            } ?>
                                <li class="dropdown-background-icon-button ButtonResetBroadcast">
                                    <a href="index.php?action=show_popup&amp;popup=reset_rss_feed&amp;album=<?php 
                                        echo $album_name_full; ?>" data-remote="false" data-toggle="modal" 
                                        data-target="#modal">
                                        ®Reset_broadcast_link®
                                    </a>
                                </li>
                                <?php if ($trace_on && $display_trace_stats) {
                                            ?>
                                    <li class="dropdown-background-icon-button ButtonResetStats">
                                        <a href="index.php?action=show_popup&amp;popup=album_stats_reset&amp;album=<?php 
                                            echo $album_name_full; ?>" data-remote="false" data-toggle="modal" 
                                            data-target="#modal">
                                            ®Stats_Reset®
                                        </a>
                                    </li>
                                <?php
                                        } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li role="presentation" id="list" style="padding-left: 14px;"
                    <?php if (!isset($current_tab) || $current_tab == 'list') {
                                            echo 'class="active"';
                                        } ?> >
                    <a href="javascript:show_album_details('<?php echo $current_album; ?>');">
                        <img src="images/page4/list.png" style="display:inline;height: 12px;"/> 
                        ®Assets_list®
                    </a>
                </li>
                <li role="presentation" id="url" 
                    <?php if (isset($current_tab) && $current_tab == 'url') {
                                            echo 'class="active"';
                                        } ?>>
                    <a <?php if (!$public_album) {
                                            echo 'style="color: red !important;"';
                                        } ?>
                        href="javascript:show_ezplayer_link('<?php echo $current_album; ?>');">
                        <img src="images/page4/PictoEZPlayer.png" style="display:inline"/> 
                        ®Player_url®
                    </a>
                </li>
                
                <?php if ($trace_on && $display_trace_stats) {
                                            ?>
                    <li role="presentation" id="stats"
                        <?php if (isset($current_tab) && $current_tab == 'stats') {
                                                echo 'class="active"';
                                            } ?>>
                        <a href="javascript:show_stats_descriptives('<?php echo $current_album; ?>'); ">
                            <span class="glyphicon glyphicon-stats" aria-hidden="true" style="color: black;"></span>
                            ®Stats_Descriptives®
                        </a>
                    </li>
                <?php
                                        } ?>
            </ul>
    </div>

    <!-- Popups -->
    <div style="display: none;">
        <?php include_once 'popup_reset_rss_feed.php'; ?>
    </div>
</div>