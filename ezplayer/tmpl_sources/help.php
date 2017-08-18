<!DOCTYPE html>

<!--
 This page is meant to contain a FAQ/tutorial on how to use the service
-->

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--
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
        -->
        <title>®ezplayer_page_title®</title>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/ezplayer_style_v2.css" />
        <link rel="stylesheet" type="text/css" href="css/smartphone.css" />
		
        <script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#topics li a').click(function() {
                    $(this).siblings().toggle(200);
                });
            });
        </script>
    </head>
    <body>
        <div class="container">
            <?php include_once template_getpath('div_help_header.php'); ?>
            <div id="global">
                <div id="div_center">
                    <?php
                    include_once template_getpath('div_help_center.php');
                    ?>
                </div><!-- div_center END -->
            </div><!-- global -->
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
        </div><!-- Container fin -->
    </body>
</html>
