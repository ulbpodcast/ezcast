<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- 
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
-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
    <head>
        <title>EZplayer</title>
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
                margin: 0px;
                padding: 0px;
            }

            form h3 {
                margin-top: 0px;
                padding-top: 0px;
                font-size: 12px;
            }
            #warning {
                position: fixed; 
                width: 100%; 
                background: #FFD7D7; 
                color: #7C0505; 
                text-align: center
            }
            #warning div{
                width: 934px; 
                margin: 0 auto; 
                position: relative; 
                padding: 16px; 
            }
            #warning ul li{
                display: inline;
            }
            #warning a{
                display: block;
                padding: 0px 3px;
                background: #7C0505;
                text-align: center;
                color: #FFD7D7; 
                position: absolute;
                right: 0px;
                -moz-border-radius: 8px;
                -webkit-border-radius: 8px;
                border-radius: 8px;
                text-decoration:  none;
            }
        </style>
        <script type="text/javascript"> 
            function detect_flash(){
                if ((navigator.mimeTypes ["application/x-shockwave-flash"] == undefined)){
                    document.login_form.has_flash.value='N';         
                }
                else {
                    document.login_form.has_flash.value='Y'; 
                }
            }  
        </script>
    </head>
    <body>
        <?php
        $warning = true;
        switch (strtolower($_SESSION['browser_name'])) {
            case 'safari' :
                if ($_SESSION['browser_version'] >= 5)
                    $warning = false;
                break;
            case 'chrome' :
                if ($_SESSION['browser_version'] >= 4)
                    $warning = false;
                break;
            case 'internet explorer' :
                if ($_SESSION['browser_version'] >= 9)
                    $warning = false;
                break;
            case 'firefox' :
                if ($_SESSION['browser_version'] >= 22 
                        && ($_SESSION['user_os'] == "Windows" || $_SESSION['user_os'] == "Android"))
                    $warning = false;
                break;
        }
        if ($warning) {
            ?>
            <div id="warning">
                <div>
                    <a href="#" onclick="document.getElementById('warning').style.display='none'; ">&#215;</a> 
                     ®Warning_browser® :
                    <ul>
                        <li><b>Safari 5+</b> | </li>
                        <li><b>Google Chrome</b> | </li>
                        <?php if ($_SESSION['user_os'] == "Windows") {?>
                        <li><b>Internet Explorer 9+</b> | </li>
                        <li><b>Firefox 22+</b></li>
                        <?php } ?>
                    </ul>
                </div>       
            </div>
<?php } ?>
        <div class="container">

            <div class="contenu">
                <img src="images/podcast_logo.jpg" width="739" height="144" />
                <br />
                <h2>®service_name®</h2> <h4>®service_description®</h4>
                <div style="color: red;"><?php echo $error; ?></div>
                <br />
                <form name="login_form" action="<?php global $ezplayer_safe_url; echo $ezplayer_safe_url; ?>/index.php" method="post" onsubmit="detect_flash();">
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

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="submit" name="logged_session" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />
                    <input type="submit" name="anonymous_session" value="®No_authentication®" />
                    <br />
                </form>
            </div>
        </div>
    </body>
</html>