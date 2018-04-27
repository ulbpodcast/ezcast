<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>EZmanager</title>
        <?php include_once template_getpath('head_css_js.php'); ?>
    </head>
    <body>
        <div class="container">
            <?php include 'div_main_header.php'; ?>
            <div id="global">
                <form action="index.php" method="post">
                    <input type="hidden" name="action" value="acceptTermsOfUses" />
                    <p><input type="submit"class="btn btn-primary btn-lg" value="J'accepte"></p>
                   </form>
            </div>
            
        </div>
    <?php include 'div_main_footer.php'; ?>
    </body>
</html>