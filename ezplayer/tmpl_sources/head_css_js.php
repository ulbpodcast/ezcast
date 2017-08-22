<link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
<link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
<link rel="stylesheet" type="text/css" href="css/ezplayer_style.css" />
<link rel="stylesheet" type="text/css" href="commons/css/common_style.css" />

<?php
    global $ezplayer_custom_css_filename;
    if($ezplayer_custom_css_filename !== false) {
        echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$ezplayer_custom_css_filename.'"/>';;
    }
?>
<link rel="stylesheet" type="text/css" href="css/smartphone.css" />
<script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>