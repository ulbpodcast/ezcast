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
This is the popup displaying the URL to the HD RSS feed.
You should not have to use this file on your own; if you do, make sure the variable $hd_rss_url is defined
-->
<div class="popup" id="player_url_box" style="width:600px;">
    <h2>®Player_url®</h2>
    ®Player_url_message® <br/><br/>
    <strong><a href="<?php echo $player_full_url; ?>" target="_blank"><?php echo $player_full_url; ?></a><br/><br/></strong>
        
    <!-- Copy to clipboard button 
    All browsers use flash + javascript except Internet explorer which has an access to the clipboard -->
    
    <!--[if !IE]><!-->
    <div id="wrapper_clip" style="position:relative">
        <span id="copy_player_url" class="Bouton"><a><span id="copy_button_text_player_url">®Copy_to_clipboard®</span></a></span>
        <div id="zero_clipboard_player_url" onmouseout="getElementById('copy_button_text_player_url').style.color='#797676'" onmouseover="getElementById('copy_button_text_player_url').style.color='#004B93'" style="position:absolute; left:200px; top:0px; width:200px; height:30px; z-index:99"></div>
    </div>
    <!--<![endif]-->  
    
    <!--[if IE]>
    <span id="copy_button_player_url" class="Bouton"><a href="#" onclick="window.clipboardData.setData('Text','<?php echo $player_full_url; ?>');"><span>®Copy_to_clipboard®</span></a></span>
    <![endif]-->
</div>

