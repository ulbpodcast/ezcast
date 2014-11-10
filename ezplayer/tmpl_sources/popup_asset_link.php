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

<?php
$asset_link = $ezplayer_url . '/index.php?action=view_asset_details'
        . '&album=' . $album
        . '&asset=' . $asset_meta['record_date']
        . '&asset_token=' . $_SESSION['asset_token']
        . '&anon=true';
?>

<div id="popup_asset_link" class="reveal-modal left">
    <h2><?php echo print_info($asset_meta['title']); ?></h2>
    <br/><p>®Asset_link_message®</p>
    <br/><p style="text-align:center;"><a href="<?php echo $asset_link ?>"><?php echo $asset_link ?></a></p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>

    <!--[if !IE]><!-->
    <div class="wrapper_clip" style="position:relative; text-align: center;">
        <span id="copy_asset_button_<?php echo $index ?>" class="copy-to-clipboard-button">®Copy_to_clipboard®</span>
        <div id="zero_clipboard_asset_<?php echo $index ?>" 
             onmouseout="getElementById('copy_asset_button_<?php echo $index ?>').style.background='#333333'" 
             onmouseover="getElementById('copy_asset_button_<?php echo $index ?>').style.background='#11acea'" 
             style="position:absolute; left:160px; top:0px; width:200px; height:30px; z-index:105; cursor: pointer;"
             onclick="server_trace(new Array('3', 'asset_share', current_album, current_asset, duration));">
        </div>
    </div>
    <script>
        copyToClipboard('#zero_clipboard_asset_<?php echo $index ?>', '<?php echo $asset_link ?>');
    </script>
    <!--<![endif]-->  

    <!--[if IE]>
    <span id="copy_asset_button_<?php echo $index ?>" class="copy-to-clipboard-button" onclick="window.clipboardData.setData('Text','<?php echo $asset_link ?>');">®Copy_to_clipboard®</span>
    <![endif]-->
</div>
