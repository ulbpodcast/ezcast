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

<h2>®RSS_url®</h2>
<a class="close-reveal-modal" href="javascript:close_popup();">&#215;</a>
<br/>
<p>®EZPLAYER_RSS_feed_URL_message®</p><br/>
<p style="text-align:center; color: #333333;"><a href="<?php echo $album['rss']; ?>"><?php echo $album['rss']; ?></a></p><br/>
<!--[if !IE]><!-->
<div class="wrapper_clip" style="position:relative; text-align: center;">
    <span id='share_rss_button' class="copy-to-clipboard-button centered" >®Copy_to_clipboard®</span>
    <div id="share_clip_rss" 
         title="®HD_RSS_feed®" 
         onmouseout="getElementById('share_rss_button').style.background = '#333333'" 
         onmouseover="getElementById('share_rss_button').style.background = '#11acea'"
         style="position:absolute; left:200px; top:0px; width:200px; height:30px; z-index:105; cursor: pointer;">
    </div>
</div>
<script>
    copyToClipboard('#share_clip_rss', '<?php echo $album['rss']; ?>', 200);
</script>
<!--<![endif]-->  

<!--[if IE]>
<a class="copy-to-clipboard-button" id="share_clip_rss" href="#" onclick="window.clipboardData.setData('Text','<?php echo $album['rss']; ?>');"></a>
<![endif]-->