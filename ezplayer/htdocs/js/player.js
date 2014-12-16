/*
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
*/

var quality;
var type;
var cam_loaded;
var slide_loaded;
var panel_width = 295;
var save_currentTime = null;
var from_shortcut = false;
var trace_pause = false;
/**
 * Duration of the notification (sec)
 * @type Number
 */
var notif_display_delay = 10;
/**
 * Number of threads to display at once
 * @type Number
 */
var notif_display_number = 3;

window.addEventListener("keyup", function(e) {
    var el = document.activeElement;

    if (lvl == 3 && (!el || (el.tagName.toLowerCase() != 'input' &&
            el.tagName.toLowerCase() != 'textarea'))) {
        from_shortcut = true;
        // focused element is not an input or textarea
        switch (e.keyCode) {
            case 32:  // space 
                toggle_play();
                break;
            case 66:  // 'b'
                if (fullscreen)
                    toggle_panel();
                break;
            case 78:  // 'n'
                toggle_bookmark_form('custom');
                break;
            case 107:
            case 187: // '+'
                video_playbackspeed('up');
                break;
            case 109:
            case 189: // '-'
                video_playbackspeed('down');
                break;
            case 82: // 'r'
                toggle_shortcuts();
                break;
            case 83:  // 's'
                (type == 'cam') ?
                        switch_video('slide') :
                        switch_video('cam');
                break;
            case 37:  // 'left arrow'
                video_navigate('rewind');
                break;
            case 39:  // 'right arrow'
                video_navigate('forward');
                break;
            case 27:  // 'esc'
                video_fullscreen(false);
                break;
            case 70:  // 'f'
                video_fullscreen(!fullscreen);
                break;
            case 38:  // 'up arrow'
                video_volume('up');
                break;
            case 40:  // 'down arrow'
                video_volume('down');
                break;
            case 77:  // 'm'
                toggle_mute();
                break;
            case 79 : // 'o'
                if (is_lecturer == true)
                    toggle_bookmark_form('official');
                break;
            case 76:  // 'l'
                video_link();
                break;
            case 8:  // 'backspace'
                break;
        }
    } else if (lvl == 3) {
        if (e.keyCode == 27) {
            // leave focus when esc is pressed in input or text field
            $('input, textarea').blur();
        }
    }
}, false);

window.addEventListener("keydown", function(e) {
    var el = document.activeElement;

    if (lvl == 3 && (!el || (el.tagName.toLowerCase() != 'input' &&
            el.tagName.toLowerCase() != 'textarea'))) {
        // space and arrow keys
        if ([32, 37, 38, 39, 40, 8].indexOf(e.keyCode) > -1) {
            e.preventDefault();
        }
    }
}, false);

// resizes the video when video is played fullscreen AND bookmark form is visible
$(window).bind('resize', function(e)
{
    window.resizeEvt;
    $(window).resize(function()
    {
        // wait for the window being resized
        clearTimeout(window.resizeEvt);
        window.resizeEvt = setTimeout(function()
        {
            if (fullscreen && bookmark_form) {
                video_resize();
            }
            if (fullscreen && show_panel) {
                panel_resize();
                pct = (100 - (panel_width * 100 / ($(window).width()))) + '%';
                $('video').css('width', pct);

            }
        }, 250);
    });
});

