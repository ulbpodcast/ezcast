<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- 
        * EZCAST EZmanager 
        *
        * Copyright (C) 2014 Université libre de Bruxelles
        *
        * Written by Michel Jansens <mjansens@ulb.ac.be>
        * 		    Arnaud Wijns <awijns@ulb.ac.be>
        *                   Antoine Dewilde
        * UI Design by Julien Di Pietrantonio
        *
        * This software is free software; you can redistribute it and/or
        * modify it under the terms of the GNU Lesser General Public
        * License as published by the Free Software Foundation; either
        * version 3 of the License, or (at your option) any later version.
        *
        * This software is distributed in the hope that it will be useful,
        * but WITHOUT ANY WARRANTY; without even the implied warranty of
        * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
        * Lesser General Public License for more details.
        *
        * You should have received a copy of the GNU Lesser General Public
        * License along with this software; if not, write to the Free Software
        * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


        Main template.
        This template is the main frame, the content divs are dynamically filled as the user clicks.

        WARNING: Please call template_repository_path() BEFORE including this template
        -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=8" />
        <title>®podman_page_title®</title>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/style_podman.css" />
        <link rel="stylesheet" type="text/css" href="css/colorbox.css" />
        <link href="css/uploadify.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="js/AppearDissapear.js"></script>
        <script type="text/javascript" src="js/hover.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="js/ZeroClipboard.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript" src="js/upload.js"></script>
        <script type="text/javascript">
            /**
             * Retrieves album header and displays it in div_album_header
             */
            var current_album = '<?php if (isset($_SESSION['podman_album'])) echo $_SESSION['podman_album']; ?>';
            ZeroClipboard.setMoviePath('./swf/ZeroClipboard10.swf');


            // Links an instance of clipboard to its position in the rss pop-up
            function copyToClipboard(album, tocopy) {
                var clip = new ZeroClipboard.Client();
                clip.setText('');
                clip.addEventListener('mouseDown', function() {
                    // window.alert("copy ok");        
                    clip.setText(tocopy);
                });
                clip.addEventListener('onComplete', function() {
                    alert("®Content_in_clipboard®");
                });

                // Set the text to copy in the clipboard
                clip.setText(tocopy);
                $(album).html(clip.getHTML(200, 30));
            }

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
                    x[i].onchange = x[i].onmouseout = function() {
                        this.relatedElement.value = this.value;
                    }
                }
            }

            function show_advanced_menu() {
                $('#dropdown_menu .submenu').toggle();
            }

            function is_touch_device() {
                return !!('ontouchstart' in window) // works on most browsers 
                        || !!('onmsgesturechange' in window); // works on ie10
            }
            ;

            function show_album_details(album) {
                // highlighting the current album, and removing the old one
                if (document.getElementById('album_' + current_album + '_clic')) {
                    document.getElementById('album_' + current_album + '_clic').style.display = 'none';
                    document.getElementById('album_' + current_album + '').style.display = '';
                }
                if (document.getElementById('album_' + album + '_clic')) {
                    document.getElementById('album_' + album + '_clic').style.display = '';
                    document.getElementById('album_' + album + '').style.display = 'none';
                }
                current_album = album;

                // Getting the content from the server, and filling the div_album_header with it
                document.getElementById('div_content').innerHTML = '<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>';
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
                makeRequest('index.php', '?action=edit_asset&album=' + album + '&asset=' + asset + '&title=' + title + '&description=' + description, 'asset_' + asset + '_details');

                // And finally we refresh the view
                document.getElementById('asset_' + asset + '_title').innerHTML = ' | ' + decodeURIComponent(title);
                document.getElementById('asset_' + asset + '_title_clic').innerHTML = ' | ' + decodeURIComponent(title);
            }

            function submit_upload_form() {
                //document.getElementById('title').disabled = true;
                //document.getElementById('type').disabled = true;
                //document.getElementById('keepQuality').disabled = true;
                //document.getElementById('description').disabled = true;
                document.getElementById('fileinputs_container').style.display = 'none';
                document.getElementById('loadingfile_label').style.display = 'none';
                document.getElementById('more_options_div').style.display = 'none';
                document.forms["submit_form"].submit();

                progressRate = 0;
                document.getElementById('progressbar_container').style.display = 'block';
                document.getElementById('progressbar').style.width = progressRate + '%';
                document.getElementById('submitButton').innerHTML = '®Upload_in_progress® (' + progressRate + '%)';

                check_upload_progress();
            }

            function check_form() {

                if (document.getElementById('title').value == '') {
                    window.alert('®No_title®');
                    return false;
                }
                var file = document.getElementById('loadingfile').value;
                if (file == '') {
                    window.alert('®No_file®');
                    return false;
                } else {
                    //        Regex doesn't work in IE
                    //        var ext = file.match(/^.+\.([^.]+)$/)[1];
                    var ext = file.split('.').pop();
                    var extensions = <?php
global $valid_extensions;
echo json_encode($valid_extensions);
?>;

                    // check if extension is accepted
                    var found = false;
                    for (var i = 0; i < extensions.length; i++) {
                        if (found = (extensions[i] == ext.toLowerCase()))
                            break;
                    }
                    if (!found) {
                        window.alert('®bad_extension®');
                        return false;
                    }
                }
                return true;
            }


            /**
             * This function is called by the uploader popup to update the progress bar
             */
            function check_upload_progress() {
                // Getting XHR instance
                xhr = false;

                if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                    xhr = new XMLHttpRequest();
                }
                else if (window.ActiveXObject) { // IE
                    try {
                        xhr = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        try {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch (e) {
                            // Who cares
                        }
                    }
                }

                if (!xhr) {
                    alert('Cannot create XHR instance');
                    return false;
                }

                if (xhr && xhr.readyState != 0) {
                    xhr.abort();
                }

                // Getting info about file
                var keyFile = document.getElementById('keyFile').value;
                console.log(keyFile);
                xhr.open('POST', 'index.php?action=get_upload_progress', true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send('keyFile=' + keyFile);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        if (xhr.responseText != 'false') {

                            // Parsing info about file
                            var response = eval('(' + xhr.responseText + ')');
                            var progressRate = Math.round(response.current / response.total * 100);

                            document.getElementById('progressbar_container').style.display = 'block';
                            document.getElementById('progressbar').style.width = progressRate + '%';
                            document.getElementById('submitButton').innerHTML = '®Upload_in_progress® (' + progressRate + '%)';

                            if (response.done == 1) {
                                //document.getElementById('submitButton').innerHTML = '®Upload_finished®';
                                document.getElementById('submit_media').innerHTML = '<h2>®Upload_finished_title®</h2>®Upload_finished®<br/><br/><br/><span class="Bouton"><a href="#" onclick="close_popup();"><span>®Close_and_return_to_index®</span></a></span>';
                                //document.getElementById('submit_media').style.height = 200+'px';
                                //$.colorbox.resize({height: document.getElementById('submit_media').height()});
                                //close_popup();
                            }
                            else {
                                setTimeout(check_upload_progress, 2000);
                            }
                        }
                        else {
                            setTimeout(check_upload_progress, 2000);
                        }
                    }
                };
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
        <div id="test"></div>

        <div class="container">
