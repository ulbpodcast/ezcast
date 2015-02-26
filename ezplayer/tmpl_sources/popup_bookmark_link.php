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

$bookmark_link = $ezplayer_url . '/index.php?action=view_asset_bookmark'
        . '&album=' . $album 
        . '&asset=' . $bookmark['asset'] 
        . '&t=' . $bookmark['timecode'];
?>

<div id="popup_bookmark_<?php echo $index ?>" class="reveal-modal">
    <h2><?php print_bookmark_title($bookmark['title']); ?></h2>
    <br/><p>®Bookmark_link_message®</p>
    <br/><p style="text-align:center;"><a href="<?php echo $bookmark_link ?>"><?php echo $bookmark_link ?></a></p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    
    <!--[if !IE]><!-->
    <div class="wrapper_clip" style="position:relative; text-align: center;">
        <span id="copy_button_<?php echo $index ?>" class="copy-to-clipboard-button">®Copy_to_clipboard®</span>
        <div id="zero_clipboard_<?php echo $index ?>" 
             onmouseout="getElementById('copy_button_<?php echo $index ?>').style.background='#333333'" 
             onmouseover="getElementById('copy_button_<?php echo $index ?>').style.background='#11acea'" 
             style="position:absolute; left:160px; top:0px; width:200px; height:30px; z-index:105; cursor: pointer;">
        </div>
    </div>
    <!--<![endif]-->  
    
    <!--[if IE]>
    <span id="copy_button_<?php echo $index ?>" class="Bouton"><a href="#" onclick="window.clipboardData.setData('Text','<?php echo $bookmark_link ?>');"><span>®Copy_to_clipboard®</span></a></span>
    <![endif]-->
</div>

<script>
    copyToClipboard('#zero_clipboard_<?php echo $index ?>', '<?php echo $bookmark_link ?>');
</script>