function load_player(media) {
    // Notification panel starts hidden
    $('#video_notifications').hide();
    var videos = document.getElementsByTagName('video');
    for (var i = 0, max = videos.length; i < max; i++) {
        videos[i].addEventListener("seeked", function() {
            previous_time = time;
            time = Math.round(this.currentTime);
            document.getElementById('bookmark_timecode').value = time;
            document.getElementById('thread_timecode').value = Math.round(this.currentTime);
            server_trace(new Array('4', 'video_seeked', current_album, current_asset, duration, previous_time, time, type, quality));
        }, false);

        // Listener on video time change
        // In order to match threads timecode to video timecode
        videos[i].addEventListener("timeupdate", function() {
            currentTime = Math.round(this.currentTime);

            if (currentTime == save_currentTime)
                return;

            save_currentTime = currentTime;
            html_value = "<ul>";
            i = 0;
            for (var key in timecode_array) {
                if (!(timecode_array[key] > currentTime)
                        && !(timecode_array[key] < currentTime - notif_display_delay)
                        && !(timecode_array[key] === null)) {
                    i++;
                    // Display max N threads (with N = notif_display_number)
                    if (i > notif_display_number)
                        break;
                    html_value += "<li id='notif_"+key+"' class ='notification_item'><span class='span-link red' onclick='javascript:remove_notification_item(" + key + ")' >x</span><span class='notification-item-title' onclick='javascript:thread_details_update(" + key + ")'> " + title_array[key] + "</span></li>";
                }
            }
            html_value += "</ul>";
            $('#notifications').html(html_value);
            if (i > 0)
                $('#video_notifications').slideDown();
            else
                $('#video_notifications').slideUp();
        });

    }

    var elem = media.split('_');
    quality = elem[0];
    type = elem[1];

}

function video_addlisterners(){
    
}

function switch_video(media_type) {
    /*    
     if (media_type != "cam" && media_type != "slide") return;
     if (media_type == type) return;
     if (quality != 'high' && quality != 'low') quality = 'low';
     var media = quality + '_' + media_type;
     var video = document.getElementById('main_video');
     var source = document.getElementById('main_video_source');
     var paused = video.paused;
     var oldCurrentTime = video.currentTime;
     // doesn't work in Safari 5
     // source.setAttribute('src', source.getAttribute(media + '_src'));  
     video.setAttribute('src', source.getAttribute(media + '_src'));
     video.load();  
     video.addEventListener('loadedmetadata', function() {        
     this.currentTime = oldCurrentTime;
     }, false); 
     paused ? video.pause() : video.play();
     var elem = media.split('_');
     quality = elem[0];
     type = elem[1];
     $('.movie-button, .slide-button').toggleClass('active');
     */
    origin = get_origin();

    if (media_type != "cam" && media_type != "slide")
        return;
    if (media_type == type)
        return;
    if (quality != 'high' && quality != 'low')
        quality = 'low';
    var media = quality + '_' + media_type;
    if (media_type == 'cam') {
        var to_show = document.getElementById('main_video');
        var to_hide = document.getElementById('secondary_video');
    } else {
        var to_hide = document.getElementById('main_video');
        var to_show = document.getElementById('secondary_video');
    }
    var oldCurrentTime = to_hide.currentTime;

    if (/webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
        trace_pause = true; // disables trace to make sure play/pause actions are not written in the logs
        to_hide.pause();
        if ((media_type == 'cam' && cam_loaded) || (media_type == 'slide' && slide_loaded)) {
            to_show.currentTime = oldCurrentTime;
            trace_pause = true; // disables trace to make sure play/pause actions are not written in the logs
            to_show.play();
            document.getElementById("load_warn").style.display = 'none';
        } else {
            document.getElementById("load_warn").style.display = 'block';
        }
    } else if (/Android/i.test(navigator.userAgent)) {
        trace_pause = true; // disables trace to make sure play/pause actions are not written in the logs
        to_hide.pause();
        to_show.currentTime = oldCurrentTime;
    } else {
        to_show.currentTime = oldCurrentTime;
        if (!to_hide.paused) {
            trace_pause = true; // disables trace to make sure play/pause actions are not written in the logs
            to_hide.pause();
            trace_pause = true; // disables trace to make sure play/pause actions are not written in the logs
            to_show.play();
        }
    }
    to_hide.style.display = 'none';
    to_show.style.display = 'block';
    var elem = media.split('_');
    quality = elem[0];
    from = type;
    type = elem[1];
    server_trace(new Array('4', 'video_switch', current_album, current_asset, duration, time, from, type, quality, origin));
    $('.movie-button, .slide-button').toggleClass('active');
}

