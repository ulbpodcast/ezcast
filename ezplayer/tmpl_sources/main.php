<!doctype html>
<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
                <!-- 
         * EZCAST EZplayer
         *
         * Copyright (C) 2014 Université libre de Bruxelles
         *
         * Written by Michel Jansens <mjansens@ulb.ac.be>
         * 	      Arnaud Wijns <awijns@ulb.ac.be>
         *            Carlos Avidmadjessi
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
                -->

        <!--
        Main template.
        This template is the main frame, the content divs are dynamically filled as the user clicks.
        
        WARNING: Please call template_repository_path() BEFORE including this template
        -->
        <title>®ezplayer_page_title®</title>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/ezplayer_style.css" />
        <link rel="stylesheet" type="text/css" href="css/reveal.css" />
        <link rel="stylesheet" type="text/css" href="lib/tinyEditor/tinyeditor.css" />

        <script type="text/javascript" src="lib/tinyEditor/tiny.editor.packed.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="js/httpRequest.js"></script>            
        <script type="text/javascript" src="js/jQuery/jquery.scrollTo-1.4.3.1-min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.localscroll-1.2.7-min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.reveal.js"></script>
        <script type="text/javascript" src="js/jQuery/highlight-js.js"></script>
        <script type="text/javascript" src="js/player.js"></script>
        <script type="text/javascript" src="js/ZeroClipboard.js"></script>

        <script>
            var current_album;
            var current_asset;
            var current_token;
            var current_tab;
            var clippy;
            var ie_browser = false;
            var thread_desc_editor = null;
            var thread_desc_edit_editor = null;
            var comment_desc_editor = null;
            var comment_desc_edit_editor = null;
            var comment_desc_reply_editor = null;
            var timecode_array = new Array();
            var title_array = new Array();
            var display_thread_details = false;
            var thread_to_display = null;

            ZeroClipboard.setMoviePath('./swf/ZeroClipboard10.swf');

            $(document).ready(function() {

                $(".container").css("min-height", ($(window).height() - 100) + "px");

                $('#assets_button, .bookmarks_button, .toc_button').localScroll({
                    target: '#side_pane',
                    axis: 'x'
                });
                // import/export menu closes when click outside
                $("*", document.body).click(function(e) {
                    if ((e.target.id != "bookmarks_actions") && !$(e.target).hasClass("menu-button") && ($("#bookmarks_actions").css("display") != "none")) {
                        $("#bookmarks_actions").css("display", "none");
                        $(".settings.bookmarks a.menu-button").toggleClass('active')
                    } else if ((e.target.id != "tocs_actions") && !$(e.target).hasClass("menu-button") && ($("#tocs_actions").css("display") != "none")) {
                        $("#tocs_actions").css("display", "none");
                        $(".settings.toc a.menu-button").toggleClass('active')
                    }
                });

                //===== CHECKBOX ONCHANGE ======================================
                if (!$("input[name='display_threads']").is(':checked')) {
                    $('#settings_notif_threads').removeAttr('checked');
                    $('#settings_notif_threads').attr("disabled", "disabled");
                }
                $("input[name='display_threads']").change(function() {

                    if ($(this).is(':checked')) {
                        $('#settings_notif_threads').removeAttr('disabled');
                    } else {
                        $('#settings_notif_threads').removeAttr('checked');
                        $('#settings_notif_threads').attr("disabled", "disabled");
                    }

                });

                window.onpopstate = function(event) {
                    var state = jQuery.parseJSON(JSON.stringify(event.state));
                    if (event.state == null) {

                    } else {
                        if (state.key === 'show-asset-details') {
                            location.reload();
                            history.pushState({"key": "show-asset-details"}, 'asset-details', 'index.php?action=view_asset_details');
                        } else if (state.key === 'show-album-assets') {
                            location.reload();
                            history.pushState({"key": "show-album-assets"}, 'album-details', 'index.php?action=view_album_assets');
                        } else if (state.key === 'thread-details') {
                            location.reload();
                            history.pushState({"key": "thread-details"}, 'thread-details', 'index.php?action=show_thread_detail');
                        } else if (state.key === 'show-album-forum') {
                            location.reload();
                            history.pushState({"key": "show-album-forum"}, 'album-forum', 'index.php?action=view_forum');
                        } else if (state.key === 'home') {
                            $('body').load('index.php');
                            location.reload();
                            history.pushState({"key": "home"}, 'album-details', 'index.php');
                        } else {
                            alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
                        }
                    }
                };
            });

            // Links an instance of clipboard to its position 
            function copyToClipboard(id, tocopy, width) {
                if (width == null || width == '' || width == 0)
                    width = 200;
                if (id == '#share_clip') {
                    clippy = new ZeroClipboard.Client();
                    clip = clippy;
                } else {
                    clip = new ZeroClipboard.Client();
                }
                clip.setText('');
                clip.addEventListener('mouseDown', function() {
                    console.log('Copy done.');
                    clip.setText(tocopy);
                });
                clip.addEventListener('onComplete', function() {
                    alert("®Content_in_clipboard®");
                });

                // Set the text to copy in the clipboard
                clip.setText(tocopy);
                $(id).html(clip.getHTML(width, 30));
            }

            function show_album_assets(album, token) {
                // the side pane changes to display the list of all assets contained in the selected album
                current_album = album;
                current_token = token;

                // Getting the content from the server, and filling the div_album_header with it
                document.getElementById('div_center').innerHTML = '<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>';
                makeRequest('index.php', '?action=view_album_assets&album=' + album + '&token=' + token + '&click=true', 'div_center');
                history.pushState({"key": "show-album-assets", "function": "show_album_assets(" + album + "," + token + ")", "url": "index.php?action=view_album_assets&album=" + album + "&token=" + token}, 'album-details', 'index.php?action=view_album_assets');
            }

            function show_asset_details(event, album, asset, asset_token) {
                if (event != null && event != 'no-event' && $(event.target).is("label") || $(event.target).is("input")) {
                    return false;
                }
                current_album = album;
                current_asset = asset;
                display_thread_details = false;

                makeRequest('index.php', '?action=view_asset_details&album=' + album + '&asset=' + asset + '&asset_token=' + asset_token + '&click=true', 'div_center');
                history.pushState({"key": "show-asset-details", "function": "show_asset_details(" + album + "," + asset + "," + asset_token + ")", "url": "index.php?action=view_asset_details&album=" + album + "&asset=" + asset + "&asset_token=" + asset_token}, 'asset-details', 'index.php?action=view_asset_details');
            }

            function show_thread(album, asset, token, threadId) {
                if (album != null && asset != null) {
                    current_album = album;
                    current_asset = asset;
                }
                if (typeof fullscreen != 'undefined' && fullscreen) {
                    video_fullscreen(false);
                }

                $('#asset-' + asset).click();

                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=thread_details_view&click=true',
                    data: {'thread_id': threadId},
                    success: function(response) {
                        $('.thread_info').html(response);
                        display_thread_details = true;
                        thread_to_display = response;
//                        location.reload();
                        $('.threads_info').hide();
                        $('.thread_info').show();
                    },
                    complete: function(jqXHR, textStatus) {
//                        scrollTo('thread_info');
                    }
                });
            }

            function show_asset_bookmark(album, asset, timecode, type) {
                current_album = album;
                current_asset = asset;

                makeRequest('index.php', '?action=view_asset_bookmark&album=' + album + '&asset=' + asset + '&t=' + timecode + '&type=' + type + '&click=true', 'div_center');
            }


            function show_search_albums() {
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

            function check_bookmark_form() {
                var timecode = document.getElementById('bookmark_timecode');
                var level = document.getElementById('bookmark_level');

                if (isNaN(timecode.value)
                        || timecode.value == ''
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

            function check_edit_bookmark_form(index, tab) {
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

            function sort_bookmarks(panel, order, source) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=sort_asset_bookmark',
                    data: 'panel=' + panel + '&order=' + order + "&source=" + source + "&click=true",
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //     ajaxSubmitForm('submit_' + tab + '_form_' + index, 'index.php', '?action=add_asset_bookmark', 'div_right');  

            }

            function submit_bookmark_form() {
                var tab = document.getElementById('bookmark_source').value;
                (tab == 'custom') ? current_tab = 'main' : current_tab = 'toc';
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=add_asset_bookmark&click=true',
                    data: $('#submit_bookmark_form').serialize(),
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('submit_bookmark_form', 'index.php', '?action=add_asset_bookmark', 'div_right');  
                hide_bookmark_form();

            }

            function submit_edit_bookmark_form(index, tab) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=add_asset_bookmark&click=true',
                    data: $('#submit_' + tab + '_form_' + index).serialize(),
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //     ajaxSubmitForm('submit_' + tab + '_form_' + index, 'index.php', '?action=add_asset_bookmark', 'div_right');  

            }
            //===== THREAD =====================================================

            function check_thread_form() {
                thread_desc_editor.post();
                var timecode = document.getElementById('thread_timecode');
                var message = document.getElementById('thread_description_tinyeditor').value;
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

            function check_edit_thread_form(threadId) {
                thread_desc_edit_editor.post();
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
            function check_comment_form() {
                // Divers vérifications
                comment_desc_editor.post();
                var message = document.getElementById('comment_message_tinyeditor').value;
                if (message == '') {
                    window.alert('®missing_message®');
                    return false;
                }
                return true;
            }
            function check_answer_comment_form(id) {
                // Divers vérifications
                comment_desc_reply_editor.post();
                var message = document.getElementById('answer_comment_message_' + id + '_tinyeditor').value;

                if (message == '') {
                    window.alert('®missing_message®');
                    return false;
                }
                return true;
            }

            function choose_thread_visibility(value) {
                if (value == 1)
                    $('#thread_visibility').attr("checked", "checked")
                else
                    $('#thread_visibility').removeAttr("checked");
                if (check_thread_form())
                    submit_thread_form();
            }

            function submit_thread_form() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=add_asset_thread&click=true',
                    data: $('#submit_thread_form').serialize(),
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });

                hide_thread_form();
            }

            function submit_comment_form() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=add_thread_comment&click=true',
                    data: $('#submit_comment_form').serialize(),
                    success: function(response) {
                        hide_comment_form();
                        $('#threads').html(response);
                    }
                });
            }

            function submit_answer_comment_form(id) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=add_thread_comment_answer&click=true',
                    data: {'answer_message': document.getElementById('answer_comment_message_' + id + '_tinyeditor').value, 'answer_parent': document.getElementById('answer_parent_' + id).value, 'thread_id': document.getElementById('answer_thread_' + id).value, 'answer_nbChilds': document.getElementById('answer_nbChilds_' + id).value, 'album': document.getElementById('answer_album').value, 'asset': document.getElementById('answer_asset').value},
                    success: function(response) {
                        hide_answer_comment_form(id);
                        $('#threads').html(response);
                    }
                });
            }

            function show_thread_details(event, thread_id) {
                if ($(event.target).is('a') || $(event.target).is('span.timecode'))
                    return;
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=thread_details_view&click=true',
                    data: {'thread_id': thread_id},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
//                history.pushState({"key": "thread-details"}, 'thread-details', 'index.php?action=show_thread_detail');
            }

            function threads_list_update() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=threads_list_view&click=true',
                    data: {'album': current_album, 'asset' : current_asset},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }

            function delete_asset_thread(thread_id, album, asset) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=delete_asset_thread&click=true',
                    data: {'thread_id': thread_id, 'thread_album': album, 'thread_asset': asset},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }
            function delete_thread_comment(thread_id, comment_id) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=delete_thread_comment&click=true',
                    data: {'thread_id': thread_id, 'comment_id': comment_id},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }

            function edit_thread_comment(comId) {
                if (!$("#edit_comment_message_" + comId + "_tinyeditor").hasClass('edited')) {
                    comment_desc_edit_editor = new TINY.editor.edit('editor_edit', {
                        id: 'edit_comment_message_' + comId + '_tinyeditor',
                        width: 468,
                        height: 100,
                        cssclass: 'tinyeditor normal',
                        controlclass: 'tinyeditor-control',
                        rowclass: 'tinyeditor-header',
                        dividerclass: 'tinyeditor-divider',
                        controls: ['bold', 'italic', 'underline', '|', 'subscript', 'superscript', '|'
                                    , 'unorderedlist', '|', 'blockjustify', '|', 'undo', 'redo'],
                        xhtml: true,
                        bodyid: 'editor_edit',
                        resize: {cssclass: 'resize'}
                    });
                }
                $('.comment-options').hide();
                $('#comment_message_id_' + comId).hide();
                $('#edit-options-' + comId).show();
                $('#edit_comment_' + comId).show();
                $('#comment_message_' + comId).focus();
            }
            function edit_asset_thread(threadId) {
                if (!$("#edit_thread_message_" + threadId + "_tinyeditor").hasClass('edited')) {
                    thread_desc_edit_editor = new TINY.editor.edit('editor_edit', {
                        id: 'edit_thread_message_' + threadId + '_tinyeditor',
                        width: 480,
                        height: 100,
                        cssclass: 'tinyeditor edit',
                        controlclass: 'tinyeditor-control',
                        rowclass: 'tinyeditor-header',
                        dividerclass: 'tinyeditor-divider',
                        controls: ['bold', 'italic', 'underline', '|', 'subscript', 'superscript', '|'
                                    , 'unorderedlist', '|', 'blockjustify', '|', 'undo', 'redo'],
                        xhtml: true,
                        bodyid: 'editor_edit',
                        resize: {cssclass: 'resize'}
                    });
                }
                $('#message-thread').hide();
                $('#thread-options').hide();
                $('#edit_thread_form_' + threadId).show();
                $('#edit_thread_title' + threadId).focus();

            }

            function hide_edit_comment(comId) {
                $('#comment_message_id_' + comId).show();
                $('.comment-options').show();
                $('#edit-options-' + comId).hide();
                $('#edit_comment_' + comId).hide();
            }

            function nl2br(str, is_xhtml) {
                var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
                return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
            }

            function cancel_edit_comment(comId, message) {
                $("#edit_comment_message_" + comId + "_tinyeditor").addClass('edited');
                $("#edit_comment_message_" + comId + "_tinyeditor").val($('#comment_message_id_' + comId).text());
                hide_edit_comment(comId);
            }

            function cancel_edit_thread(threadId, title, message, timecode) {
                $("#edit_thread_message_" + threadId + "_tinyeditor").addClass('edited');
                document.getElementById('edit_thread_title_' + threadId).value = title;
                document.getElementById('edit_thread_timecode_' + threadId).value = timecode;
                $('#edit_thread_form_' + threadId).hide();
                $('#message-thread').show();
                $('#thread-options').show();
            }

            function submit_edit_comment_form(comment_id) {
                comment_desc_edit_editor.post();
                var message = document.getElementById('edit_comment_message_' + comment_id + '_tinyeditor').value;
                if (message == '') {
                    window.alert('®missing_message®');
                    return false;
                }
                var album = document.getElementById('edit_comment_album').value;
                var asset = document.getElementById('edit_comment_asset').value;
                var thread = document.getElementById('edit_comment_thread').value;
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=update_thread_comment&click=true',
                    data: {'comment_id': comment_id, 'comment_message': message, 'thread_id': thread, 'album': album, 'asset': asset},
                    success: function(response) {
                        $('#threads').html(response);
                        hide_edit_comment(commentId);
                    }
                });
            }

            function submit_edit_thread_form(threadId, album, asset) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=update_asset_thread&click=true',
                    data: {
                        'thread_id': threadId,
                        'thread_title': document.getElementById('edit_thread_title_' + threadId).value,
                        'thread_message': document.getElementById('edit_thread_message_' + threadId + '_tinyeditor').value,
                        'thread_timecode': document.getElementById('edit_thread_timecode_' + threadId).value,
                        'thread_album': album,
                        'thread_asset': asset
                    },
                    success: function(response) {
                        $('#edit_thread_form_' + threadId).hide();
                        $('#threads').html(response);

                    }
                });
            }

            function toggle_hidden_thread_part(_id) {
                if ($('#hidden-item-thread-' + _id).is(":hidden")) {
                    $('#hidden-item-thread-' + _id).slideDown();
                    $('.more-button-' + _id).addClass("active");
                } else {
                    $('#hidden-item-thread-' + _id).slideUp();
                    $('.more-button-' + _id).removeClass("active");
                }
            }

            function thread_details_update(thread_id) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=thread_details_view&click=true',
                    data: {'thread_id': thread_id},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }

            function refresh_asset_threads() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=show_asset_threads&click=true',
                    data: {},
                    success: function(response) {
                        $('#threads_info').html(response);
                    }
                });
            }


            //=== END - THREAD =================================================

            //===== BEGIN - SETTINGS ===========================================
            function submit_settings_form() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=edit_settings&click=true',
                    data: {
                        'display_new_video_notification': $('#settings_notif_new_asset').attr('checked') == 'checked' ? true : false,
                        'display_threads': $('#settings_display_threads').attr('checked') == 'checked' ? true : false,
                        'display_thread_notification': $('#settings_notif_threads').attr('checked') == 'checked' ? true : false
                    },
                    success: function(response) {
                        hide_settings_form();
                        location.reload();
                    }
                });
            }

            function invert_download_checkbox(event, index) {
                $('#is_downloadable_' + index).click();
            }

            function set_asset_download_option(album, asset, index) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=edit_asset_meta&click=true',
                    data: {
                        'display_download_link': $('#is_downloadable_' + index).attr('checked') == 'checked' ? true : false,
                        'album_download_link': album,
                        'asset_download_link': asset
                    },
                    success: function(response) {
                    }
                });
            }
            //=== END - SETTINGS ===============================================

            //===== BEGIN - VOTE ===============================================
            function vote(user, comment, type) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=vote&click=true',
                    data: {'login': user, 'comment': comment, 'vote_type': type},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }

            function approve(comment) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=approve&click=true',
                    data: {'approved_comment': comment},
                    success: function(response) {
                        $('#threads').html(response);
                    }
                });
            }
            //=== END - VOTE ===================================================

            //===== SEARCH =====================================================
            function show_search_options() {
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

            //==================================================================
            function check_search_form() {
                var search_words = $('#main_search').val();
                if (typeof search_words != 'undefined') {
                    if (search_words.trim() == '') {
                        return false;
                    }
                    submit_search_form();
                }
            }

            function submit_search_form() {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=search_bookmark&click=true',
                    data: $('#search_form').serialize(),
                    success: function(response) {
                        $('#popup_search_result').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'popup_search_result');  

                $('#popup_search_result').reveal($(this).data());
            }

            function search_keyword(keyword) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=search_bookmark&click=true',
                    data: 'search=' + keyword + '&target=global&albums%5B%5D=&fields%5B%5D=keywords&tab%5B%5D=official&tab%5B%5D=custom&level=0',
                    success: function(response) {
                        $('#popup_search_result').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'popup_search_result');  

                $('#popup_search_result').reveal($(this).data());
            }

            function submit_import_bookmarks_form(source) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=import_bookmarks&click=true&source=' + source,
                    data: $('#select_import_bookmark_form').serialize(),
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('select_import_bookmark_form', 'index.php', '?action=import_bookmarks'+ 
                //       '&source=' + source, 'div_right');    
                close_popup();
            }

            function submit_delete_bookmarks_form(source) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=delete_bookmarks&click=true&source=' + source,
                    data: $('#select_delete_bookmark_form').serialize(),
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('select_delete_bookmark_form', 'index.php', '?action=delete_bookmarks'+ 
                //       '&source=' + source, 'div_right');    
                close_popup();
            }

            function submit_delete_tocs_form(source) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?action=delete_bookmarks&click=true&source=' + source,
                    data: $('#select_delete_toc_form').serialize(),
                    success: function(response) {
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //    ajaxSubmitForm('select_delete_toc_form', 'index.php', '?action=delete_bookmarks'+ 
                //        '&source=' + source, 'div_right');    
                close_popup();
            }

            function close_popup() {
                var e = jQuery.Event("click");
                $(".reveal-modal-bg").trigger(e); // trigger it on document
            }

            function edit_bookmark(index, tab, title, description, keywords, level) {
                document.getElementById(tab + '_title_' + index).value = title;
                document.getElementById(tab + '_description_' + index).value = description;
                document.getElementById(tab + '_keywords_' + index).value = keywords;
                document.getElementById(tab + '_level_' + index).value = level;
                toggle_edit_bookmark_form(index, tab);
            }

            function toggle_edit_bookmark_form(index, tab) {
                $('#' + tab + index).toggle();
                $('#' + tab + '_info_' + index).toggle();
                $('#edit_' + tab + '_' + index).toggle();
                $('#' + tab + '_title_' + index).toggle();
            }

            function remove_bookmark(album, asset, timecode, source, tab) {
                makeRequest('index.php', '?action=remove_asset_bookmark' +
                        '&album=' + album +
                        '&asset=' + asset +
                        '&timecode=' + timecode +
                        '&source=' + source +
                        '&tab=' + tab +
                        "&click=true", 'div_right');
            }

            function remove_bookmarks(album, asset) {
                makeRequest('index.php', '?action=remove_asset_bookmarks' +
                        '&album=' + album +
                        '&asset=' + asset +
                        "&click=true", 'div_center');
            }

            function copy_bookmark(album, asset, timecode, title, description, keywords, level, source, tab) {
                makeRequest('index.php', '?action=copy_bookmark' +
                        '&album=' + album +
                        '&asset=' + asset +
                        '&timecode=' + timecode +
                        '&title=' + title +
                        '&description=' + description +
                        '&keywords=' + keywords +
                        '&level=' + level +
                        '&source=' + source +
                        '&tab=' + tab +
                        "&click=true", 'div_right');
            }

            function delete_album_token(album) {
                makeRequest('index.php', '?action=delete_album_token' +
                        '&album=' + album +
                        '&click=true', 'div_center');
            }

            function move_album_token(index, upDown) {
                makeRequest('index.php', '?action=move_album_token' +
                        '&index=' + index + '&up_down=' + upDown + "&click=true", 'div_center');
            }

            function toggle_detail(index, pane, elem) {
                $('#' + pane + '_detail_' + index).slideToggle();
                $('#' + pane + '_' + index).toggleClass('active');
                elem.toggleClass('active');
                var millisecondsToWait = 350;
                setTimeout(function() {
                    $('.' + pane + '_scroll').scrollTo('#' + pane + '_' + index);
                    // Whatever you want to do after the wait
                }, millisecondsToWait);
            }

            function toggle(elem) {
                $(elem).toggle(200);
            }

            function toggle_checkboxes(source, target) {
                var checkboxes = document.getElementsByName(target);
                for (var i = 0; i < checkboxes.length; i++)
                    checkboxes[i].checked = source.checked;
            }

            function scroll(direction, element) {
                var scrolled = $(element).scrollTop();
                if (direction == 'up') {
                    var scroll = scrolled + 55;
                } else {
                    var scroll = scrolled - 55;
                }

                $(element).animate({scrollTop: scroll}, "fast");
            }

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
                    x[i].onchange = x[i].onmouseout = function() {
                        this.relatedElement.value = this.value;
                    }
                }
            }

            function check_upload_form() {
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


            function submit_upload_bookmarks() {
                if (ie_browser) {
                    document.forms["upload_bookmarks"].submit();
                    $('#upload_target').load(function() {
                        document.getElementById('popup_import_bookmarks').innerHTML = $("#upload_target").contents().find("body").html();
                    });
                } else {
                    ajaxUpload('XMLbookmarks', 'loadingfile', 'index.php', '?action=upload_bookmarks', 'popup_import_bookmarks');
                }
                // doesn't work in IE < 10 (due to FormData object)
                //     ajaxUpload('XMLbookmarks', 'loadingfile', 'index.php', '?action=upload_bookmarks', 'popup_import_bookmarks');                
            }


        </script>

        <?php if (isset($head_code)) echo $head_code; ?>
    </head>
    <body>
        <?php
        // Displays a warning message if the brower is not fully supported
        $warning = true;
        switch (strtolower($_SESSION['browser_name'])) {
            case 'safari' :
                if ($_SESSION['browser_version'] >= 5)
                    $warning = false;
                break;
            case 'chrome' :
                if ($_SESSION['browser_version'] >= 4)
                    $warning = false;
                break;
            case 'internet explorer' :
                if ($_SESSION['browser_version'] >= 9)
                    $warning = false;
                break;
            case 'firefox' :
                if ($_SESSION['browser_version'] >= 22 && ($_SESSION['user_os'] == "Windows" || $_SESSION['user_os'] == "Android"))
                    $warning = false;
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
                        <?php if ($_SESSION['user_os'] == "Windows") { ?>
                            <li><b>Internet Explorer 9+</b> | </li>
                            <li><b>Firefox 22+</b></li>
                        <?php } ?>
                    </ul>
                </div>       
            </div>
        <?php } ?>
        <div class="container">
            <?php include_once template_getpath('div_main_header.php'); ?>
            <div id="global">
                <div id="div_center">
                    <?php
                    if (isset($error_path) && !empty($error_path)) {
                        include_once $error_path;
                    } else if ($_SESSION['ezplayer_mode'] == 'view_main') {
                        include_once template_getpath('div_main_center.php');
                    } else if ($_SESSION['ezplayer_mode'] == 'view_forum') {
                        include_once template_getpath('div_album_forum.php');
                    } else {
                        include_once template_getpath('div_assets_center.php');
                    }
                    ?>
                </div><!-- div_center END -->
            </div><!-- global -->

            <?php
            if ($_SESSION["show_message"]) {
                include_once template_getpath('popup_message_of_day.php');
                ?>
                <script>
                $('#popup_message_of_day').reveal($(this).data());
                </script>           
            <?php } ?>
        </div><!-- Container fin -->


        <!-- FOOTER - INFOS COPYRIGHT -->
        <?php include_once template_getpath('div_main_footer.php'); ?>
        <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
    </body>
</html>