<?php include_once template_getpath('div_main_header.php'); ?>
            <div id="global">
                <!-- "New album" button -->
                <span class="CreerAlbum"><a href="javascript:show_popup_from_inner_div('#popup_new_album');">®Create_album®</a></span>
                <!-- <div class="button_new_album"> <a href="javascript:show_popup_from_inner_div('#popup_new_album')" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','images/page4/BCreerAlbum_<?php echo get_lang(); ?>.png',1)"><img src="images/page4/ACreerAlbum_<?php echo get_lang(); ?>.png" name="Image5" width="101" height="14" border="0" id="Image5" title="®Create_album®" /></a></div> -->
                <!-- "New album" button, END -->          

                <div id="div_center">

                    <!-- Left column: album list -->
                    <div id="div_album_list">
                        <!-- Album list goes here -->
<?php include_once template_getpath('div_album_list.php'); ?>
                    </div>
                    <!-- Left column: album list END -->

                    <!-- Right part of the screen: album and asset details -->
                    <div id="div_content">

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
<?php include_once 'popup_new_album.php'; ?>

                <!-- This popup gets automatically filled with messages, depending on the situation -->
                <div class="popup" id="popup_messages"></div>

                <!-- This popup gets automatically filled with errors -->
                <div class="popup" id="popup_errors"></div>
            </div>
        </div><!-- Container fin -->
    </body>
</html>