function toggle_video_quality(media_quality) {

    if (media_quality != "high" && media_quality != "low")
        return;
    if (media_quality == quality)
        return;
    var media = media_quality + '_' + type;

    if (document.getElementById('secondary_video') && type == 'slide') {
        var video = document.getElementById('secondary_video');
    } else {
        var video = document.getElementById('main_video');
    }

    var source = document.getElementById('main_video_source');
    var paused = video.paused;
    var oldCurrentTime = video.currentTime;
    // doesn't work in Safari 5
    // source.setAttribute('src', source.getAttribute(media + '_src')); 

    if (document.getElementById('secondary_video')) {
        document.getElementById('main_video').setAttribute('src', source.getAttribute(media_quality + '_cam_src'));
        document.getElementById('secondary_video').setAttribute('src', source.getAttribute(media_quality + '_slide_src'));
        document.getElementById('main_video').load();
        document.getElementById('secondary_video').load();
    } else {
        video.setAttribute('src', source.getAttribute(media + '_src'));
        video.load();
    }
    video.addEventListener('loadedmetadata', function() {
        this.currentTime = oldCurrentTime;
    }, false);
    trace_pause = true;
    paused ? video.pause() : video.play();
    var elem = media.split('_');
    quality = elem[0];
    type = elem[1];
    $('.high-button, .low-button').toggleClass('active');
    server_trace(new Array('4', 'video_quality', current_album, current_asset, duration, time, type, media_quality, quality));

}

function seek_video(bookmark_time, bookmark_type) {
    server_trace(new Array('4', 'video_bookmark_click', current_album, current_asset, duration, time, bookmark_time, type, bookmark_type, current_tab, quality));

    if (bookmark_type != '' && type != bookmark_type) {
        switch_video(bookmark_type);
    }
    if (document.getElementById('secondary_video')) {
        if (type == 'slide') {

            var video = document.getElementById('secondary_video');
        } else {

            var video = document.getElementById('main_video');
        }
    } else {
        var video = document.getElementById('main_video');
    }
    var paused = video.paused;
    //  video.load();  
    //  video.addEventListener('loadedmetadata', function() {        
    //      this.currentTime = time;
    //  }, false);      
    video.currentTime = bookmark_time;
    paused ? video.pause() : video.play();
}

function video_playbackspeed(rate) {
    origin = get_origin();
    var video = document.getElementById('main_video');
    var playbackSpeed = video.playbackRate;
    if (playbackSpeed == 0.5 && rate == 'up') {
        playbackSpeed = 1;
    } else if (playbackSpeed < 2 && rate == 'up') {
        playbackSpeed = playbackSpeed + 0.2;
    } else if (playbackSpeed > 1 && rate == "down") {
        playbackSpeed = playbackSpeed - 0.2;
    } else if (playbackSpeed <= 1 && playbackSpeed > 0.5 && rate == "down") {
        playbackSpeed = playbackSpeed - 0.5;
    }
    server_trace(new Array('4', 'playback_speed_' + rate, current_album, current_asset, duration, time, type, quality, playbackSpeed, origin));

    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').playbackRate = playbackSpeed;
    }
    video.playbackRate = playbackSpeed;
    document.getElementById('toggleRate').innerHTML = (playbackSpeed.toFixed(1) + 'x');
}

function toggle_playbackspeed() {
    origin = get_origin();
    var video = document.getElementById('main_video');
    var playbackSpeed = video.playbackRate;
    var rate;

    if (playbackSpeed == 0.5) {
        playbackSpeed = 1;
        rate = 'up';
    } else if (playbackSpeed < 2) {
        playbackSpeed = playbackSpeed + 0.2;
        rate = 'up';
    } else {
        playbackSpeed = 0.5;
        rate = 'down';
    }
    server_trace(new Array('4', 'playback_speed_' + rate, current_album, current_asset, duration, time, type, quality, playbackSpeed, origin));

    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').playbackRate = playbackSpeed;
    }
    video.playbackRate = playbackSpeed;
    document.getElementById('toggleRate').innerHTML = (playbackSpeed.toFixed(1) + 'x');
    if (playbackSpeed != 1) {
        document.getElementById('toggleRate').classList.add('active');
    } else {
        document.getElementById('toggleRate').classList.remove('active');
    }
}

