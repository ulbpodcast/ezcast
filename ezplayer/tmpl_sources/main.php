<!doctype html>
<?php
/* Main template.
 * This template is the main frame, the content divs are dynamically filled as the user clicks.
 *
 * WARNING: Please call template_repository_path() BEFORE including this template
 */
?>
<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
        <title>®ezplayer_page_title®</title>
        <meta name="description" content="EZPlayer is a video player to view EZCast video" />
        <?php include_once template_getpath('head_css_js.php'); ?>
        
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="#EZPlayer" />
        <meta name="twitter:title" content="EZPlayer (from EZCast)" /> <!-- Personalize with translation -->
        <meta name="twitter:description" content="EZPlayer is propulsed by EZCast (ULB)" />
        <meta name="twitter:image" content="./images/Header/LogoEZplayer.png" />
    
        <script>
<?php
global $trace_on;
if ($trace_on) {
    ?>
                var trace_on = true;
<?php
} else {
        ?>
                var trace_on = false;
<?php
    } ?>
        </script>
        <script type="text/javascript" src="lib/tinymce/tinymce.min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-2.1.3.min.js"></script>
        <?php
        global $streaming_video_player;
        switch ($streaming_video_player) {
            case 'momovi':
                ?>
                <script src="momovi/static/js/video.js"></script>
                <script src="momovi/static/js/jquery.ba-throttle-debounce.min.js"></script>
                <?php
                break;
            case 'flowplayer':
                ?>
                <link rel="stylesheet" href="flowplayer/skin/skin.css">
                <script src="flowplayer/flowplayer.min.js"></script>
                <script src="flowplayer/hls.js"></script>
                <?php
                break;
        }
        ?>
        <script type="text/javascript" src="js/httpRequest.js"></script>            
        <script type="text/javascript" src="js/jQuery/jquery.scrollTo-1.4.3.1-min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.localscroll-1.2.7-min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.reveal.js"></script>
        <script type="text/javascript" src="js/jQuery/highlight-js.js"></script>
        <?php global $video_split_time;
        echo '<script>var video_split_time = '.$video_split_time . '</script>'; ?>
        <script type="text/javascript" src="js/lib_player.js"></script>
        <script type="text/javascript" src="js/lib_threads.js"></script>
        <script type="text/javascript" src="js/lib_bookmarks.js"></script>
        <script type="text/javascript" src="js/lib_chat.js"></script>
        <script type="text/javascript" src="js/clipboard.js"></script>

        <script>
            var current_album;
            var current_asset;
            var current_token;
            var current_tab;
            var clippy;
            var ie_browser = false;
            var threads_array = new Array();
            var display_thread_details = false;
            var display_threads_notif = false;
            var thread_to_display = null;
            var ezplayer_mode = '<?php echo $_SESSION['ezplayer_mode']; ?>';
            
            $(document).ready(function () {

                $('#assets_button, .bookmarks_button, .toc_button').localScroll({
                    target: '#side_pane',
                    axis: 'x'
                });
                // import/export menu closes when click outside
                $("*", document.body).click(function (e) {
                    if ((e.target.id != "bookmarks_actions") && !$(e.target).hasClass("menu-button") && 
                            ($("#bookmarks_actions").css("display") != "none")) {
                        $("#bookmarks_actions").css("display", "none");
                        $(".settings.bookmarks a.menu-button").toggleClass('active')
                    } else if ((e.target.id != "tocs_actions") && !$(e.target).hasClass("menu-button") && 
                            ($("#tocs_actions").css("display") != "none")) {
                        $("#tocs_actions").css("display", "none");
                        $(".settings.toc a.menu-button").toggleClass('active')
                    }
                });

                // settings checkboxes 
                if (!$("input[name='display_threads']").is(':checked')) {
                    $('#settings_notif_threads').removeAttr('checked');
                    $('#settings_notif_threads').attr("disabled", "disabled");
                }
                $("input[name='display_threads']").change(function () {

                    if ($(this).is(':checked')) {
                        $('#settings_notif_threads').removeAttr('disabled');
                    } else {
                        $('#settings_notif_threads').removeAttr('checked');
                        $('#settings_notif_threads').attr("disabled", "disabled");
                    }

                });

                // history handler for back/next buttons of the browser
                window.onpopstate = function (event) {
                    if (event.state !== null) {
                        var state = jQuery.parseJSON(JSON.stringify(event.state));
                        window.location = state.url;
                    }
                };
            });

            // ============== N A V I G A T I O N ================ //

            /**
             * Navigates to assets list
             * @param {type} album
             * @param {type} asset
             * @param {type} timecode
             * @param {type} type
             * @returns {undefined}
             */
            function show_album_assets(album, token) {
                // the side pane changes to display the list of all assets contained in the selected album
                current_album = album;
                current_token = token;

                // Getting the content from the server, and filling the div_album_header with it
                document.getElementById('div_center').innerHTML = '<div style="text-align: center;">' +
                        '<img src="images/loading_white.gif" alt="loading..." /></div>';
                tinymce.remove();
                makeRequest('index.php', '?action=view_album_assets&album=' + album + '&token=' + token + '&click=true', 'div_center');
                // history.pushState({"key": "show-album-assets", "function": "show_album_assets(" + album + "," + token + ")", 
                //      "url": "index.php?action=view_album_assets&album=" + album + "&token=" + token}, 'album-details', 
                //      'index.php?action=view_album_assets');
            }

            /**
             * Navigates to the given asset
             * @param {type} album
             * @param {type} asset
             * @param {type} asset_token
             * @returns {undefined}
             */
            function show_asset_details(album, asset, asset_token) {
                current_album = album;
                current_asset = asset;
                display_thread_details = false;

                makeRequest('index.php', '?action=view_asset_details&album=' + album + '&asset=' + asset + '&asset_token=' + 
                        asset_token + '&click=true', 'div_center');
                //   history.pushState({"key": "show-asset-details", "function": "show_asset_details(" + album + "," + 
                //      asset + "," + asset_token + ")", "url": "index.php?action=view_asset_details&album=" + album + 
                //      "&asset=" + asset + "&asset_token=" + asset_token}, 'asset-details', 'index.php?action=view_asset_details');
            }

            /**
             * Navigates to the given asset
             * @param {type} album
             * @param {type} asset
             * @param {type} asset_token
             * @returns {undefined}
             */
            function show_asset_streaming(album, asset, asset_token) {
                current_album = album;
                current_asset = asset;
                display_thread_details = false;

                makeRequest('index.php', '?action=view_asset_streaming&album=' + album + '&asset=' + asset + '&asset_token=' + 
                        asset_token + '&click=true', 'div_center');
                //   history.pushState({"key": "show-asset-details", "function": "show_asset_details(" + album + "," + 
                //      asset + "," + asset_token + ")", "url": "index.php?action=view_asset_details&album=" + album + 
                //      "&asset=" + asset + "&asset_token=" + asset_token}, 'asset-details', 'index.php?action=view_asset_details');
            }

            /**
             * Navigates to the given thread (from trending threads)
             * @param {type} threadId
             * @returns {Boolean}
             */
            function show_thread(album, asset, timecode, threadId, commentId) {
                if (album != null && asset != null) {
                    current_album = album;
                    current_asset = asset;
                }
                if (typeof fullscreen != 'undefined' && fullscreen) {
                    video_fullscreen(false);
                }
                if (ezplayer_mode == 'view_asset_streaming')
                    player_kill();

                server_trace(new Array('2', 'thread_detail_from_trending', current_album, current_asset, timecode, threadId));
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=view_asset_bookmark',
                    data: 'album=' + album + '&asset=' + asset + "&t=" + timecode + "&thread_id=" + threadId + "&click=true",
                    success: function (response) {
                        $('#div_center').html(response);
                        if (commentId != '') {
                            $.scrollTo('#comment_' + commentId);
                        } else {
                            $.scrollTo('#threads');
                        }
                    }
                });
                close_popup();
            }

            /**
             * Displays the detail of a thread (from threads list)
             * @returns {Boolean}
             */
            function show_thread_details(event, thread_id) {
                if ($(event.target).is('a') || $(event.target).is('span.timecode'))
                    return;

                server_trace(new Array('3', 'thread_detail_show', current_album, current_asset, thread_id));
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=view_thread_details&click=true',
                    data: {'thread_id': thread_id},
                    success: function (response) {
                        $('#threads').html(response);
                        tinymce.remove('textarea');
                    }
                });
            }

            /**
             * Navigates to the given bookmark
             * @param {type} threadId
             * @returns {Boolean}
             */
            function show_asset_bookmark(album, asset, timecode, type) {
                current_album = album;
                current_asset = asset;

                if (ezplayer_mode == 'view_asset_streaming')
                    player_kill();

                makeRequest('index.php', '?action=view_asset_bookmark&album=' + album + '&asset=' + asset + '&t=' + 
                        timecode + '&type=' + type + '&click=true', 'div_center');
                close_popup();
            }


            // ================ S E A R C H ================== //

            /**
             * Modifies the display of the search form
             * @returns {Boolean}
             */
            function search_form_setup() {
                if ($('#album_radio').is(':checked')) {
                    $('.search_current').hide();
                    $('.search_albums').show();
                } else if ($('#current_radio').is(':checked')) {
                    $('.search_albums').hide();
                    $('.search_current').show();
                } else {
                    $('.search_albums').hide();
                    $('.search_current').hide();
                }
            }

            /**
             * Verifies the search form before submitting it
             * @returns {Boolean}
             */
            function search_form_check() {
                var search_words = $('#main_search').val();
                if (typeof search_words != 'undefined') {
                    if (search_words.trim() == '') {
                        return false;
                    }
                    search_form_submit();
                }
            }

            /**
             * Adjusts the search options according to the selected fields
             * (bookmarks | threads)
             * @returns {Boolean}
             */
            function search_options_adjust() {
                if ($('#cb_toc').is(':checked') || $('#cb_bookmark').is(':checked')) {
                    $('#search_bookmarks').removeClass('hidden');
                } else {
                    $('#search_bookmarks').addClass('hidden');
                }
                if ($('#cb_threads').is(':checked')) {
                    $('#search_threads').removeClass('hidden');
                } else {
                    $('#search_threads').addClass('hidden');
                }
            }

            /**
             * Submits the search form to the server
             * @param {type} index
             * @param {type} tab
             * @returns {Boolean}
             */
            function search_form_submit() {
                $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=threads_bookmarks_search&click=true&origin=bookmarks',
                    data: $('#search_form').serialize(),
                    success: function (response) {
                        $('#div_popup').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'div_popup');  

                $('#div_popup').reveal($(this).data());
            }

            /**
             * Submits the keyword to be searched to the server
             * @returns {Boolean}
             */
            function keyword_search(keyword) {
                $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=threads_bookmarks_search&click=true&origin=keyword',
                    data: 'search=' + keyword + '&target=global&albums%5B%5D=&fields%5B%5D=keywords&tab%5B%5D=official&tab%5B%5D=custom&level=0',
                    success: function (response) {
                        $('#div_popup').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'div_popup');  

                $('#div_popup').reveal($(this).data());
            }

            // ============== A L B U M S ============== //

            /**
             * Deletes an album from the home page
             * @param {type} album
             * @returns {undefined}
             */
            function album_token_delete(album) {
                makeRequest('index.php', '?action=album_token_delete' +
                        '&album=' + album +
                        '&click=true', 'div_center');
                close_popup();
            }

            /**
             * Sets the album position (up/down)
             */
            function album_token_move(album, index, upDown) {
                makeRequest('index.php', '?action=album_token_move' +
                        '&album=' + album + '&index=' + index + '&up_down=' + upDown + "&click=true", 'div_center');
            }

            // ============== F O R M   V A L I D A T I O N ============= //

            // -------------- B O O K M A R K S ------------- //

            /**
             * checks the bookmark creation form before submitting it
             * @returns {Boolean}
             */
            function bookmark_form_check() {
                var timecode = document.getElementById('bookmark_timecode');
                var level = document.getElementById('bookmark_level');
                var bookmark_source = document.getElementById('bookmark_source');
                
                if (isNaN(timecode.value)
                        || timecode.value == ''
                        || timecode.value < 0) {
                    window.alert('®Bad_timecode®');
                    return false;
                }
                
                var liste = (bookmark_source.value === 'official') ? official_bookmarks_time_code : personal_bookmarks_time_code;
                
                if($.inArray(parseInt(timecode.value), liste) >= 0) {
                    window.alert("®already_use_timecode®");
                    return false;
                }
                    

                if (isNaN(level.value)
                        || level.value < 1
                        || level.value > 3) {
                    window.alert('®Bad_level®');
                    return false;
                }
                return true;
            }

            /**
             * checks the bookmark edition form before submitting it
             * @returns {Boolean}
             */
            function bookmark_edit_form_check(index, tab) {
                var timecode = document.getElementById(tab + '_timecode_' + index);
                var level = document.getElementById(tab + '_level_' + index);

                if (timecode.value == ''
                        || timecode.value < 0) {
                    window.alert('®Bad_timecode®');
                    return false;
                }

                if (isNaN(level.value)
                        || level.value < 1
                        || level.value > 3) {
                    window.alert('®Bad_level®');
                    return false;
                }
                return true;
            }

            /**
             * Checks the xml file containing bookmarks
             * @returns {Boolean}
             */
            function bookmarks_upload_form_check() {
                var file = document.getElementById('loadingfile').value;
                if (file == '') {
                    window.alert('®No_file®');
                    return false;
                } else {
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

            // -------------- T H R E A D S ------------- //

            /**
             * Checks the thread creation form 
             * @returns {undefined}             
             */
            function thread_form_check() {

                document.getElementById('thread_desc_tinymce').value = tinymce.get('thread_desc_tinymce').getContent();
                var timecode = document.getElementById('thread_timecode');
                var message = document.getElementById('thread_desc_tinymce').value;
                var title = document.getElementById('thread_title').value;

                if (isNaN(timecode.value)
                        || timecode.value == ''
                        || timecode.value < 0) {
                    window.alert('®Bad_timecode®');
                    return false;
                }
                if (message === '') {
                    window.alert('®missing_message®');
                    return false;
                }
                if (title === '') {
                    window.alert('®missing_title®');
                    return false;
                }
                return true;
            }

            /**
             * Checks the thread edition form
             * @param {type} id
             * @returns {undefined}             */
            function thread_edit_form_check(threadId) {
                $("#edit_thread_message_" + threadId + "_tinyeditor").html(tinymce.get("edit_thread_message_" + 
                        threadId + "_tinyeditor").getContent());
                var message = document.getElementById("edit_thread_message_" + threadId + "_tinyeditor").value;
                var title = document.getElementById('edit_thread_title_' + threadId).value;
                if (message === '') {
                    window.alert('®missing_message®');
                    return false;
                }
                if (title === '') {
                    window.alert('®missing_title®');
                    return false;
                }
                return true;
            }

            /**
             * Checks the comment creation form
             * @param {type} id
             * @returns {undefined}             */
            function thread_comment_form_check() {

                $('#comment_message_tinyeditor').html(tinymce.get('comment_message_tinyeditor').getContent());
                var message = document.getElementById('comment_message_tinyeditor').value;
                if (message == '') {
                    window.alert('®missing_message®');
                    return false;
                }
                return true;
            }

            /**
             * Checks the comment reply form
             * @param {type} event
             * @param {type} thread_id
             * @returns {undefined}             */
            function comment_answer_form_check(id) {
                $('#answer_comment_message_' + id + '_tinyeditor').html(tinymce.get('answer_comment_message_' + id + '_tinyeditor').getContent());
                var message = document.getElementById('answer_comment_message_' + id + '_tinyeditor').value;

                if (message == '') {
                    window.alert('®missing_message®');
                    return false;
                }
                return true;
            }

            // =============== A D M I N   M O D E  ,  C O N T A C T   &   P R E F E R E N C E S ============== //

            /**
             * Enables/Disables admin mode
             * @type 
             */
            function admin_mode_update() {
                // creates a form 
                var form = document.createElement("form");
                form.setAttribute("method", 'post');
                form.setAttribute("action", 'index.php');

                // adds a hidden field containing the action
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", 'action');
                hiddenField.setAttribute("value", 'admin_mode_update');

                form.appendChild(hiddenField);

                // submits the form
                document.body.appendChild(form);
                form.submit();
            }

            // shows the settings / contact form
            function header_form_show(src) {
                switch (src) {
                    case 'settings':
                        $('#settings_form').slideDown();
                        $('#user-settings').addClass('active');
                        settings_form = true;
                        break;
                    case 'contact':
                        $('#contact_form').slideDown();
                        $('#contact').addClass('active');
                        contact_form = true;
                        break;
                }
            }

            // hides the settings /contact form
            function header_form_hide(src) {
                switch (src) {
                    case 'settings':
                        $('#settings_form').slideUp();
                        $('#user-settings').removeClass('active');
                        settings_form = false;
                        break;
                    case 'contact':
                        $('#contact_form').slideUp();
                        $('#contact').removeClass('active');
                        contact_form = false;
                        break;
                }
            }
            // shows/hides the settings / contact form
            function header_form_toggle(src) {
                var show_hide;
                if ((settings_form && src == "settings") || (contact_form && src == "contact")) {
                    show_hide = '_hide';
                    header_form_hide(src);
                } else {
                    show_hide = '_show';
                    header_form_show(src);
                }
                server_trace(new Array('4', src + show_hide, current_album, current_asset));
            }

            // =============== P O P - U P  ================ //

            /**
             * Renders a modal window with a message related to an album
             * @param {type} display the action to be shown in the modal window (delete | rss | ...)
             * @returns {undefined}  
             */
            function popup_album(album, display) {
                $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=album_popup&click=true',
                    data: 'album=' + album + '&display=' + display,
                    success: function (response) {
                        $('#div_popup').html(response);
                    }
                });
                $('#div_popup').reveal($(this).data());
            }

            /**
             * Renders a modal window with a message related to an asset
             * @param {type} display the action to be shown in the modal window (share_link | share_time | ...)
             * @returns {undefined}             */
            function popup_asset(album, asset, currentTime, type, display) {
                $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=asset_popup&click=true',
                    data: 'album=' + album + '&asset=' + asset + '&time=' + currentTime + '&type=' + type + '&display=' + display,
                    success: function (response) {
                        $('#div_popup').html(response);
                    }
                });
                $('#div_popup').reveal($(this).data());
            }

            // Closes the modal window being displayed
            function close_popup() {
                var e = jQuery.Event("click");
                $(".reveal-modal-bg").trigger(e); // trigger it on document
            }

            // =============== V A R I O U S ================= //

            function nl2br(str, is_xhtml) {
                var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
                return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
            }

            function toggle(elem) {
                $(elem).toggle(200);
            }

            // checks/unchecks all checkboxes
            function toggle_checkboxes(source, target) {
                var checkboxes = document.getElementsByName(target);
                for (var i = 0; i < checkboxes.length; i++)
                    checkboxes[i].checked = source.checked;
            }

            // scrolls to the given component
            function scrollTo(component) {
                if (typeof $('#' + component)[0] != 'undefined')
                    $('#' + component)[0].scrollIntoView(true);
            }

            // sends an array to the server containing the action trace to be saved
            function server_trace(array) {
                if (trace_on) { // from main.php
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?action=client_trace',
                        data: {info: array}
                    });
                }
                return true;
            }

            // determines whether the action has been triggered from a button or a keyboard shortcut
            function get_origin() {
                if (from_shortcut) { // a key has been pressed to run the action
                    from_shortcut = false;
                    return "from_shortcut";
                } else {
                    return "from_button";
                }
            }

            // determines whether official or personal bookmarks tab is active
            function setActivePane(elem) {
                if (elem == '.bookmarks_button') {
                    $('.settings.bookmarks').show();
                    $('.settings.toc').hide();
                    current_tab = 'main';
                } else {
                    $('.settings.bookmarks').hide();
                    $('.settings.toc').show();
                    current_tab = 'toc';
                }
                $('.bookmarks_button').removeClass("active");
                $('.toc_button').removeClass("active");
                $(elem).addClass("active");
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
                    x[i].onchange = x[i].onmouseout = function () {
                        this.relatedElement.value = this.value;
                    }
                }
            }


        </script>

        <?php if (isset($head_code)) {
            echo $head_code;
        } ?>
    </head>
    <body>
        <?php
        // Displays a warning message if the brower is not fully supported
        $warning = true;
        switch (strtolower($_SESSION['browser_name'])) {
            case 'safari':
                if ($_SESSION['browser_version'] >= 5) {
                    $warning = false;
                }
                break;
            case 'chrome':
                if ($_SESSION['browser_version'] >= 4) {
                    $warning = false;
                }
                break;
            case 'internet explorer':
                if ($_SESSION['browser_version'] >= 9) {
                    $warning = false;
                }
                break;
            case 'opera':
                if ($_SESSION['browser_version'] >= 26) {
                    $warning = false;
                }
                break;
            case 'firefox':
                if (($_SESSION['browser_version'] >= 22 && ($_SESSION['user_os'] == "Windows" ||
                        $_SESSION['user_os'] == "Android")) || $_SESSION['browser_version'] >= 35) {
                    $warning = false;
                }
                break;
        }
        if ($warning) {
            ?>
            <div id="warning">
                <div>
                    <a href="#" onclick="document.getElementById('warning').style.display = 'none';
                       ">&#215;</a> 
                    ®Warning_browser® :
                    <ul>
                        <li><b>Safari 5+</b> | </li>
                        <li><b>Google Chrome</b> | </li>
                        <li><b>Opera 26+</b> </li>
                        <?php if ($_SESSION['user_os'] == "Windows") {
                ?>
                            <li> | <b>Internet Explorer 9+</b> | </li>
                            <li><b>Firefox 22+</b></li>
    <?php
            } ?>
                    </ul>
                </div>       
            </div>
                <?php
        } ?>
        <div class="container">
            <div id="header_wrapper">
