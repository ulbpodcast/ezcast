<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>®install_page_title®</title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/style.css" rel="stylesheet"/>
        <script type="text/javascript" src="./jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="./modernizr.custom.23345.js"></script>

    </head>

    <body link="#000088" vlink="#000044" alink="#0000ff">
        <div class="container_ezplayer">
            <?php include_once template_getpath("div_header.php"); ?>
            <div id="global">
            <h2 style="text-align: center; padding: 10px 0px;">®install_success_title®</h2>
            <div class="alert alert-success">Install successful. For improved security, we advise you to delete or rename the "install.php".</div>
            <br/>
            Before using EZcast and its components, create - at least - one renderer.<br/><br/>
            <ol>
                    <li>Connect as administrator in <a target="_blank" href="<?php global $ezadmin_url;
            echo $ezadmin_url; ?>">EZadmin</a></li>
                <li>Select "create renderer" in the menu, on the left</li>
                <li>Follow instruction on the screen</li>
            </ol>
            
            </div>
        <?php include_once template_getpath('div_footer.php'); ?>
        </div>
    </body>
</html>