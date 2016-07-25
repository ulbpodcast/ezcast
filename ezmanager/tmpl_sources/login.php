<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>®podman_page_title®</title>
        <!-- 
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
        -->
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />

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
global $ezmanager_url;
echo $ezmanager_url;
?>/index.php";
                document.forms['login_form'].submit();
            }
        </script>
    </head>
    <body>
        <div class="container">
<?php include 'div_help_header.php'; ?>
            <div id="global">
                <p>
                    <br />

                    <form id="login_form" method="post" action="<?php
                          global $ezmanager_safe_url;
                          echo $ezmanager_safe_url;
                          ?>/index.php" onsubmit="detect_flash();">
                        <div style="color: red; font-weight: bold;"><?php echo $error; ?></div>
                    <input type="hidden" name="action" value="login" />
                    <input type="hidden" name="has_flash" value=""/>
                        <div id="login_fields">
                            <label>®Login®:&nbsp;&nbsp;</label><input type="text" name="login" autocapitalize="off" autocorrect="off" tabindex="1" style="margin-right: 15px;" />
                            <br/>
                            <label>®Password®:&nbsp;&nbsp;</label><input type="password" name="passwd" autocapitalize="off" autocorrect="off" tabindex="2" />
                        </div>

                        <select name="lang" style="width: 235px; padding: 0px; margin-bottom: 15px;" tabindex="3">
                            <option value="en">English</option>
                            <option value="fr" selected="selected">Français</option>
                        </select>
                        <br/>
                        <a style="font-size: 0.8em;" href="javascript:unsafe_connection();" title="®Unsafe_connection®">®Unencrypted_connection®</a>
                        <input type="submit" style="width: 100px;" tabindex="4" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />

                </form>
                </p>

            </div>

<?php include 'div_main_footer.php'; ?>

        </div>
    </body>
</html>