function show_bookmark_form(source) {
    $("#video_shortcuts").css("display", "none");
    if (document.getElementById('secondary_video')) {
        if (type == 'slide') {

            var video = document.getElementById('secondary_video');
        } else {

            var video = document.getElementById('main_video');
        }
    } else {
        var video = document.getElementById('main_video');
    }
    // Hide thread form if it's visible
    if (thread_form) {
        hide_thread_form();
//        $('#thread_form').hide();
    }
    video.pause();
    document.getElementById('bookmark_timecode').value = Math.round(video.currentTime);
    document.getElementById('bookmark_source').value = source;
    document.getElementById('bookmark_type').value = type;
    if (source == 'official') {
        $('.bookmark-color').hide();
        $('.toc-color').show();
        $('.add-bookmark-button').removeClass("active");
        $('.add-toc-button').addClass("active");
        $('#subBtn').removeClass("blue");
        $('#subBtn').addClass("orange");
        $('#bookmark_form').addClass("toc");
    } else {
        $('.bookmark-color').show();
        $('.toc-color').hide();
        $('.add-toc-button').removeClass("active");
        $('.add-bookmark-button').addClass("active");
        $('#subBtn').removeClass("orange")
        $('#subBtn').addClass("blue");
        $('#bookmark_form').addClass("bookmark");
    }
    video_resize_bookmark();
    $('#bookmark_form').slideDown();
    bookmark_form = true;

}


function hide_bookmark_form() {
    bookmark_form = false;
    $("#video_shortcuts").css("display", "block");
    $('video').animate({'height': '93%'});
    if (fullscreen && show_panel) {
        $('#div_right').animate({'height': '92.6%'}, function() {
            panel_resize()
        });
    }
    $('#bookmark_form').slideUp();
    document.getElementById('bookmark_title').value = '';
    document.getElementById('bookmark_description').value = '';
    document.getElementById('bookmark_keywords').value = '';
    document.getElementById('bookmark_level').value = '1';
    $('.add-bookmark-button').removeClass("active");
    $('.add-toc-button').removeClass("active");
    $('#bookmark_form').removeClass("bookmark");
    $('#bookmark_form').removeClass("toc");

}

function toggle_bookmark_form(source) {

    origin = get_origin();

    from_shortcut = false;
    if (bookmark_form) {
        server_trace(new Array('4', 'bookmark_form_hide', current_album, current_asset, duration, time, type, source, quality, origin));
        if (source == 'official') {
            if ($('#bookmark_form').hasClass("toc")) {
                hide_bookmark_form();
            } else {
                hide_bookmark_form();
                toggle_bookmark_form('official');
            }
        } else {
            if ($('#bookmark_form').hasClass("bookmark")) {
                hide_bookmark_form();
            } else {
                hide_bookmark_form();
                toggle_bookmark_form('custom');
            }
        }
    } else {
        server_trace(new Array('4', 'bookmark_form_show', current_album, current_asset, duration, time, type, source, quality, origin));
        show_bookmark_form(source);
        $("#bookmark_title").focus();
    }
}

//===== THREAD =================================================================

/*
 * Hide or show thread form depending on his current state.
 */
function toggle_thread_form() {
    if (thread_form) {
        hide_thread_form();
        return;
    } else if (bookmark_form) {
//        $('#bookmark_form').hide();
        hide_bookmark_form();
    }
    show_thread_form();
    $("#thread_title").focus();
}

/*
 * Hide or show comment form depending on his current state.
 */
function toggle_comment_form() {
    if (comment_form) {
        hide_comment_form();
    } else {
        show_comment_form();
        $("#comment_message").focus();
    }
}

