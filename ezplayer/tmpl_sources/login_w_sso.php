<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
    <head>
        <title>EZplayer</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
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
        
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/ezplayer_style_v2.css" />
        <?php include_once template_getpath('head_css_js.php'); ?>


		<style>
			.btn {
				-moz-border-radius:12px;
				-webkit-border-radius:12px;
				border-radius:12px;
				display:inline-block;
				cursor:pointer;
				color:#ffffff;
				font-family:Arial;
				font-size:17px;
				padding:16px 31px;
				text-decoration:none;
				margin: 0px 20px 0px 20px;
			}
			.btn-sso {
				background-color:#44c767;
				border:1px solid #18ab29;
				text-shadow:0px 1px 0px #2f6627;	
			}
			.btn-sso:hover {
				background-color:#5cbf2a;
			}
			.btn-sso:active {
				position:relative;
				top:1px;
			}
			.btn-default {
				background-color:#c6c6c6;
				border:1px solid #aaaaaa;
				text-shadow:0px 1px 0px #2f6627;
			}
			.btn-default:hover {
				background-color:#bcbcbc;
			}
			.btn-default:active {
				position:relative;
				top:1px;
			}
			.btn-center {
				text-align: center;
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

            function login_server_trace(array) {
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?action=client_trace',
                        data: {info: array}
                    });
                return true;
            }
        </script>
        
        <script>
			$( document ).ready(function() {
				$("#btndefault").click(function () {
					$("#login_form").toggle('slow');
					return false;
				});
                $("#login_form").hide();
                <?php if (isset($error) && $error!=''){ ?>               
                    $("#login_form").show();             
                <?php } ?>                                                        
			});
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
            case 'opera' :
                if ($_SESSION['browser_version'] >= 26)
                    $warning = false;
                break;
            case 'firefox' :
                if (($_SESSION['browser_version'] >= 22 && ($_SESSION['user_os'] == "Windows" || $_SESSION['user_os'] == "Android"))
                        || $_SESSION['browser_version'] >= 35)
                    $warning = false;
                break;
        }
        if ($warning) {
            ?>
            <div id="warning">
                <div>
                    <a href="#" onclick="document.getElementById('warning').style.display = 'none';
                       ">&#215;</a> 
                    ®Warning_browser® :
                    <ul>
                        <li><b>Safari 5+</b> | </li>
                        <li><b>Google Chrome</b> | </li>
                        <li><b>Opera 26+</b> </li>
                        <?php if ($_SESSION['user_os'] == "Windows") { ?>
                            <li><b>Internet Explorer 9+</b> | </li>
                            <li><b>Firefox 22+</b></li>
                        <?php } ?>
                    </ul>
                </div>       
            </div>
        <?php } ?>
        <div class="container">
            <?php include 'div_help_header.php'; ?>
            <div id="global">
                <a id="news" onclick="login_server_trace(new Array('0', 'view_info'));" href='<?php global $ezplayer_url;
            echo $ezplayer_url . "/infos.php"; ?>' target='_blank'>
                <!--    <div>
                        <h3>Nouveauté EZplayer: Les discussions</h3>
                        <p>Découvrez les discussions et les interactions qu'elles permettent en cliquant sur ce ruban.</p>
                    </div>  -->     
                </a>
                <p>
                    <br />

                    <br />
                    
                    <div class="btn-center">
						<a class="btn btn-sso" href="<?php
                          global $ezplayer_safe_url;
                          echo $ezplayer_safe_url;
                          ?>/index.php?sso"  id="btnSSO" title="Authentification SSO">®authSSO®</a>
						<a class="btn btn-default" href="#" id="btndefault" title="">®authLocal®</a>
					</div>
					
                    <form id="login_form" method="post" style="display:none;" action="<?php
                        global $ezplayer_safe_url;
                        echo $ezplayer_safe_url;
                        ?>/index.php" onsubmit="detect_flash();">
                        <div style="color: red; font-weight: bold;"><?php echo $error; ?></div>
                        <input type="hidden" name="action" value="login" />
                        <input type="hidden" name="has_flash" value=""/>
                        <div id="login_fields">
                        <table>
                            <tr>
                                <td><label >®Netid®:&nbsp;&nbsp;</label></td>
                                <td><input type="text" name="login" autocapitalize="off" autocorrect="off" tabindex="1" style="margin-right: 15px;" /></td>
                            </tr>
                            <tr>
                                <td> <label>®Password®:&nbsp;&nbsp;</label>   </td>
                                <td> <input type="password" name="passwd" autocapitalize="off" autocorrect="off" tabindex="2" /> </td>                            
                           </tr>
                        </table>
                        </div>

                        <select name="lang" style="width: 242px; padding: 0px; margin-bottom: 15px;" tabindex="3">
                            <option value="en">English</option>
                            <option value="fr" selected="selected">Français</option>
                        </select>
                        <br/>
                        <input type="submit" name="logged_session" style="margin-left: 190px;;" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" tabindex="3"/>

                    </form>
                    
                    <div class="login_video col-md-12" style="text-align:center; padding-top:4em;">
						<h2>®tuto_ezplayer®</h2><br />
						<video id="tuto_video" width="720" controls="" type="video/mp4" src="./videos/tuto_fr.mp4" style="">
                        ®tuto_ezplayer®</video>
					</div>

                </p>

            </div>
            
 
<?php include 'div_main_footer.php'; ?>

        </div>
    </body>
</html>
