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
Asks confirmation before deleting an album.
You should not have to include this file yourself (included in div_album_header.php), but if you do, make sure that $album_name is correctly defined
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Unpublish®</h4>
</div>
<div class="modal-body">
    <strong>®Title®&nbsp;:</strong> <?php echo htmlspecialchars($title); ?>

    <center>
        <a class="btn btn-info" target="_blank" href="?action=view_help" role="button">®Help®</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#unpublish_asset'));$('#modal').modal('show'); }, 500);"
            href="index.php?action=unpublish_asset&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>" 
            data-dismiss="modal" id="unpublish_asset">
            ®OK®
        </a>
    </center>
</div>