// displays comment form (for reply) and create editor if it doesn't exist yet
function show_answer_comment_form(id) {
    // checks whether the editor already exists or not.
    // if it doesn't exist, it creates it.
    if (!$('#answer_comment_message_' + id + '_tinyeditor').hasClass('editor-created')) {
        comment_desc_reply_editor = new TINY.editor.edit('editor', {
            id: 'answer_comment_message_' + id + '_tinyeditor',
            width: 444,
            height: 100,
            cssclass: 'tinyeditor normal',
            controlclass: 'tinyeditor-control',
            rowclass: 'tinyeditor-header',
            dividerclass: 'tinyeditor-divider',
            controls: ['bold', 'italic', 'underline', '|', 'subscript', 'superscript',
                '|', 'unorderedlist', '|', 'blockjustify', '|', 'undo', 'redo'],
            xhtml: true
        });
    }
    $('.comment-options').hide();

    $('#answer_comment_form_' + id).slideDown();
    $("#answer_comment_message_" + id).focus();
}

function toggle_settings_form() {
    if (settings_form) {
        hide_settings_form();
    } else {
        show_settings_form();
    }
}

// displays thread form 
function show_thread_form() {
    // Creates the editor only if it's not yet otherwise it will display 2 editors (or more)
    if (!$('#thread_description_tinyeditor').hasClass('editor-created')) {
        thread_desc_editor = new TINY.editor.edit('editor', {
            id: 'thread_description_tinyeditor',
            width: 502,
            height: 100,
            cssclass: 'tinyeditor',
            controlclass: 'tinyeditor-control',
            rowclass: 'tinyeditor-header',
            dividerclass: 'tinyeditor-divider',
            controls: ['bold', 'italic', 'underline', '|', 'subscript', 'superscript',
                '|', 'unorderedlist', '|', 'blockjustify', '|', 'undo', 'redo'],
            xhtml: true
        });
    }
    $("#video_shortcuts").css("display", "none");
    if (document.getElementById('secondary_video')) {
        if (type == 'slide') {

            var video = document.getElementById('secondary_video');
        } else {

            var video = document.getElementById('main_video');
        }
    } else {
        var video = document.getElementById('main_video');
    }

    video.pause();
    document.getElementById('thread_timecode').value = Math.round(video.currentTime);

    $('.add-thread-button').removeClass("active");
    $('.add-thread-button').addClass("active");

    video_resize();
    $('#thread_form').slideDown();
    thread_form = true;
}

function show_comment_form() {
    if (!$('#comment_message_tinyeditor').hasClass('editor-created')) {
        comment_desc_editor = new TINY.editor.edit('editor', {
            id: 'comment_message_tinyeditor',
            width: 560,
            height: 100,
            cssclass: 'tinyeditor margin-left',
            controlclass: 'tinyeditor-control',
            rowclass: 'tinyeditor-header',
            dividerclass: 'tinyeditor-divider',
            controls: ['bold', 'italic', 'underline', '|', 'subscript', 'superscript',
                '|', 'unorderedlist', '|', 'blockjustify', '|', 'undo', 'redo'],
            xhtml: true
        });
        $('#comment_message_tinyeditor').addClass('editor-created');
    }
    $('#comment_form').slideDown();
    $("html, body").animate({scrollTop: $(document).height()}, 1000);
    comment_form = true;
}

function hide_thread_form() {
    $("#video_shortcuts").css("display", "block");
    $('video').animate({'height': '92.12%'});
    if (fullscreen && show_panel) {
        $('#div_right').animate({'height': '92.6%'}, function() {
            panel_resize()
        });
    }
    $('#thread_form').slideUp();
    document.getElementById('thread_title').value = '';
    document.getElementById('thread_description_tinyeditor').value = '';
    $('.add-thread-button').removeClass("active");

    $('#thread_description_tinyeditor').addClass('editor-created');
    thread_form = false;
}

function hide_comment_form() {
    comment_form = false;
    $('#comment_form').slideUp();
    document.getElementById('comment_message_tinyeditor').value = '';
}

function hide_answer_comment_form(id) {
    $('.comment-options').show();
    $('#answer_comment_form_' + id).slideUp();
    document.getElementById('answer_comment_message_' + id + '_tinyeditor').value = '';
    $('#answer_comment_message_' + id + '_tinyeditor').addClass('editor-created');
}


