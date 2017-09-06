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
This popup appears when the user clicks on "move this record".
It presents the user with a list of albums he can move the asset to.

You should not have to use this template on its own. however, if you do, please
make sure $created_albums_with_descriptions is initialized and is an array containing the album names (without any suffix) as keys, and albums descriptions as values
for every album the user can create.
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Move®</h4>
</div>
<div class="modal-body">
    <?php if (empty($created_albums_list_with_descriptions)) {
    ?>
        ®No_albums_to_move_asset_to®
    <?php
} else {
        ?>
        <p>®Move_asset_message®</p>

        <div class="row">
            <br />
            <div class="col-md-12">
                <table class="table table-hover text-left" >
                    <?php foreach ($created_albums_list_with_descriptions as $destination_name => $destination_description) {
            // sortir des template...
            global $repository_path;
            $course_code_public='';
            $album_path = $repository_path . "/" . $destination_name."-pub";
            $album_metadata = metadata2assoc_array($album_path . "/_metadata.xml");
            if (isset($album_metadata['course_code_public']) && $album_metadata['course_code_public']!='') {
                $course_code_public = $album_metadata['course_code_public'];
            } else {
                $course_code_public = $destination_name;
            }
                        
            if ($album != $destination_name.'-priv') {
                echo '<tr>';
                echo '<td class="album_name col-md-4" style="font-weight: bold;">';
                echo '<a href="index.php?action=move_asset&from='.$album.'&to='.
                                            $destination_name.'-priv'.'&asset='.$asset_name.'" ' .
                                            'onClick=\'setTimeout(function(){ display_bootstrap_modal($("#modal"), '.
                                                '$("#move_asset_'.$destination_name.'_priv"));$("#modal").modal("show"); }, 500);\' ' .
                                            'data-dismiss="modal" id="move_asset_'.$destination_name.'_priv" >';
                echo $course_code_public . ' (®private®)';
                echo '</a>';
                echo '</td>';
                echo '<td class="album_description">';
                echo $destination_description . ' (®Private_album®)';
                echo '</td>';
                echo '</tr>';
            }
            if ($album != $destination_name.'-pub') {
                echo '<tr>';
                echo '<td class="album_name col-md-2" style="font-weight: bold;">';
                echo '<a href="index.php?action=move_asset&from='.$album.'&to='.
                                            $destination_name.'-pub'.'&asset='.$asset_name.'" ' .
                                            'onClick=\'setTimeout(function(){ display_bootstrap_modal($("#modal"), '.
                                                '$("#move_asset_'.$destination_name.'_pub"));$("#modal").modal("show"); }, 500);\' ' .
                                            'data-dismiss="modal" id="move_asset_'.$destination_name.'_pub" >';
                echo $course_code_public . ' (®public®)';
                echo '</a>';
                echo '</td>';
                echo '<td class="album_description">';
                echo $destination_description . ' (®Public_album®)';
                echo '</td>';
                echo '</tr>';
            }
        } ?>
                </table>
            </div>
        </div>
    <?php
    } ?>
</div>
