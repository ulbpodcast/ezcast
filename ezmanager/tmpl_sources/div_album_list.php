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

<div class="col-sm-4 sidebar">
    <ul class="nav nav-sidebar">

    <?php
    // before calling this template, please declare $albums as an array
    // with all album short names (i.e. names without the -pub or -priv suffix)
    global $redraw;
    global $current_album;
    global $current_album_is_public;
    
    if (empty($created_albums)) {
        echo '<li class="disabled"><a href="#" style="font-style: italic;">';
        if (empty($allowed_albums)) {
            echo '®No_album_available®';
        } else {
            echo '®No_album_created®';
        }
        echo '</a></li>';
    } else {
        foreach ($created_albums as $album_) {
            $metadata = ezmam_album_metadata_get($album_."-pub"); //get album name and not id for display
            
            $full_name = $metadata['name'];
            if (isset($metadata['course_code_public']) && $metadata['course_code_public']!='') {
                $displayed_name = $metadata['course_code_public'];
            } else {
                $displayed_name = $album_;
            }

            if (strlen($metadata['name']) > 15) {
                $metadata['name']=substr($metadata['name'], 0, 15)."...";
            } ?>
            <li id="album_<?php echo $album_.'-priv'; ?>" class="album-in-list" title="<?php echo $full_name ?>">
                <a href="javascript:show_album_details('<?php echo $album_.'-priv'; ?>');">
                    <img style="width: 30px;" src="images/page4/iconAlbumPriv.png" />
                    <?php echo $displayed_name; ?> (®Private_album®)
                    <span style="float: right;top: 9px;" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                </a> 
            </li>
            <li id="album_<?php echo $album_.'-pub'; ?>" class="album-in-list album-separation" title="<?php echo $full_name ?>">
                <a href="javascript:show_album_details('<?php echo $album_.'-pub'; ?>');">
                    <img style="width: 30px;" src="images/page4/iconAlbumPublic.png" />
                    <?php echo $displayed_name; ?> (®Public_album®)
                    <span style="float: right;top: 9px;" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                </a> 
            </li>
            <?php
        }
    } ?>
    </ul>
</div>