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
DEPRECATED

Pops up for resetting RSS feed
You should not have to include this file yourself.
-->
<div class="popup" id="popup_reset_rss_feed">
    <h2>®Regenerate_RSS®?</h2>
    <span class="warning">®Non_reversible_operation®</span><br/><br/>
    <div>®Regenerate_RSS_message®</div><br/>
     <span class="Bouton"><a href="?action=view_help" target="_blank"><span>®Help®</span></a></span>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span class="Bouton"><a href="javascript:close_popup();"><span>®Cancel®</span></a></span>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span class="Bouton"><a href="javascript:popup_regenerate_rss_callback('<?php echo $album; ?>');"><span>®OK®</span></a></span>
</div>