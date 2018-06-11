<link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
<link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
<link rel="stylesheet" type="text/css" href="css/ezplayer_style.css" />
<link rel="stylesheet" type="text/css" href="css/reveal.css" />
<link rel="stylesheet" type="text/css" href="commons/css/common_style.css" />

<?php
    global $apache_documentroot;
    $custom_folder = "$apache_documentroot/ezplayer/css/custom/";
    $dir = new DirectoryIterator($custom_folder);
    foreach ($dir as $fileinfo) {
        if ($fileinfo->isFile()) {
            echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$fileinfo->getFilename().'"/>';
        }
    }
        
    if($_SESSION['isPhone']){ ?>
        <link rel="stylesheet" type="text/css" href="css/smartphone.css" />
<?php } ?>	
<script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>
