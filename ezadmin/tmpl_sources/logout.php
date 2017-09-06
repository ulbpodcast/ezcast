<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

    <title>®ezadmin_page_title®</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
    <link  rel="stylesheet" href="css/style.css"/>
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

    </head>
    <body>
        <?php include 'div_header.php'; ?>
        <div id="global">
            <div id="login_form">
                ®Logout_confirmation_message®
            </div>

        </div>

        <?php include 'div_footer.php'; ?>
    </body>
</html>