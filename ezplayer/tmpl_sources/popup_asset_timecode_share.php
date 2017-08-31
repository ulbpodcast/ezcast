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
include_once 'lib_print.php';
?> 

<h2><?php echo print_info($asset_meta['title']); ?></h2>
<br/><p>®Share_time_message®</p>
<br/>
<textarea readonly class="share_log_asset" onclick="this.select()" id="share_time_link"><?php echo $share_time; ?></textarea>
<a class="close-reveal-modal" href="javascript:close_popup();">&#215;</a>
<br/>

<!--[if !IE]><!-->
<div class="wrapper_clip" style="position:relative; text-align: center;">
    <span id="share_time" onclick="copy_video_url();" class="copy-to-clipboard-button">
        <span id="share_valid" style="display: none">✔</span>
        ®Copy_to_clipboard®
    </span>
</div>
<!--<![endif]-->  

<!--[if IE]>
<a class="copy-to-clipboard-button" id="share_clip" href="#" onclick="window.clipboardData.setData('Text','<?php echo $share_time; ?>');"></a>
<![endif]-->

<script>
    var copy = false;
    function copy_video_url() {
        if(!copy) {
            server_trace(new Array('4', 'link_copy', current_album, current_asset, duration, time, type, quality));
            copy = true;
        }
        $('#share_time_link').select();
        document.execCommand('copy');
        $('#share_valid').css('display', 'inline');
        $('#share_time').css('background-color', '#2ebb2e');
    }
    
</script>
