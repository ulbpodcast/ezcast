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

<?php
// before calling this template, please declare $albums as an array
// with all album short names (i.e. names without the -pub or -priv suffix)
global $redraw;
global $current_album;
global $current_album_is_public;

if(empty($created_albums)) {
    if(empty($allowed_albums)) {
        ?>
        <div style="font-style: italic;padding: 10px;">®No_album_available®</div>
        <?php
    }
    else {
        ?>
        <div style="font-style: italic;padding: 10px;">®No_album_created®</div>
        <?php
    }
}
else {
    foreach($created_albums as $album) {
        $stylePriv = '';
        $stylePrivClic = 'display: none;';
        $stylePub = '';
        $stylePubClic = 'display: none;';

        if($redraw && $current_album == $album) {
            if($current_album_is_public) {
                $stylePub = 'display: none;';
                $stylePubClic = '';
            }
            else {
                $stylePriv = 'display: none;';
                $stylePrivClic = '';
            }
        }
        ?>
            <div class="button_private_album" id="album_<?php echo $album.'-priv'; ?>" style="<?php echo $stylePriv; ?>"> <a href="javascript:show_album_details('<?php echo $album.'-priv'; ?>');"><?php echo $album; ?> (®Private_album®)</a> </div>
            <div class="button_private_album_selected" id="album_<?php echo $album.'-priv' ?>_clic" style="<?php echo $stylePrivClic; ?>"> <a href="javascript:show_album_details('<?php echo $album.'-priv'; ?>');"><?php echo $album; ?> (®Private_album®)</a></div>
            <div class="button_public_album" id="album_<?php echo $album.'-pub'; ?>" style="<?php echo $stylePub; ?>"> <a href="javascript:show_album_details('<?php echo $album.'-pub'; ?>');"><?php echo $album; ?> (®Public_album®)</a> </div>
            <div class="button_public_album_selected" id="album_<?php echo $album.'-pub' ?>_clic" style="<?php echo $stylePubClic; ?>"> <a href="javascript:show_album_details('<?php echo $album.'-pub'; ?>');"><?php echo $album; ?> (®Public_album®)</a></div>
        <?php
    }
}

?>