//=== END - THREAD =============================================================
//
//===== BEGIN - SETTINGS =======================================================
function show_settings_form() {
    $('#settings_form').slideDown();
    $('#user-settings').addClass('active');
    settings_form = true;
}

function hide_settings_form() {
    $('#settings_form').slideUp();
    $('#user-settings').removeClass('active');
    settings_form = false;
}
//=== END - SETTINGS ===========================================================

function toggle_play() {
    if (type != "cam" && type != "slide")
        return;
    var video = document.getElementById('main_video');
    if (type == 'slide' && document.getElementById('secondary_video')) {
        var video = document.getElementById('secondary_video');
    }
    if (video.paused) {
        video.play();
    } else {
        video.pause();
    }
}

function video_navigate(forwardRewind) {
    origin = get_origin();
    if (document.getElementById('secondary_video')) {
        if (type == 'slide') {

            var video = document.getElementById('secondary_video');
        } else {

            var video = document.getElementById('main_video');
        }
    } else {
        var video = document.getElementById('main_video');
    }
    var paused = video.paused;
    //  video.load();  
    //  video.addEventListener('loadedmetadata', function() {        
    //      this.currentTime = time;
    //  }, false);      
    video.currentTime = (forwardRewind == 'forward') ? video.currentTime + 15 : video.currentTime - 15;
    paused ? video.pause() : video.play();
    server_trace(new Array('4', 'video_' + forwardRewind, current_album, current_asset, duration, time, type, quality, origin));
}

function video_volume(upDown) {
    origin = get_origin();
    var video = document.getElementById('main_video');
    var volume = video.volume;
    if (volume < 1 && upDown == 'up') {
        volume = volume + 0.05;
    } else if (volume > 0 && upDown == 'down') {
        volume = volume - 0.05;
    }

    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').volume = volume;
    }
    video.volume = volume;
    server_trace(new Array('4', 'video_volume_' + upDown, current_album, current_asset, duration, time, type, quality, origin));

}

function toggle_mute() {
    origin = get_origin();
    var video = document.getElementById('main_video');
    video.muted = !video.muted;
    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').muted = !video.muted;
    }
    server_trace(new Array('4', 'video_mute', current_album, current_asset, duration, time, type, quality, video.muted, origin));
}

function video_link() {
    from_shortcut = false;
    $(".share-button").click();
}

function video_fullscreen(on) {
    origin = get_origin();
    if (on) {
        fullscreen = true;
        $('#video_player').css('width', '100%');
        $('#video_player').css('height', '100%');
        $('#video_player').css('position', 'fixed');
        pct = (100 - (panel_width * 100 / ($(window).width()))) + '%';
        $('video').css('width', (show_panel) ? pct : '100%');
        $('.fullscreen-button').addClass("active");
        if (bookmark_form) {
            pct = (100 - (5.51 + (225 * 100 / $(window).height()))) + '%';
            $('video').css('height', pct);
        }
        // bookmarks panel
        $('.panel-button').css('display', 'inline-block');
        $('#video_notifications').addClass('panel-active');
        panel_fullscreen();
        server_trace(new Array('4', 'video_fullscreen_enter', current_album, current_asset, duration, time, type, quality, origin));
    } else {
        fullscreen = false;
        $('#video_player').css('width', '640px');
        $('#video_player').css('height', '508px');
        $('#video_player').css('position', 'relative');
        $('video').css('width', '99.5%');
        $('.fullscreen-button').removeClass("active");
        if (bookmark_form) {
            $('video').css('height', '51%');
        }
        // bookmarks panel
        $('.panel-button').css('display', 'none');
        $('#video_notifications').removeClass('panel-active');
        panel_exit_fullscreen();
        server_trace(new Array('4', 'video_fullscreen_exit', current_album, current_asset, duration, time, type, quality, origin));
    }
}

function video_resize() {
    pct = (100 - (5.51 + (300 * 100 / $(window).height())));
    $('video').animate({'height': (fullscreen) ? pct + '%' : '34%'});
    if (fullscreen) {
        $('#div_right').animate({'height': (pct + 1) + '%'}, function() {
            panel_resize();
        });
    }
}