<?php include_once template_getpath('div_main_header.php'); ?>
            </div>
            <div id="global">
                <div id="div_center">
                    <?php
                    if (isset($error_path) && !empty($error_path)) {
                        include_once $error_path;
                    } elseif ($_SESSION['ezplayer_mode'] == 'view_main') {
                        include_once template_getpath('div_main_center.php');
                    } elseif ($_SESSION['ezplayer_mode'] == 'view_asset_streaming') {
                        include_once template_getpath("div_streaming_center.php");
                    } else {
                        include_once template_getpath('div_assets_center.php');
                    }
                    ?>
                </div><!-- div_center END -->
            </div><!-- global -->

            <?php
            if ($_SESSION["show_message"]) {
                include_once template_getpath('popup_message_of_day.php'); ?>
                <script>
                    $('#popup_message_of_day').reveal($(this).data());
                </script>           
            <?php
            } ?>
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
        </div><!-- Container fin -->

        <div class="reveal-modal-bg"></div>         

        <!-- Popup are generated on demand and included in this div -->
        <div id="div_popup" class="reveal-modal"></div>

    </body>
    <!-- scripts that must be loaded after document -->
    <script>
         var clipboard = new Clipboard('.clipboard');

        clipboard.on('success', function(e) {
            alert("®Content_in_clipboard®");
            //todo: proper tooltip instead
        });
    </script>
</html>
