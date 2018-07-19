<link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
<link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="bootstrap/css/dashboard.css" />
<link rel="stylesheet" type="text/css" href="commons/css/common_style.css" />
<link href="css/uploadify.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="jQuery-DateTimePicker/jquery.simple-dtpicker.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styleHelp.css" />
<link rel="stylesheet" type="text/css" href="css/style_podman.css" />

<?php
    global $apache_documentroot;
    $custom_folder = "$apache_documentroot/ezmanager/css/custom/";
    $dir = new DirectoryIterator($custom_folder);
    foreach ($dir as $fileinfo) {
        if ($fileinfo->isFile()) {
            echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$fileinfo->getFilename().'"/>';
        }
    }
?>
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-2.2.4.min.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript" src="js/upload.js"></script>
        <script type="text/javascript" src="js/clipboard.js"></script>
        <script src="./js/highstock.js" async></script>        
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="jQuery-DateTimePicker/jquery.simple-dtpicker.js"></script>
        
