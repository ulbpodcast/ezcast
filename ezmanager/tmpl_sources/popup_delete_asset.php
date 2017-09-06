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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Delete_asset® "<?php echo htmlspecialchars($title); ?>" ?</h4>
</div>
<div class="modal-body">
    <div class="alert alert-warning text-center" role="alert">®Destructive_operation®</div>

    <center>
        <a class="btn btn-info" target="_blank" href="?action=view_help" role="button">®Help®</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onClick="$('#delete_album_load').load('index.php?action=delete_asset&album=<?php 
            echo $album; ?>&asset=<?php echo $asset_name; ?>');show_album_details('<?php echo $album; ?>');"
            data-dismiss="modal">
            ®OK®
        </a>
        <span id="delete_album_load" class="do-not-remove" style="display: none;"></span>
    </center>
</div>