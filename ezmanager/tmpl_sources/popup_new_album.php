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
This popup appears when the user clicks on "create an album".
It asks for basic information about the album to create.

You should not have to use this template on its own. however, if you do, please
make sure $not_created_albums_with_descriptions is initialized and is an array containing the album names (without any suffix) as keys, and albums descriptions as values
for every album the user can create.
-->

<div class="popup" id="popup_new_album">
    <h2>®Create_album®</h2>
    <!-- If all albums have already been created, we display a message explaining the situation -->
    <?php if(empty($not_created_albums_with_descriptions)) {
        ?>
        ®All_albums_already_created®
        <?php
    }
    
    //Else, we display the album list
    else { ?>
        ®Create_album_message®<br/>
        <table>
        <?php 
        foreach($not_created_albums_with_descriptions as $album_name => $album_description) {
            ?>
            <tr>
            <!-- Note: upon clicking this link, the JS function defined in show_details_functions.js will call the web_index
                 with an action "create_album". Once the processing is over, this div will be updated with the confirmation message. -->
                <td class="album_name">
                    <a href="javascript:show_popup_from_outer_div('index.php?action=create_album&amp;album=<?php echo $album_name; ?>', true);"><?php echo $album_name; ?></a>
                </td>
                <td class="album_description">
                    <a href="javascript:show_popup_from_outer_div('index.php?action=create_album&amp;album=<?php echo $album_name; ?>', true);"><?php echo $album_description; ?></a>
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