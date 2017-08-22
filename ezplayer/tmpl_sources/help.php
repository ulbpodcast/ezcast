<!DOCTYPE html>

<!--
 This page is meant to contain a FAQ/tutorial on how to use the service
-->

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>®ezplayer_page_title®</title>
        <?php include_once template_getpath('head_css_js.php'); ?>
        <script>
            $(document).ready(function() {
                $('#topics li a').click(function() {
                    $(this).siblings().toggle(200);
                });
            });
        </script>
    </head>
    <body>
        <div class="container">
            <?php include_once template_getpath('div_help_header.php'); ?>
            <div id="global">
                <div id="div_center">
                    <?php
                    include_once template_getpath('div_help_center.php');
                    ?>
                </div><!-- div_center END -->
            </div><!-- global -->
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
        </div><!-- Container fin -->
    </body>
</html>
