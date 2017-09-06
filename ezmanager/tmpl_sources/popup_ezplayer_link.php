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
This is the popup displaying the ULB code for the video
You should not have to use this file on your own; if you do, make sure the variables $ulb_code are defined
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Player_url®</h4>
</div>
<div class="modal-body">
    <p>®EZplayer_link_message®</p>

    <textarea readonly="" class="form-control" onclick="this.select()" id="share_time_link"><?php echo trim($ezplayer_link); ?></textarea>
    <br />
    <div class="wrapper_clip" style="position:relative; text-align: center;">
        <span id="share_time" onclick="copy_video_url();" class="btn btn-default">
            <span id="share_valid" style="display: none">✔</span>
            ®Copy_to_clipboard®
        </span>
    </div>
</div>
