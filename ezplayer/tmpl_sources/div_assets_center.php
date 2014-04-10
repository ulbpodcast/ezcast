<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
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

include_once 'lib_print.php';
?>

<!-- Left column: player and comments -->
<div id="div_left">
    <!-- Player goes here -->
    <?php 
    if ($_SESSION['ezplayer_mode'] == 'view_album_assets'){
    include_once template_getpath('div_left_assets.php');
    } else {
    include_once template_getpath('div_left_details.php'); 
    // comments go here
    include_once template_getpath('div_main_bottom.php');
    }?>
</div>
<!-- Left column: player and comments END -->

<!-- Right column: assets list -->
<div id="div_right">
    <!-- Side part goes here : assets list, asset details and ToC -->                        
    <?php
    if ($_SESSION['ezplayer_mode'] == 'view_album_assets') {

            require template_getpath('div_side_assets.php');
    } else {
            require template_getpath('div_side_details.php');
    }
    ?>

</div><!-- div_right END -->

