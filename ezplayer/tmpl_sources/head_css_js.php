<link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
<link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
<link rel="stylesheet" type="text/css" href="css/ezplayer_style_v2.css" />

<?php
    global $custom_css_filename;
    if($custom_css_filename !== false) {
        echo '<link rel="stylesheet" type="text/css" href="css/custom/'.$custom_css_filename.'"/>';;
    }
    
    global $enable_css_phone;
    if ($enable_css_phone)
        echo '<link rel="stylesheet" type="text/css" href="css/smartphone.css" />';
?>

<script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>