<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>®ezadmin_page_title®</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" type="text/css" href="commons/css/common_style.css" />
                
        <?php
            global $apache_documentroot;
            $custom_folder = "$apache_documentroot/ezadmin/css/custom/";
            $dir = new DirectoryIterator($custom_folder);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile()) {
                    echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$fileinfo->getFilename().'"/>';
                }
            }
        ?>

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
            <?php include 'div_header.php'; ?>
            <div id="global">
                <p>
                    <br />

                    <br />

                    <?php if (isset($_SESSION['install'])) {
            ?>
                        <form id="login_form" name="login_form" action="install.php" method="post">
                        <?php
        } else {
            ?>
                            <form id="login_form" name="login_form" action="<?php
                            global $ezadmin_safe_url;
            echo $ezadmin_safe_url; ?>/index.php" method="post" onsubmit="detect_flash();">
                                  <?php
        } ?>
                                  <?php if (isset($_SESSION['install'])) {
            ?>
                                <h4>®install_description®</h4>
                            <?php
        } ?>
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
                            <input type="submit" style="width: 100px; margin-left: 30px;" tabindex="4" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" />

                        </form>
                </p>

            </div>

            <?php include 'div_footer.php'; ?>
    </body>
</html>
