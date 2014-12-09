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

<div id="popup_movie_link" class="reveal-modal left">
    <h2><b><?php echo print_info($asset_meta['title']); ?></b> (®Video®)</h2>
    <h3><?php print_info(substr(get_user_friendly_date($asset_meta['record_date'], '/', false, get_lang(), false), 0, 10)); ?></h3>
    <br/><p>®Download_movie_message®</p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    <a href="<?php echo $asset_meta['low_cam_src'] . '&origin=link'; ?>" onclick="server_trace(new Array('3', 'cam_download', current_album, current_asset, duration, 'low'));" class="simple-button purple">®low_res®</a>
    <a href="<?php echo $asset_meta['high_cam_src'] . '&origin=link'; ?>" onclick="server_trace(new Array('3', 'cam_download', current_album, current_asset, duration, 'high'));" class="simple-button purple">®high_res®</a>
</div>
