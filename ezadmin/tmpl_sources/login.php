<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

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

        <title>®ezadmin_page_title®</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link href="css/style.css" rel="stylesheet"/>

        <script type="text/javascript">
            function detect_flash() {
                if ((navigator.mimeTypes ["application/x-shockwave-flash"] == undefined)) {
                    document.login_form.has_flash.value = 'N';
                }
                else {
                    document.login_form.has_flash.value = 'Y';
                }
            }

            function unsafe_connection() {
                document.forms['login_form'].action = "<?php
global $ezadmin_url;
echo $ezadmin_url;
?>/index.php";
                document.forms['login_form'].submit();
            }
        </script>
    </head>
    <body>
        <div class="container">
            <?php include 'div_header.php'; ?>
            <div id="global">
                <p>
                    <br />

                    <br />

                    <?php if (isset($_SESSION['install'])) { ?>
                        <form id="login_form" name="login_form" action="install.php" method="post">
                        <?php } else { ?>
                            <form id="login_form" name="login_form" action="<?php
                            global $ezadmin_safe_url;
                            echo $ezadmin_safe_url;
                            ?>/index.php" method="post" onsubmit="detect_flash();">
                                  <?php } ?>
                                  <?php if (isset($_SESSION['install'])) { ?>
                                <h4>®install_description®</h4>
                            <?php } ?>
                            <div style="color: red; font-weight: bold;"><?php echo $error; ?></div>
                            <input type="hidden" name="action" value="login" />
                            <input type="hidden" name="has_flash" value=""/>
                            <div id="login_fields">
                                <label>®Login®:&nbsp;&nbsp;</label><input type="text" name="login" autocapitalize="off" autocorrect="off" tabindex="1" style="margin-right: 15px" />
                                <br/>
                                <label>®Password®:&nbsp;&nbsp;</label><input type="password" name="passwd" autocapitalize="off" autocorrect="off" tabindex="2" />
                            </div>

                            <select name="lang" style="width: 240px; padding: 0px; margin-bottom: 15px;" tabindex="3">
                                <option value="en">English</option>
                                <option value="fr" selected="selected">Français</option>
                            </select>
                            <br/>
                            <a style="font-size: 0.8em;" href="javascript:unsafe_connection();" title="®Unsafe_connection®">®Unencrypted_connection®</a>
                            <input type="submit" style="width: 100px; margin-left: 30px;" tabindex="4" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />

                        </form>
                </p>

            </div>

            <?php include 'div_footer.php'; ?>

        </div>

    </body>
</html>