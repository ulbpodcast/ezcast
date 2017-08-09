<?php 
/*
* EZCAST EZmanager 
*
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
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

<!--
Before calling this template, please define the following variables:
- $album The (technical) album name, i.e. including suffix
- $album_name The (user-friendly) album name
- $description The album description/long name
- $public_album Set to "true" if the album is public, false otherwise
- $hd_rss_url URL to the HD RSS feed
- $sd_rss_url URL to the SD RSS feed
-->
<div id="div_album_header">
    <div class="BlocInfoAlbum">
            <div class="BoutonInfoAlbum"> 
                <span class="TitreCour">
                    <?php echo $album_name; ?> | 
                    <?php echo $description; ?> | 
                    <?php ($public_album) ? '®Public_album®' : '®Private_album®'; ?>
                </span>
                
                <!-- drop-down menu -->
                <div id="advanced_menu">
                    <ul id="dropdown_menu">
                        <li>
                            <div onclick="show_advanced_menu()"></div>
                            <ul class="submenu">
                                <li>
                                    <span class="BoutonSuppAlbum">
                                        <a href="javascript:show_popup_from_inner_div('#popup_delete_album')">
                                            ®Delete_album®
                                        </a>
                                    </span>
                                </li>
                                <li>
                                    <span class="BoutonEditer">
                                        <a href="index.php?action=view_edit_album" data-remote="false" data-toggle="modal" 
                                           data-target="#modal">
                                            ®Edit_album®
                                        </a>
                                    </span>
                                </li>
                                <li>
                                    <span class="BoutonRSS">
                                        <a href="javascript:show_popup_from_inner_div('#popup_reset_rss_feed')">
                                            ®Regenerate_RSS®
                                        </a>
                                    </span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li role="presentation" id="list" style="padding-left: 5px;"
                    <?php if(!isset($current_tab) || $current_tab == 'list' ) { echo 'class="active"'; } ?> >
                    <a href="javascript:show_album_details('<?php echo $album; ?>');">
                        <img src="images/page4/list.png" style="display:inline"/> 
                        ®Assets_list®
                    </a>
                </li>
                <li role="presentation" id="url" 
                    <?php if(isset($current_tab) && $current_tab == 'url' ) { echo 'class="active"'; } ?>>
                    <a <?php if(!$public_album) { echo 'style="color: red !important;"'; } ?>
                        href="javascript:show_ezplayer_link('<?php echo $album; ?>');">
                        <img src="images/page4/PictoEZ.png" style="display:inline"/> 
                        ®Player_url®
                    </a>
                </li>
                <li role="presentation" id="stats"
                    <?php if(isset($current_tab) && $current_tab == 'stats' ) { echo 'class="active"'; } ?>>
                    <a href="javascript:show_stats_descriptives('<?php echo $album; ?>'); ">
                        <img src="images/page4/stats.png" style="display:inline"/>
                        ®Stats_Descriptives®
                    </a>
                </li>
            </ul>
    </div>

    <!-- Popups -->
    <div style="display: none;">
        <?php include_once 'popup_player_url.php'; ?>
        <?php include_once 'popup_delete_album.php'; ?>
        <?php include_once 'popup_reset_rss_feed.php'; ?>
        <?php include_once 'popup_stats_descriptives.php'; ?>
    </div>
</div>