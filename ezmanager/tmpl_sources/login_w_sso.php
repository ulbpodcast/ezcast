<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>®podman_page_title®</title>
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
            #login_form{
                margin: auto; !important;
                width: 300px; !important;
            }
		</style>

        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <script type="text/javascript" src="js/jQuery/jquery-1.7.2.min.js"></script>
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
        <div class="container">
<?php include 'div_help_header.php'; ?>
            <div id="global">
                <p>
                    <br />

                    <div class="btn-center">
						<a class="btn btn-sso" href="<?php
                          global $ezmanager_safe_url;
                          echo $ezmanager_safe_url;
                          ?>/index.php?sso"  id="btnSSO" title="Authentification SSO">Authentification SSO</a>
						<a class="btn btn-default" href="#" id="btndefault" title="">Authentification locale</a>
					</div>
					
                    <form id="login_form" method="post" style="display:none;" action="<?php
                          global $ezmanager_safe_url;
                          echo $ezmanager_safe_url;
                          ?>/index.php" onsubmit="detect_flash();">
                        <div style="color: red; font-weight: bold;"><?php echo $error; ?></div>
                    <input type="hidden" name="action" value="login" />
                    <input type="hidden" name="has_flash" value=""/>
                        <div id="login_fields">
                        <table>
                            <tr>
                                <td><label>®Login®:&nbsp;&nbsp;</label> </td>
                                <td> <input type="text" name="login" autocapitalize="off" autocorrect="off" tabindex="1" style="margin-right: 15px;" /></td>
                            </tr>
                            <tr>
                                <td><label>®Password®:&nbsp;&nbsp;</label></td>
                                <td><input type="password" name="passwd" autocapitalize="off" autocorrect="off" tabindex="2" /></td>
                            </tr>
                        </table>
                            
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
