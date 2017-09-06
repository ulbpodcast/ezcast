<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>®podman_page_title®</title>
        <?php include_once template_getpath('head_css_js.php'); ?>
    </head>
    <body>
        <div class="container">
            <?php include 'div_main_header.php'; ?>
            <div id="global">
                <p> 
                     <?php if($sso_enabled) { ?>
                    <div class="login-choices btn-center" >
                          <a class="btn-login btn-sso" href="<?php global $ezmanager_safe_url; echo $ezmanager_safe_url;
                          ?>/index.php?sso"  id="btnSSO" title="Authentification SSO">®authSSO®</a>
                          <a class="btn-login btn-login-default btn-default" href="#" id="default_auth_button" title="">®authLocal®</a>
                    </div>
                    <?php
} ?>
                    
                    <br />
                    
                    <?php if(isset($error) && $error != "") { 
                        echo '<div class="alert alert-danger col-md-4 col-md-offset-4" role="alert">';
                        echo $error;
                        echo '</div><br />';
                    } ?>
                    
                    <form id="form_login" class="form_login form-horizontal col-md-6 col-md-offset-3" method="post" action="<?php
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
                                    <option value="fr" <?php echo (isset($lang) && $lang == 'fr') ? 'selected="selected"' : ''; ?>>
                                        Français
                                    </option>
                                    <option value="en" <?php echo (isset($lang) && $lang == 'en') ? 'selected="selected"' : ''; ?>>
                                        English
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9 text-right">
                                <input type="submit" class="btn btn-default login" tabindex="4" value="®Connect®">
                            </div>
                        </div>
                </form>
                </p>
            </div>
            <?php include 'div_main_footer.php'; ?>
        </div>
        
        <script type="text/javascript">
            
            function detect_flash() {
                if ((navigator.mimeTypes ["application/x-shockwave-flash"] == undefined)) {
                    document.form_login.has_flash.value = 'N';
                }
                else {
                    document.form_login.has_flash.value = 'Y';
                }
            }
            <?php if ($sso_enabled) {
                              ?>
                $( document ).ready(function() {
                    $("#form_login").hide();
                    <?php if(isset($error) && $error != "") { ?>
                        $("#form_login").show();      
                    <?php } ?>
                    $("#default_auth_button").click(function () {
                        $("#form_login").slideToggle('fast');
                        return false;
                    });
                });
            <?php
                          } ?>
        </script>
    </body>
</html>