function video_resize_bookmark() {
    pct = (100 - (5.51 + (253 * 100 / $(window).height())));
    $('video').animate({'height': (fullscreen) ? pct + '%' : '43%'});
    if (fullscreen) {
        $('#div_right').animate({'height': (pct + 1) + '%'}, function() {
            panel_resize();
        });
    }
}

function panel_resize() {
    pct = (100 - (130 * 100 / $("#div_right").height())) + '%';
    $('#side_pane').css('height', pct);
    pct = (100 - ((2 * 55) * 100 / $(".side_pane_content").height())) + '%';
    $('.bookmark_scroll, .toc_scroll').css('height', pct);
    pct = (100 - ((2 * 63) * 100 / $(".side_pane_content").height())) + '%';
    $('.no_content').css('height', pct);
}

function panel_show() {
    panel_fullscreen();
    show_panel = true;
    pct = (100 - (panel_width * 100 / ($(window).width()))) + '%';
    $('video').animate({
        width: pct
    });
    $('#div_right').animate({
        right: '0px'
    });
}

function panel_hide() {
    panel_fullscreen();
    show_panel = false;
    $('#div_right').animate({
        right: '-300px'
    });

    $('video').animate({
        width: '100%'
    });
}

function toggle_panel() {
    origin = get_origin();
    if (show_panel) {
        panel_hide();
        server_trace(new Array('3', 'panel_hide', current_album, current_asset, duration, time, type, quality, origin));
    } else {
        panel_show();
        server_trace(new Array('3', 'panel_show', current_album, current_asset, duration, time, type, quality, origin));
    }
    $('.panel-button').toggleClass('active');
    $('#video_notifications').toggleClass('panel-active');
}

function panel_fullscreen() {
    $('#div_right').css('right', (show_panel) ? '0px' : '-300px');
    $('#div_right').css('position', 'fixed');
    pct = (100 - (5.51 + (225 * 100 / $(window).height())));
    $('#div_right').css('height', (bookmark_form) ? (pct + 1) + '%' : '92.6%');
    $('#div_right').css('background-color', '#F1F1F1');
    $('#side-pane-scroll-area').css('height', '100%');
    $('.side_pane_content').css('height', '100%');
    panel_resize();
    if (!show_panel) {
        $('#video_notifications').removeClass('panel-active');
    }
}

function panel_exit_fullscreen() {
    $('#div_right').css('position', 'relative');
    $('#div_right').css('right', '0px');
    $('#div_right').css('height', '100%');
    $('#div_right').css('background-color', '');
    $('#side_pane').css('height', '435px');
    $('#side-pane-scroll-area').css('height', '435px');
    $('.side_pane_content').css('height', '435px');
    $('.bookmark_scroll, .toc_scroll').css('height', '323px');
    $('.no_content').css('height', '307px');
    $('#div_right').css('display', 'block');
}

function toggle_shortcuts() {
    var action;
    origin = get_origin();
    shortcuts = !shortcuts;
    if (shortcuts)
        $('#video_shortcuts').css('height', '92.4%');
    $('.shortcuts').animate({'width': (shortcuts) ? 'show' : 'hide'}, function() {
        $('.shortcuts_tab a').toggleClass('active');
        if (!shortcuts)
            $('#video_shortcuts').css('height', '10%');
    });
    action = (shortcuts) ? 'show' : 'hide';
    server_trace(new Array('4', 'shortcuts_' + action, current_album, current_asset, duration, time, type, quality, origin));

}

function remove_notification_item(key) {
    timecode_array[key] = null;
    title_array[key] = null;
    $('#notif_'+key).hide();
}

function scrollTo(component) {
//    while(typeof $('#' + component)[0] == 'undefined')
//        $('#' + component)[0].scrollIntoView(true);
    if( typeof $('#' + component)[0] != 'undefined' )
        $('#' + component)[0].scrollIntoView(true);
}

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

function get_origin() {
    if (from_shortcut) { // a key has been pressed to run the action
        from_shortcut = false;
        return "from_shortcut";
    } else {
        return "from_button";
    }
}