<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>®podman_page_title®</title>

        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <link type="text/css" href="commons/css/common_style.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jQuery/jquery-1.7.2.min.js"></script>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />

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
                          ?>/index.php">
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
                        <input type="submit" style="width: 100px;" tabindex="4" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />

                </form>
                </p>

            </div>

        <?php include 'div_main_footer.php'; ?>

        </div>
    </body>
</html>
