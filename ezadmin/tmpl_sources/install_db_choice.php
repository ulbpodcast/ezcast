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

    <body link="#000088" vlink="#000044" alink="#0000ff" <?php if (isset($GLOBALS['debugmode']) && $GLOBALS['debugmode'] == "devl") echo 'background="#99ff99"' ?>>
        <div class="container_ezplayer">
            <?php include_once template_getpath("div_header.php"); ?>
            <div id="global">
            <h2 style="padding: 10px 0px;">®install_db_choice®</h2>
            <?php foreach ($errors as $e) {
                ?>
                <div class="alert alert-error">
                    <?php echo $e; ?>
                </div>
            <?php }
            ?>
            <div class="alert alert-error">At least one table used by EZcast already exists in the given database. Select one of the following option to continue the installation.</div>
            <br/>
            <form method="POST" style="padding: 0px 10px;">
                <?php foreach ($radio_buttons as $value => $label) { ?>
                    <input type="radio" checked="checked" id="<?php echo $value; ?>" name="db_choice" value="<?php echo $value; ?>" style="margin-top: 12px; float: left"/>
                    <label for = "<?php echo $value; ?>" style="display: inline-block; width: 650px; margin-left: -25px; padding: 8px 38px; border: 1px solid #CCC;"><?php echo $label; ?></label>
                    <br/>
                <?php } ?>
                <input type="submit" name="db_choice_submit" value="®install®" class="btn btn-primary" style="margin-top: 30px;"/>
            </form>

            </div>
            <?php include_once template_getpath('div_footer.php'); ?>
        </div>
    </body>
</html>