<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <title>®podman_page_title®</title>
        <meta name="description" content="EZManager is an web application to manage video from EZCast" />
        <link rel="shortcut icon" type="image/ico" href="images/favicon.ico" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="bootstrap/css/dashboard.css" />
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <link rel="stylesheet" type="text/css" href="css/colorbox.css" />
        <link href="css/uploadify.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-2.2.4.min.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript" src="js/upload.js"></script>
        <script type="text/javascript" src="js/clipboard.js"></script>
        
        <script type="text/javascript" src="jQuery-DateTimePicker/jquery.simple-dtpicker.js"></script>
        <link type="text/css" href="jQuery-DateTimePicker/jquery.simple-dtpicker.css" rel="stylesheet" />
        <script type="text/javascript">
            /**
             * Retrieves album header and displays it in div_album_header
             */
            var current_album = '<?php if (isset($_SESSION['podman_album'])) echo $_SESSION['podman_album']; ?>';
            var tab = 'list';
            
            // Render a styled file input in the submit form
            function initFileUploads() {
                var W3CDOM = (document.createElement && document.getElementsByTagName
                        && navigator.appName != 'Microsoft Internet Explorer');
                if (!W3CDOM)
                    return;
                var fakeFileUpload = document.createElement('div');
                fakeFileUpload.className = 'fakefile';
                var input = document.createElement('input');
                input.style.width = '140px';
                fakeFileUpload.appendChild(input);
                var span = document.createElement('span');
                span.innerHTML = '®select®';
                fakeFileUpload.appendChild(span);
                var x = document.getElementsByTagName('input');
                for (var i = 0; i < x.length; i++) {
                    if (x[i].type != 'file')
                        continue;
                    if (x[i].parentNode.className != 'fileinputs')
                        continue;
                    x[i].className = 'file hidden';
                    var clone = fakeFileUpload.cloneNode(true);
                    x[i].parentNode.appendChild(clone);
                    x[i].relatedElement = clone.getElementsByTagName('input')[0];
                    x[i].onchange = x[i].onmouseout = function () {
                        this.relatedElement.value = this.value;
                    }
                }
            }

            function is_touch_device() {
                return !!('ontouchstart' in window) // works on most browsers 
                        || !!('onmsgesturechange' in window); // works on ie10
            }
            ;

            function refresh_album_view() {
                switch(tab) {
                    case 'stats':
                        show_stats_descriptives(current_album);
                        break;
                    
                    case 'url':
                        show_ezplayer_link(current_album);
                        break;
                    
                    case 'list':
                    default:
                        show_album_details(current_album);
                        break;
                }
            }

            function show_album_details(album) {
                $('#album_' + current_album).removeClass('active');
                $('#album_' + album).addClass('active');
                current_album = album;
                tab = 'list';

                // Getting the content from the server, and filling the div_album_header with it
                document.getElementById('div_content').innerHTML = '<div style="text-align: center;">' + 
                        '<img src="images/loading_white.gif" alt="loading..." /></div>';
                makeRequest('index.php', '?action=view_album&album=' + album, 'div_content');
            }

            function show_div(id) {
                var el = document.getElementById(id);
                el.style.display = (el.style.display != 'none' ? 'none' : '');
            }

            /**
             * This function is called whenever the user saves their change on an asset (Edit mode)
             */
            function edit_asset_data(album, asset) {
                // First we retrieve the data
                var unencoded_title = document.getElementById('title_' + asset + '_input').value;
                var title = encodeURIComponent(unencoded_title);
                var description = encodeURIComponent(document.getElementById('description_' + asset + '_input').value);

                if (unencoded_title.length > <?php
                    global $title_max_length;
                    echo $title_max_length;
                    ?>) {
                    window.alert('®Title_too_long®');
                    return false;
                }

                // Then we update them
                makeRequest('index.php', '?action=edit_asset&album=' + album + '&asset=' + asset + '&title=' + title + 
                        '&description=' + description, 'asset_' + asset + '_details');

                // And finally we refresh the view
                document.getElementById('asset_' + asset + '_title').innerHTML = ' | ' + decodeURIComponent(title);
                document.getElementById('asset_' + asset + '_title_clic').innerHTML = ' | ' + decodeURIComponent(title);
            }

            function asset_downloadable_set(album, asset) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=asset_downloadable_set',
                    data: {
                        'downloadable': $('#is_downloadable_' + asset).is(':checked') ? true : false,
                        'album': album,
                        'asset': asset
                    }
                });
            }
            
            function show_stats_descriptives(album) {
                tab = 'stats';
                document.getElementById('div_content').innerHTML = '<div style="text-align: center;">' + 
                        '<img src="images/loading_white.gif" alt="loading..." /></div>';
                makeRequest('index.php', '?action=view_stats&album=' + album, 'div_content');
            }
            
            function show_ezplayer_link(album) {
                tab = 'url';
                document.getElementById('div_content').innerHTML = '<div style="text-align: center;">' + 
                        '<img src="images/loading_white.gif" alt="loading..." /></div>';
                makeRequest('index.php', '?action=view_ezplayer_link&album=' + album, 'div_content');
            }

        </script>
        <script type="text/javascript" src="js/popup_general.js"></script>
        <script type="text/javascript" src="js/popup_callback.js"></script>
        <script type="text/javascript" src="js/show_details_functions.js"></script>


        <!-- Script Submit -->

        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/jquery.uploadify.v2.1.4.js"></script>
        <!-- End script submit -->

        <?php if (isset($head_code)) echo $head_code; ?>
    </head>
    <body>
        <div class="container">
            <?php include_once template_getpath('div_main_header.php'); ?>
            <div id="global" class="row">
                <!-- "New album" button -->
                <div class="col-md-12 btn-new-album">
                    <a class="btn btn-default" type="button" href="index.php?action=show_popup&amp;popup=new_album"
                       data-remote="false" data-toggle="modal" data-target="#modal" >
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        ®Create_album®
                    </a>
                </div>
                <!-- <div class="button_new_album"> <a href="javascript:show_popup_from_inner_div('#popup_new_album')" 
                    onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','images/page4/BCreerAlbum_<?php 
                    // echo get_lang(); ?>.png',1)"><img src="images/page4/ACreerAlbum_<?php // echo get_lang(); ?>.png" name="Image5" 
                    width="101" height="14" border="0" id="Image5" title="®Create_album®" /></a></div> -->
                <!-- "New album" button, END -->          

                <div id="div_center" class="col-md-12">

                    <!-- Album list goes here -->
                    <?php include_once template_getpath('div_album_list.php'); ?>
                    <!-- Left column: album list END -->

                    <!-- Right part of the screen: album and asset details -->
                    <div id="div_content" class="col-md-8">

                        <!-- Album details go here (dynamically filled with div_album_header) -->
                        <?php
                        // If we are in redraw mode, we fill the content of the div
                        if ($redraw && isset($current_album)) {
                            require 'div_album_header.php';
                        }
                        ?>

                        <!-- Asset list goes here (dynamically filled with div_asset_list) -->
                        <?php
                        // If we are in redraw mode, we fill the content of the div
                        if ($redraw && isset($current_album)) {
                            require 'div_asset_list.php';
                        }
                        ?>
                    </div><!-- div_content END -->
                    <div id="spacer"></div>
                </div><!-- center div END -->
            </div><!-- global -->
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->

            <!-- Popups -->
            <div style="display: none;">
                <!-- This popup gets automatically filled with messages, depending on the situation -->
                <div class="popup" id="popup_messages"></div>

                <!-- This popup gets automatically filled with errors -->
                <div class="popup" id="popup_errors"></div>
            </div>
            
            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <img src="images/loading_white.gif" alt="loading..." /></div>
                  </div>
            </div>
        </div> <!-- Container fin -->
        <script>
        $("#modal").on("show.bs.modal", function(e) {
            var link = $(e.relatedTarget);
            display_bootstrap_modal($(this), link);
        });
        function display_bootstrap_modal(modal, button) {
            modal.find(".modal-content").load(button.attr("href"));
        }
        </script>
    </body>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</html>
