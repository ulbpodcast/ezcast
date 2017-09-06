<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php /*
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

        Main template.
        This template is the main frame, the content divs are dynamically filled as the user clicks.

        WARNING: Please call template_repository_path() BEFORE including this template
        */ ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=8" />
        <title>®podman_page_title®</title>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <link rel="stylesheet" type="text/css" href="commons/css/common_style.css" />
        <link rel="stylesheet" type="text/css" href="css/colorbox.css" />
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-2.2.4.min.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript" src="js/upload.js"></script>
        <script type="text/javascript">
            /**
             * Retrieves album header and displays it in div_album_header
             */
            var current_album = '<?php echo $_SESSION['podman_album']; ?>';

            function show_album_details(album) {
                // highlighting the current album, and removing the old one
                if (document.getElementById('album_' + current_album + '_clic')) {
                    document.getElementById('album_' + current_album + '_clic').style.display = 'none';
                    document.getElementById('album_' + current_album + '').style.display = '';
                }
                if (document.getElementById('album_' + album + '_clic')) {
                    document.getElementById('album_' + album + '_clic').style.display = '';
                    document.getElementById('album_' + album + '').style.display = 'none';
                }
                current_album = album;

                // Getting the content from the server, and filling the div_album_header with it
                makeRequest('index.php', '?action=view_album&album=' + album, 'div_content');
            }

            /**
             * This function is called whenever the user saves their change on an asset (Edit mode)
             */
            function edit_asset_data(album, asset) {
                // First we retrieve the data
                var title = encodeURIComponent(document.getElementById('title_' + asset + '_input').value);
                var description = encodeURIComponent(document.getElementById('description_' + asset + '_input').value);

                // Then we update them
                makeRequest('index.php', '?action=edit_asset&album=' + album + '&asset=' + asset + '&title=' + title + '&description=' + description, 'asset_' + asset + '_details');

                // And finally we refresh the view
                document.getElementById('asset_' + asset + '_title').innerHTML = ' | ' + decodeURIComponent(title);
                document.getElementById('asset_' + asset + '_title_clic').innerHTML = ' | ' + decodeURIComponent(title);
            }
        </script>
        <script type="text/javascript" src="js/popup_general.js"></script>
        <script type="text/javascript" src="js/popup_callback.js"></script>
        <script type="text/javascript" src="js/show_details_functions.js"></script>
    </head>
    <body onload="MM_preloadImages('images/page4/BCreerAlbum.png')">
        <div class="container">
            <div id="global">
                <?php include_once template_getpath('div_main_header.php'); ?>

                <!-- "New album" button -->
                <div class="button_new_album"> <a href="javascript:show_popup_from_inner_div('#popup_new_album')" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5', '', 'images/page4/BCreerAlbum_<?php echo get_lang(); ?>.png', 1)"><img src="images/page4/ACreerAlbum_<?php echo get_lang(); ?>.png" name="Image5" width="101" height="14" border="0" id="Image5" title="®Create_album®" /></a></div>
                <!-- "New album" button, END -->


                <div id="div_center">

                    <!-- Left column: album list -->
                    <div id="div_album_list">
                        <!-- Album list goes here -->
                        <?php include_once template_getpath('div_album_list.php'); ?>
                    </div>
                    <!-- Left column: album list END -->

                    <!-- Right part of the screen: album and asset details -->
                    <div id="div_content">
                        <?php include_once template_getpath('div_submit_media_infos.php'); ?>
                    </div><!-- div_content END -->
                </div><!-- center div END -->
            </div><!-- global -->
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->

            <!-- Popups -->
            <div style="display: none;">
                <?php include_once 'popup_new_album.php'; ?>

                <!-- This popup gets automatically filled with messages, depending on the situation -->
                <div class="popup" id="popup_messages"></div>

                <!-- This popup gets automatically filled with errors -->
                <div class="popup" id="popup_errors"></div>
            </div>
        </div><!-- Container fin -->
    </body>
</html>
