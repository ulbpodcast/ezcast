<?php 
/*
* EZCAST EZmanager 
*
* Copyright (C) 2014 Université libre de Bruxelles
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
            <div class="BoutonInfoAlbum"> <span class="TitreCour"><?php echo $album_name; ?> | <?php echo $description; ?> | <?php if($public_album) echo '®Public_album®'; else echo '®Private_album®'; ?></span>
                
                <!-- drop-down menu -->
                <div id="advanced_menu">
                    <ul id="dropdown_menu">
                        <li>
                            <div onclick="show_advanced_menu()"></div>
                            <ul class="submenu">
                                <li><span class="BoutonSuppAlbum"><a href="javascript:show_popup_from_inner_div('#popup_delete_album')">®Delete_album®</a></span></li>
                                <li><span class="BoutonEditer"><a href="javascript:show_popup_from_outer_div('index.php?action=view_edit_album')">®Edit_album®</a></span></li>
                                <li><span class="BoutonRSS"><a href="javascript:show_popup_from_inner_div('#popup_reset_rss_feed')">®Regenerate_RSS®</a></span></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                
                <ul>
                    <li><span class="BoutonSoumettreAlbum"><a href="javascript:show_popup_from_outer_div('index.php?action=view_submit_media');">®Submit_record®</a></span></li>
                </ul>
            </div>
            <a class="greyLink" style="padding-left: 15px; border: none; font-size: 0.75em" href="javascript:show_popup_from_inner_div('#HD_RSS_box'); copyToClipboard('#zero_clipboard_hd_rss','<?php echo $hd_rss_url_web; ?>');"><img src="images/page4/PictoRss.png" style="display:inline"/> ®HD_RSS_feed®</a> <a class="greyLink" style="font-size:0.75em;" href="javascript:show_popup_from_inner_div('#SD_RSS_box'); copyToClipboard('#zero_clipboard_sd_rss','<?php echo $sd_rss_url_web; ?>');"><img src="images/page4/PictoRss.png" style="display:inline"/> ®SD_RSS_feed®</a> <a class="greyLink ezplayer" style="font-size:0.75em;" href="javascript:show_popup_from_inner_div('#player_url_box'); copyToClipboard('#zero_clipboard_player_url','<?php echo $player_full_url; ?>');"><img src="images/page4/PictoEZ.png" style="display:inline"/> ®Player_url®</a>

    </div>

    <!-- Popups -->
    <div style="display: none;">
        <?php include_once 'popup_player_url.php'; ?>
        <?php include_once 'popup_hd_rss_feed.php'; ?>
        <?php include_once 'popup_sd_rss_feed.php'; ?>
        <?php include_once 'popup_delete_album.php'; ?>
        <?php include_once 'popup_reset_rss_feed.php'; ?>
    </div>
</div>