<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- 
    * EZCAST EZmanager 
    *
    * Copyright (C) 2014 Université libre de Bruxelles
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
        <title>EZcast - ULB Podcast</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <style type="text/css">
            .formulaire {
                font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                color: #114d91;
                border: 1px dotted #004D94;
                padding: 10px;
                width: 580px;
                margin-top: 30px;
            }

            .formulaire form .texte {
                background-color: #CCC;
            }

            body,td,th {
                font-family: Verdana, Geneva, sans-serif;
                color: #004C94;
                font-size: 12px;
                background-image: url(images/background1.gif);
            }

            .container {
                background-color: #FFF;
                width: 800px;
                text-align: center;
                margin-right: auto;
                margin-left: auto;
                padding: 0px;
                margin-top: 0px;
            }

            .contenu {
                text-align: left;
                border-right-width: 1px;
                border-left-width: 1px;
                border-right-style: solid;
                border-left-style: solid;
                border-right-color: #666;
                border-left-color: #666;
                padding-top: 0px;
                padding-right: 17px;
                padding-bottom: 17px;
                padding-left: 17px;
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #666;
            }

            body {
                margin-top: 0px;
            }

            form h3 {
                margin-top: 0px;
                padding-top: 0px;
                font-size: 12px;
            }
        </style>
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
                document.forms['login_form'].action = "<?php global $ezmanager_url;
echo $ezmanager_url; ?>/index.php";
                document.forms['login_form'].submit();
            }
        </script>
    </head>
    <body>
        <div class="container">

            <div class="contenu">
                <img src="images/podcast_logo.jpg" width="739" height="144" />
                <br />
                <h2>®service_name®</h2> <h4>®service_description®</h4>
                <div style="color: red;"><?php echo $error; ?></div>
                <br />
                <form name="login_form" action="<?php global $ezmanager_safe_url;
echo $ezmanager_safe_url; ?>/index.php" method="post" onsubmit="detect_flash();">
                    <input type="hidden" name="action" value="login" />
                    <input type="hidden" name="has_flash" value=""/>
                    <label>Netid :&nbsp;<input type="text" name="login"  autocapitalize="off" autocorrect="off" /></label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <label>Password :&nbsp;<input type="password" name="passwd" autocorrect="off" autocapitalize="off" /></label>
                    <br /><br />
                    <label><select name="lang">
                            <option value="en">English</option>
                            <option value="fr" selected="selected">Français</option>
                            <!-- <option value="nl">Nederlands</option> -->
                        </select></label>                                
                    &nbsp;&nbsp;&nbsp;<a style="font-size: 0.8em;" href="javascript:unsafe_connection();" title="®Unsafe_connection®">®Unencrypted_connection®</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <input type="submit" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />
                    <br />
                </form>
            </div>
        </div>
    </body>
</html>