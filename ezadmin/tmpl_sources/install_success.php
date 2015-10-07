<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>®install_page_title®</title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/style.css" rel="stylesheet"/>
        <script type="text/javascript" src="./jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="./modernizr.custom.23345.js"></script>

        <!--
        * EZCAST EZadmin 
        * Copyright (C) 2014 Université libre de Bruxelles
        *
        * Written by Michel Jansens <mjansens@ulb.ac.be>
        * 		    Arnaud Wijns <awijns@ulb.ac.be>
        *                   Antoine Dewilde
        *                   Thibaut Roskam
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

    </head>

    <body link="#000088" vlink="#000044" alink="#0000ff" <?php if ($GLOBALS['debugmode'] == "devl") echo 'background="#99ff99"' ?>>
        <div class="container_ezplayer">
            <?php include_once template_getpath("div_header.php"); ?>
            <div id="global">
            <h2 style="text-align: center; padding: 10px 0px;">®install_success_title®</h2>
            <div class="alert alert-success">Install successful. For improved security, we advise you to delete or rename the "install.php".</div>
            <br/>
            Before using EZcast and its components, create - at least - one renderer.<br/><br/>
            <ol>
                    <li>Connect as administrator in <a target="_blank" href="<?php global $ezadmin_url;
            echo $ezadmin_url; ?>">EZadmin</a></li>
                <li>Select "create renderer" in the menu, on the left</li>
                <li>Follow instruction on the screen</li>
            </ol>
            
            </div>
        <?php include_once template_getpath('div_footer.php'); ?>
        </div>
    </body>
</html>