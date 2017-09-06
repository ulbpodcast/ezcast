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
This popup displays a confirmation message when we edit an album.

You should not have to use it by itself (it is called by web_index.php::create_album())
However, if you do, please make sure that $public_album_url is declared and set to the URL to the 
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Album_edition_succeeded_title®</h4>
</div>
<div class="modal-body edit-success">
    <p>®Album_edition_succeeded_message®</p>
</div>
<div class="modal-footer">
    <a type="button" class="btn btn-default" href="index.php">®Close_and_return_to_index®</a>
</div>

<script>
$('#modal').on('hide.bs.modal', function () {
    if($('#modal .modal-body.edit-success').length) {
        $(location).attr('href', 'index.php');
    }
})
</script>
