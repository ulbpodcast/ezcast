<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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
<script>
    ezplayer_mode = '<?php echo $_SESSION['ezplayer_mode']; ?>';
</script>
<!-- Left column: player and comments -->
<div id="div_left">
    <!-- Player goes here -->
    <?php
    if ($_SESSION['ezplayer_mode'] == 'view_album_assets') {
        include_once template_getpath('div_left_assets.php');
    } else {
        include_once template_getpath('div_left_details.php');
    }
    ?>
</div><!-- div_left END -->
<!-- Left column: player and comments END -->

<!-- Right column: assets list -->
<div id="div_right">
    <!-- Side part goes here : assets list, asset details and ToC -->                        
    <?php
    if ($_SESSION['ezplayer_mode'] == 'view_album_assets') {
        require template_getpath('div_right_assets.php');
    } else {
        require template_getpath('div_right_details.php');
    }
    ?>

</div><!-- div_right END -->

<div id="bottom">
    <!-- bottom part goes here : trending posts, discussions, ... -->                        
    <?php
    if ($_SESSION['ezplayer_mode'] == 'view_album_assets') {
        require template_getpath('div_trending_threads.php');
    } else {
        require template_getpath('div_video_description.php');
        if (acl_display_threads()) {
            ?>
            <div id="threads" class="threads_info">
                <?php
                if ($_SESSION['thread_display'] == 'details') {
                    include_once template_getpath('div_thread_details.php');
                } else {
                    include_once template_getpath('div_threads_list.php');
                } ?>
            </div><!-- END of #threads_info -->
            <?php
        }
    }
    ?>
</div>