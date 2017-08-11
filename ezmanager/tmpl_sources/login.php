<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>®podman_page_title®</title>
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
        */
        ?>
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
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
        </script>
    </head>
    <body>
        <div class="container">
            <?php include 'div_help_header.php'; ?>
            <div id="global">
                <p>
                    <br />
                    <?php if(isset($error) && $error != "") { 
                    echo '<div class="alert alert-danger col-md-4 col-md-offset-4" role="alert">';
                        echo $error;
                    echo '</div><br />';
                    } ?>
                    
                    <form id="login_form" class="form-horizontal col-md-6 col-md-offset-3" method="post" action="<?php
                          global $ezmanager_safe_url;
                          echo $ezmanager_safe_url;
                          ?>/index.php" onsubmit="detect_flash();">
                        
                        <input type="hidden" name="action" value="login" />
                        <input type="hidden" name="has_flash" value=""/>
                        
                        <div class="form-group">
                            <label for="netid" class="col-sm-3 control-label">®Login®</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="login" id="netid" autocapitalize="off" 
                                       autocorrect="off" placeholder="®Login®" tabindex="1" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="passwd" class="col-sm-3 control-label">®Password®</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="passwd" autocapitalize="off" 
                                       placeholder="®Password®" autocorrect="off" tabindex="2">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <select class="lang form-control" name="lang" tabindex="3" 
                                        onchange="document.location.href = './index.php?lang='+this.value;">
                                    <option value="en" <?php echo (isset($lang) && $lang == 'en') ? 'selected="selected"' : ''; ?>>
                                        English
                                    </option>
                                    <option value="fr" <?php echo (isset($lang) && $lang == 'fr') ? 'selected="selected"' : ''; ?>>
                                        Français
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <input type="submit" class="btn btn-default login" tabindex="4" value="®Connect®">
                            </div>
                        </div>
                </form>
                </p>

            </div>

            <?php include 'div_main_footer.php'; ?>
        </div>
    </body>
</html>