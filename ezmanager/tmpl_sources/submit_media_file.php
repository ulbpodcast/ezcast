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
        <link rel="stylesheet" type="text/css" href="css/uploadify.css" />
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/jquery.uploadify.v2.1.4.min.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#uploadify").uploadify({
                    'uploader': 'swf/uploadify.swf',
                    'script': 'uploadify.php',
                    'cancelImg': 'images/cancel.png',
                    'queueID': 'fileAttente',
                    'multi': false,
                    'auto': false,
                    'buttonText': '®Browse®',
                    'folder': '<?php echo $folder_path; ?>',
                    'onComplete': function() {
                        makeRequest('index.php', '?action=submit_upload_ok&folder_path=<?php echo urlencode(dirname($folder_path)); ?>', 'div_content');
                    }
                });
            });
        </script>
    </head>
    <body onload="MM_preloadImages('images/page4/BCreerAlbum.png')">
        <div class="container">
            <div id="global">
                <?php include_once template_getpath('div_main_header.php'); ?>

                <div id="div_center">

                    <!-- Left column: album list -->
                    <div id="div_album_list">
                        <!-- Album list goes here -->

                    </div>
                    <!-- Left column: album list END -->

                    <!-- Right part of the screen: album and asset details -->
                    <div id="div_content">
                        <?php include_once template_getpath('div_submit_media_file.php'); ?>
                    </div><!-- div_content END -->
                </div><!-- center div END -->
            </div><!-- global -->
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->

        </div><!-- Container fin -->
    </body>
</html>
