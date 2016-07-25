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
Asks confirmation before deleting an asset.
You should not have to include this file yourself, but if you do, make sure that $asset_name is correctly defined
-->
<div class="popup" id="popup_delete_asset_<?php echo $asset_name; ?>">
    <h2>®Delete_asset® "<?php echo htmlspecialchars($title); ?>"?</h2>
    <span class="warning">®Destructive_operation®</span><br/><br/>
    <span class="Bouton"> <a href="?action=view_help" target="_blank"><span>®Help®</span></a></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="Bouton"> <a href="javascript:close_popup();"><span>®Cancel®</span></a></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="Bouton"> <a href="javascript:popup_asset_delete_callback('<?php echo $album; ?>', '<?php echo $asset_name; ?>');"><span>®OK®</span></a></span>
</div>