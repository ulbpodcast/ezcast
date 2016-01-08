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
This popup appears when the user clicks on "move this record".
It presents the user with a list of albums he can move the asset to.

You should not have to use this template on its own. however, if you do, please
make sure $created_albums_with_descriptions is initialized and is an array containing the album names (without any suffix) as keys, and albums descriptions as values
for every album the user can create.
-->

<div class="popup" id="popup_move_asset_<?php echo $asset_name; ?>">
    <h2>®Move®</h2>
    <!-- If all albums have already been created, we display a message explaining the situation -->
    <?php if(empty($created_albums_list_with_descriptions)) {
        ?>
        ®No_albums_to_move_asset_to®
        <?php
    }
    
    //Else, we display the album list
    else { ?>
        ®Move_asset_message®<br/>
        <table>
        <?php 
        foreach($created_albums_list_with_descriptions as $destination_name => $destination_description) {
            ?>
            <tr>
            <!-- Note: upon clicking this link, the JS function defined in show_details_functions.js will call the web_index
                 with an action "create_album". Once the processing is over, this div will be updated with the confirmation message. -->
                <td class="album_name">
                    <a href="javascript:popup_asset_move_callback('<?php echo $album; ?>', '<?php echo $destination_name.'-priv'; ?>', '<?php echo $asset_name; ?>');"><?php echo $destination_name; ?> (®private®)</a>
                </td>
                <td class="album_description">
                    <a href="javascript:popup_asset_move_callback('<?php echo $album; ?>', '<?php echo $destination_name.'-priv'; ?>', '<?php echo $asset_name; ?>');"><?php echo $destination_description; ?> (®Private_album®)</a>
                </td>
            </tr>
            <tr>
            <!-- Note: upon clicking this link, the JS function defined in show_details_functions.js will call the web_index
                 with an action "create_album". Once the processing is over, this div will be updated with the confirmation message. -->
                <td class="album_name">
                    <a href="javascript:popup_asset_move_callback('<?php echo $album; ?>', '<?php echo $destination_name.'-pub'; ?>', '<?php echo $asset_name; ?>');"><?php echo $destination_name; ?> (®public®)</a>
                </td>
                <td class="album_description">
                    <a href="javascript:popup_asset_move_callback('<?php echo $album; ?>', '<?php echo $destination_name.'-pub'; ?>', '<?php echo $asset_name; ?>');"><?php echo $destination_description; ?> (®Public_album®)</a>
                </td>
            </tr>
            <?php
        }
        ?>
        </table>   
        <?php
    }
    ?>
</div>