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
<div id="div_ezplayer_url">
    <div class="BlocPodcastMenu">
        <?php 
        if (!$public_album) {
            echo '<br />';
            echo "<div class=\"alert alert-danger text-center\" role=\"alert\">";
            echo "®Player_url_private_alert®";
            echo "</div>";
        }
        ?>
        ®Player_url_message® <br/><br/>
        
        <textarea readonly="" class="form-control" onclick="this.select()"
                id="share_time_link"><?php echo trim($player_full_url); ?></textarea>
        <br />
        <div class="wrapper_clip" style="position:relative; text-align: center;">
            <span id="share_time" onclick="copy_video_url();" class="btn btn-default">
                <span id="share_valid" style="display: none">✔</span>
                ®Copy_to_clipboard®
            </span>
        </div>
        
    </div>
</div>