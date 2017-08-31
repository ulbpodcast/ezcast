/* 
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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

var camslide; // determines if the record_type is camslide or not
var type; // current type of the video (cam | slide)
var quality; // current quality of the video (high | low)
var cam_loaded;
var slide_loaded;
var previous_seek_time = 0; // used for the seeked event
var last_time = 0;
var time = 0;   // current timecode of the video
var duration = 0; // duration of the video
var from_shortcut = false; // determines if the action has been done from keyboard shortcut
var trace_pause = 0; // traces are paused if this variable is not 0
var mouse_down = 0; // 0 when mouseUp / 1 when mouseDown - Used for the video seeked event
var panel_width = 231;
var seeked = false;
var video_forward = false;

var playing = false;
var log_playing_interval = video_split_time; // time until each "playing" log
var last_play_start = 0;

// variable describing which components are displayed on the page
var fullscreen = false;
var show_panel = false;
var bookmark_form = "";
var thread_form = false;
var comment_form = false;
var shortcuts = false;

/**
 * Duration of the notification (sec)
 * @type Number
 */
var notif_display_delay = 3;
/**
 * Number of threads to display at once
 * @type Number
 */
var notif_display_number = 3;

window.addEventListener("keyup", function (e) {
    if (lvl == 3) {
    
        var el = document.activeElement;
        if ((!el || (el.tagName.toLowerCase() != 'input' &&
                el.tagName.toLowerCase() != 'textarea'))) {
            from_shortcut = true;
            // focused element is not an input or textarea
            switch (e.keyCode) {
                case 32:  // space 
                    player_video_play_toggle();
                    break;
                case 66:  // 'b'
                    player_bookmarks_panel_toggle();
                    break;
                case 68:  // 'd'
                    if (is_logged)
                        player_thread_form_toggle();
                    break;
                case 78:  // 'n'
                    if (is_logged)
                        player_bookmark_form_toggle('personal');
                    break;
                case 107:
                case 187: // '+'
                    player_video_playbackspeed_set('up');
                    break;
                case 109:
                case 189: // '-'
                    player_video_playbackspeed_set('down');
                    break;
                case 82: // 'r'
                    player_shortcuts_toggle();
                    break;
                case 83:  // 's'
                    (type == 'cam') ?
                            player_video_type_set('slide') :
                            player_video_type_set('cam');
                    break;
                case 37:  // 'left arrow'
                    player_video_navigate('rewind');
                    break;
                case 39:  // 'right arrow'
                    player_video_navigate('forward');
                    break;
                case 27:  // 'esc'
                    if (ezplayer_mode === 'view_asset_streaming') {
                        player_streaming_fullscreen(false);
                    } else {
                        player_video_fullscreen(false);
                    }
                    break;
                case 70:  // 'f'
                    if (ezplayer_mode === 'view_asset_streaming') {
                        player_streaming_fullscreen(!fullscreen);
                    } else {
                        player_video_fullscreen(!fullscreen);
                    }
                    break;
                case 38:  // 'up arrow'
                    player_video_volume_set('up');
                    break;
                case 40:  // 'down arrow'
                    player_video_volume_set('down');
                    break;
                case 77:  // 'm'
                    player_video_mute_toggle();
                    break;
                case 79 : // 'o'
                    if (is_lecturer == true)
                        player_bookmark_form_toggle('official');
                    break;
            }
            
        } else if (e.keyCode == 27) {
            // leave focus when esc is pressed in input or text field
            $('input, textarea').blur();
        }
        
    }
}, false);

window.addEventListener("keydown", function (e) {
    var el = document.activeElement;
    // Default behavior is modified only on the player page, when used in an input field
    if (lvl == 3 && (!el || (el.tagName.toLowerCase() != 'input' &&
            el.tagName.toLowerCase() != 'textarea'))) {
        // space and arrow keys
        if ([32, 37, 38, 39, 40, 8].indexOf(e.keyCode) > -1) {
            e.preventDefault();
        }
    }
}, false);

// resizes the video when video is played fullscreen 
$(window).bind('resize', function (e) {
    window.resizeEvt;
    $(window).resize(function () {
        // wait for the window being resized
        clearTimeout(window.resizeEvt);
        window.resizeEvt = setTimeout(function () {
            if (fullscreen) {
                if (ezplayer_mode === 'view_asset_streaming') {
                    player_streaming_fullscreen(true);
                } else {
                    player_resize();
                    if (show_panel) {
                        player_bookmarks_panel_resize();
                        $('video').css('width', $(window).width() - panel_width + 'px');
                    }
                }

            }
        }, 250);
    });
});

/**
 * Adds all the listeners on the videos to handle various events
 * This function should be called immediately after the video tags have been created
 * @param {type} current_quality
 * @param {type} current_type
 * @param {type} start_time
 * @returns {undefined}
 */
function player_prepare(current_quality, current_type, start_time) {
    // Notification panel starts hidden
    $('#video_notifications').hide();

    // get all videos of the page
    var videos = document.getElementsByTagName('video');
    var max = videos.length;
    
    // determines whether it's a camslide or not
    camslide = (max === 2);
    // set the current type being played
    type = (current_type !== '') ? current_type : 'cam';
    quality = (current_quality !== '') ? current_quality : 'low';

    document.getElementById('video_player').onmousedown = function (elem) {
        ++mouse_down;
    };
    document.getElementById('video_player').onmouseup = function () {
        --mouse_down;
    };

    for (var i = 0; i < max; i++) {
        video_listener_add(videos[i], start_time);
    }

    // Browser fullscreen event
    // --> saves the trace
    $('video').bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
        var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
        var fullscreen = state ? true : false;
        var action = fullscreen ? 'browser_fullscreen_enter' : 'browser_fullscreen_exit';
        video_trace('4', action, false);
    });

    // if camslide and current type is slide, display the slide video player
    if (camslide && type == 'slide') {
        $('#main_video').hide();
        $('#secondary_video').show();
        $('.movie-button, .slide-button').toggleClass('active');
    }
}

function video_listener_add(video, start_time) {
    
    video.addEventListener("seeking", function () {
        var current_time = Math.round(this.currentTime);
        begin_seeked(current_time);
    }, true);
    
    // when the video is being played
    // --> saves the current time
    // --> loads the thread notifications to be displayed over the player
    video.addEventListener("timeupdate", function () {
        var current_time = Math.round(this.currentTime);
        video_event_update_time(this, current_time);
    });
    
    // when the video is played
    // --> hides the shortcuts panel
    // --> saves trace
    video.addEventListener('play', function () {
        video_event_play(this);
    }, true);
    
    // when the video is played
    // --> shows the shortcuts panel
    // --> saves trace
    video.addEventListener('pause', function () {
        video_event_pause(this);
    }, true);
    
    // when the volume of the video change
    // --> check if muted and adapt volume value
    video.addEventListener('volumechange', function() {
        video_event_volume(this);
    }, true);
    
    video.addEventListener("error", function (e) {
        video_event_error($(this));
    }, true);
    
    // When data are loaded
    // --> Sets a variable that states the video is loaded (for iOS and Android)
    // --> Saves the duration of the video
    video.addEventListener('loadeddata', function () {
        video_event_data_loaded(this);
    }, false);
    
    // If accessed from bookmark / thread
    if (start_time != 0) {
        time = start_time; // Update time
        // And add event when metaData is load to move at the good time
        video.addEventListener('loadedmetadata', function () {
            // Seek the video the the given start time
            ++trace_pause;
            this.currentTime = time;
        }, false);
    }
}


///////////////// EVENT /////////////////

function begin_seeked(current_time) {
    if(trace_pause > 0) {
        --trace_pause;
    } else {
        if(!seeked) {
            previous_seek_time = last_time;
            trace_video_play_time(previous_seek_time);
            
            time_code_update();
            seeked = true;
        }
        time = current_time;
    }
}

function end_seeked() {
    if(seeked) {
        if(previous_seek_time != time && !video_forward) {
            server_trace(new Array('4', 'video_seeked', current_album, current_asset, 
                    duration, previous_seek_time, time, type, quality));
        }
        last_play_start = time;
        seeked = false;
        video_forward = false;
    }
}

function video_event_update_time(video, current_time) {
    if(current_time == time) {
        return;
    }
    
    if((current_time - time) <= 1) {
        if((current_time - last_play_start) > log_playing_interval && !seeked) {
            trace_video_play_time();
        }
    
        if(seeked && !video.seeked && playing && !video.paused && mouse_down == 0) {
            end_seeked();
        }
    }
    
    last_time = time;
    time = current_time;
    
    threads_notif_display();
}

function trace_video_play_time(stop_time) {
    if(playing) {
        stop_time = (typeof stop_time !== 'undefined') ? stop_time : time;
        var play_time = Math.round(stop_time - last_play_start);
        
        if(play_time > 0 && play_time <= log_playing_interval) {
            server_trace(new Array('4', 'video_play_time', current_album, current_asset, current_asset_name, type, 
                last_play_start, play_time));
            last_play_start = time;
        }
    }
}

function video_event_play(video) {
    if (!shortcuts) {
        $(".shortcuts_tab").css('display', 'none');
    }
    
    if (trace_pause <= 0) {
        if(!video.seeking && !playing) {
            video_trace('4', 'video_play');
        }
    } else {
        --trace_pause;
    }
    
    if(!playing) {
        last_play_start = time;
    }
    playing = true;
}

function video_event_pause(video) {
    $(".shortcuts_tab").css('display', 'block');
    if(video.seeking || seeked) {
        return;
    }
    
    if (trace_pause <= 0) {
        if(playing) {
            trace_video_play_time();
            playing = false;
        }
        video_trace('4', 'video_pause');
    } else {
        --trace_pause;
    }
}

function video_event_volume(video) {
    if(video.muted && video.volume != 0) {
        video.volume = 0;
    }
}

// handles buffer errors that occur in Chrome after the following process:
// 1) play the video
// 2) pause the video
// 3) wait for ~5 min
// 4) play the video >> ERR_CONTENT_LENGTH_MISMATCH
function video_event_error(element) {
    element.load();
    currentTime = time;
    element.play();
}

function video_event_data_loaded(event) {
    duration = Math.round(event.duration);
    (event.getAttribute('id') == "main_video") ? cam_loaded = true : slide_loaded = true;
    document.getElementById("load_warn").style.display = 'none';
}



function threads_notif_display() {
    if (display_threads_notif) {
        var html_value = "<ul>";
        var i = 0;
        var timecode = time - notif_display_delay;

        if ((time % 3) == 0 && !mouse_down) {
            player_range_count_update(time, type);
        }
        // loads the thread notifications
        while (i < notif_display_number && timecode <= time) {
            if (timecode >= 0 && typeof threads_array[timecode] !== 'undefined') {
                for (var id in threads_array[timecode]) {
                    i++;
                    if(i > notif_display_number)
                        break;
                    
                    html_value += "<li id='notif_" + id + "' class ='notification_item'>" +
                            "<span class='span-link red' onclick='javascript:player_thread_notification_remove(" + 
                                timecode + ", " + id + ")' >x</span>" +
                            "<span class='notification-item-title' onclick='javascript:thread_details_update(" + id + ", true)'> " +
                            threads_array[timecode][id] + "</span>" +
                            "</li>";
                }
            }
            timecode++;
        }

        html_value += "</ul>";
        $('#notifications').html(html_value);
        if (i > 0)
            $('#video_notifications').slideDown();
        else
            $('#video_notifications').slideUp();
    }
}


// Sends the current time and type to the server to be saved as an array
function player_range_count_update(current_time, current_type) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=asset_range_count_update',
        data: {time: current_time,
            type: current_type,
            album: current_album,
            asset: current_asset}
    });
}

/**
 * switches from cam to slide and vice versa
 * @param {type} media_type cam | slide
 */
function player_video_type_set(media_type) {
    // only available for camslide
    if (!camslide || (media_type != "cam" && media_type != "slide") || media_type == type)
        return;
    
    if (quality != 'high' && quality != 'low')
        quality = 'low';

    var to_show;
    var to_hide;
    if (media_type == 'cam') {
        to_show = document.getElementById('main_video');
        to_hide = document.getElementById('secondary_video');
    } else {
        to_hide = document.getElementById('main_video');
        to_show = document.getElementById('secondary_video');
    }
    trace_video_play_time(); 
    
    // specific case for iOS
    if (/webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
        ++trace_pause;
        to_hide.pause();
        var load_warn_display;
        if ((media_type == 'cam' && cam_loaded) || (media_type == 'slide' && slide_loaded)) {
            ++trace_pause;
            to_show.currentTime = time;
            ++trace_pause;
            to_show.play();
            load_warn_display = 'none';
        } else {
            load_warn_display = 'block';
        }
        document.getElementById("load_warn").style.display = load_warn_display;
        
    // specific case for Android
    } else if (/Android/i.test(navigator.userAgent)) {
        ++trace_pause;
        to_hide.pause();
        ++trace_pause;
        to_show.currentTime = time;
        
    // in other browsers
    } else {
        ++trace_pause;
        to_show.currentTime = time;
        if (!to_hide.paused) {
            ++trace_pause;
            to_hide.pause();
            ++trace_pause;
            to_show.play();
        }
    }
    to_hide.style.display = 'none';
    to_show.style.display = 'block';
    to_show.volume = to_hide.volume;

    from = type;
    type = media_type;
    // button or keyboard shortcut
    origin = get_origin();
    server_trace(new Array('4', 'video_switch', current_album, current_asset, duration, 
            time, from, type, quality, origin));
    $('.movie-button, .slide-button').toggleClass('active');
}

/**
 * switches from high to low resolution and vice versa
 * @param {type} media_quality high | low
 * @returns {undefined}
 */
function player_video_quality_set(media_quality) {

    if (media_quality != "high" && media_quality != "low")
        return;
    if (media_quality == quality)
        return;

    var video;
    if (camslide && type == 'slide') {
        video = document.getElementById('secondary_video');
    } else {
        video = document.getElementById('main_video');
    }

    var source = document.getElementById('main_video_source');
    var paused = video.paused;
    var old_current_time = video.currentTime;
    // doesn't work in Safari 5
    // source.setAttribute('src', source.getAttribute(media + '_src')); 

    if (camslide) {
        document.getElementById('main_video').setAttribute('src', source.getAttribute(media_quality + '_cam_src'));
        document.getElementById('secondary_video').setAttribute('src', source.getAttribute(media_quality + '_slide_src'));
        document.getElementById('main_video').load();
        document.getElementById('secondary_video').load();
    } else {
        video.setAttribute('src', source.getAttribute(media_quality + '_' + type + '_src'));
        video.load();
    }
    video.addEventListener('loadedmetadata', function () {
        ++trace_pause;
        this.currentTime = old_current_time;
    }, false);
    ++trace_pause;
    paused ? video.pause() : video.play();
    
    quality = media_quality;

    $('.high-button, .low-button').toggleClass('active');
    server_trace(new Array('4', 'video_quality', current_album, current_asset, 
            duration, time, type, media_quality, quality));

}

/**
 * Called when a bookmark is clicked
 * Changes the current time of the video
 * @param {type} bookmark_time
 * @param {type} bookmark_type
 * @returns {undefined}
 */
function player_video_seek(bookmark_time, bookmark_type) {
    server_trace(new Array('4', 'video_bookmark_click', current_album, current_asset, 
            duration, time, bookmark_time, type, bookmark_type, current_tab, quality));

    if (bookmark_type != '' && type != bookmark_type) {
        player_video_type_set(bookmark_type);
    }
    var video;
    if (camslide && type == 'slide') {
        video = document.getElementById('secondary_video');
    } else {
        video = document.getElementById('main_video');
    }
    video.currentTime = bookmark_time;
    video.paused ? video.pause() : video.play();
}

/**
 * changes the video playback speed 
 * @param {type} rate up | down
 * @returns {undefined}
 */
function player_video_playbackspeed_set(rate) {
    origin = get_origin();

    var video = document.getElementById('main_video');
    var playback_speed = video.playbackRate;
    if (rate == 'up') {
        switch (playback_speed) {
            case 0.5 :
                playback_speed = 1.0;
                break;
            case 2 :
                return;
            default:
                playback_speed += 0.2;
                break;
        }
    } else {
        switch (playback_speed) {
            case 0.5 :
                return;
            case 1 :
                playback_speed = 0.5;
                break;
            default:
                playback_speed -= 0.2;
                break;
        }
    }
    playback_speed = playback_speed.toFixed(1);

    server_trace(new Array('4', 'playback_speed_' + rate, current_album, current_asset, 
            duration, time, type, quality, playback_speed, origin));

    if (camslide) {
        document.getElementById('secondary_video').playbackRate = playback_speed;
    }
    video.playbackRate = playback_speed;
    document.getElementById('toggleRate').innerHTML = (playback_speed + 'x');
}
/**
 * Click on the playback speed button
 * @returns {undefined}
 */
function player_playbackspeed_toggle() {
    origin = get_origin();
    var video = document.getElementById('main_video');
    var playback_speed = video.playbackRate;
    var rate;

    if (playback_speed == 0.5) {
        playback_speed = 1.0;
        rate = 'up';
    } else if (playback_speed < 2) {
        playback_speed += 0.2;
        rate = 'up';
    } else {
        playback_speed = 0.5;
        rate = 'down';
    }
    playback_speed = playback_speed.toFixed(1);
    server_trace(new Array('4', 'playback_speed_' + rate, current_album, current_asset, 
            duration, time, type, quality, playback_speed, origin));

    if (camslide) {
        document.getElementById('secondary_video').playbackRate = playback_speed;
    }
    video.playbackRate = playback_speed;
    document.getElementById('toggleRate').innerHTML = (playback_speed + 'x');
    if (playback_speed != 1) {
        document.getElementById('toggleRate').classList.add('active');
    } else {
        document.getElementById('toggleRate').classList.remove('active');
    }
}

function player_video_link() {
    document.getElementById('main_video').pause();
    if (camslide)
        document.getElementById('secondary_video').pause();
    video_trace('4', 'link_open');
}

// plays/pauses the current video
function player_video_play_toggle() {
    if (type != "cam" && type != "slide")
        return;
    if (camslide && type == 'slide') {
        var video = document.getElementById('secondary_video');
    } else {
        var video = document.getElementById('main_video');
    }
    if (video.paused) {
        video.play();
    } else {
        video.pause();
    }
}

// goes 15 seconds back/forward in the video
function player_video_navigate(forward_rewind) {
    var video;
    if (camslide && type == 'slide') {
        video = document.getElementById('secondary_video');
    } else {
        video = document.getElementById('main_video');
    }
    
    video_forward = true;
    video.currentTime = (forward_rewind == 'forward') ? video.currentTime + 15 : video.currentTime - 15;
    video.paused ? video.pause() : video.play();
    video_trace('4', 'video_' + forward_rewind);
}

// increase/decrease volume
function player_video_volume_set(up_down) {
    origin = get_origin();
    var video = document.getElementById('main_video');
    var volume = video.volume;
    if (volume < 1 && up_down == 'up') {
        volume = volume + 0.05;
    } else if (volume > 0 && up_down == 'down') {
        volume = volume - 0.05;
    }

    if (camslide) {
        document.getElementById('secondary_video').volume = volume;
    }
    video.volume = volume;
    video_trace('4', 'video_volume_' + up_down);

}

// mute video
function player_video_mute_toggle() {
    origin = get_origin();
    var video = document.getElementById('main_video');
    video.muted = !video.muted;
    if (camslide) {
        document.getElementById('secondary_video').muted = !video.muted;
    }
    server_trace(new Array('4', 'video_mute', current_album, current_asset, duration, 
            time, type, quality, video.muted, origin));
}

// =================== B O O K M A R K S   A C T I O N S ===================== //

// displays the bookmark creation form
function player_bookmark_form_show(source) {
    // Hide thread form if it's visible
    if (thread_form) {
        player_thread_form_hide(false);
        return;
    }

    $("#video_shortcuts").css("display", "none");
    var video;
    if (camslide && type == 'slide') {
        video = document.getElementById('secondary_video');
    } else {
        video = document.getElementById('main_video');
    }

    video.pause();
    time_code_update();
    document.getElementById('bookmark_type').value = type;
    document.getElementById('bookmark_source').value = source;
    // sets the form style according to the source (official | personal bookmarks)
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
    var window_height = $(window).height() - 39;
    $('video').animate({'height': (fullscreen) ? (window_height - 275) + 'px' : '250px'});
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();
    $('#bookmark_form').slideDown();
    bookmark_form = source;
}

// hides the bookmark creation form
function player_bookmark_form_hide(canceled) {
    var window_height = $(window).height() - 39;
    bookmark_form = "";
    $("#video_shortcuts").css("display", "block");
    $('video').animate({'height': (fullscreen) ? window_height + 'px' : '525px'});
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();

    $('#bookmark_form').slideUp();
    if (canceled) {
        document.getElementById('bookmark_title').value = '';
        document.getElementById('bookmark_description').value = '';
        document.getElementById('bookmark_keywords').value = '';
        document.getElementById('bookmark_level').value = '1';
    }
    $('.add-bookmark-button').removeClass("active");
    $('.add-toc-button').removeClass("active");
    $('#bookmark_form').removeClass("bookmark");
    $('#bookmark_form').removeClass("toc");

}

function player_bookmark_form_toggle(source) {
    from_shortcut = false;
    if (bookmark_form != "") {
        
        video_trace('4', 'bookmark_form_hide');
        if(bookmark_form == source) {
            player_bookmark_form_hide(false);
            return;
        }
    }
    
    origin = get_origin();
    server_trace(new Array('4', 'bookmark_form_show', current_album, current_asset, duration, 
        time, type, source, quality, origin));
    player_bookmark_form_show(source);
    $("#bookmark_title").focus();   
}

// ===================== T H R E A D S    A C T I O N S ======================= //

// displays thread creation form 
function player_thread_form_show() {
    // Creates the editor if it doesn't exist yet otherwise it will display 2 editors (or more)
    if (!$('#thread_desc_tinymce').hasClass('editor-created')) {
        tinymce.init({
            selector: "#thread_desc_tinymce",
            theme: "modern",
            width: 500,
            height: 100,
            language: 'fr_FR',
            plugins: 'paste',
            paste_as_text: true,
            paste_merge_formats: false,
            menubar: false,
            statusbar: false,
            toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignjustify | bullist numlist",
            style_formats: [
                {title: 'Titre 1', block: 'h1'},
                {title: 'Titre 2', block: 'h2'},
                {title: 'Titre 3', block: 'h3'},
                {title: 'Indice', inline: 'sub'},
                {title: 'Exposant', inline: 'sup'}
            ]
        });

        $('#thread_desc_tinymce').addClass('editor-created');
    }
    $("#video_shortcuts").css("display", "none");
    if (camslide && type == 'slide') {
        var video = document.getElementById('secondary_video');
    } else {

        var video = document.getElementById('main_video');
    }

    video.pause();
    document.getElementById('thread_timecode').value = Math.round(video.currentTime);

    $('.add-thread-button').removeClass("active");
    $('.add-thread-button').addClass("active");

    var window_height = $(window).height() - 39;
    $('video').animate({'height': (fullscreen) ? (window_height - 275) + 'px' : '250px'});
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();
    $('#thread_form').slideDown();
    thread_form = true;
}

// hides the thread creation form
function player_thread_form_hide(canceled) {
    var window_height = $(window).height() - 39;

    $("#video_shortcuts").css("display", "block");
    $('video').animate({'height': (fullscreen) ? window_height + 'px' : '525px'});
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();

    $('#thread_form').slideUp();
    if (canceled) {
        document.getElementById('thread_title').value = '';
        tinymce.get('thread_desc_tinymce').setContent('');
    }
    $('.add-thread-button').removeClass("active");

    thread_form = false;
}

/*
 * Hide or show thread form depending on his current state.
 */
function player_thread_form_toggle() {

    from_shortcut = false;
    if (thread_form) {
        video_trace('4', 'thread_form_hide');
        player_thread_form_hide(false);
        
    } else if (bookmark_form != "") {
        player_bookmark_form_hide(false);
        
    } else {
        video_trace('4', 'thread_form_show');
        player_thread_form_show();
        $("#thread_title").focus();
    }
}

/**
 * Removes a thread notification from the list
 * @param {type} timecode
 * @param {type} id
 * @returns {undefined}
 */
function player_thread_notification_remove(timecode, id) {
    delete threads_array[timecode][id];
    $('#notif_' + id).hide();
}

// =============== F U L L S C R E E N   D I S P L A Y   M O D E  ================ //

function player_streaming_fullscreen(on) {
    is_chat_panel = $('#main_player').hasClass('small_display');

    if (on) {
        var window_height = $(window).height();
        var window_width = $(window).width();

        fullscreen = true;
        $('#main_player').addClass('fullscreen');
        $('#streaming_config_wrapper').css({
            height: window_height - 42 + 'px',
            width: ((is_chat_panel) ? window_width - 234 + 'px' : '100%'),
        });
        $('#streaming_video').css({
            height: '100%'
        });

        if (is_chat_panel) {
            $('.search_wrapper.streaming').addClass('fullscreen');
            $('.search_wrapper.streaming').removeClass('regular');
            var chat_height = $('#chat_wrapper').height();
            $('#chat_messages, #chat_qst_container').css({
                height: chat_height - 260 + 'px'
            });
        }
        $('.fullscreen-button').addClass("active");
    } else {
        fullscreen = false;
        $('#main_player').removeClass('fullscreen');
        $('.fullscreen-button').removeClass("active");
        $('#streaming_config_wrapper').css({
            height: '',
            width: '',
        });
        $('#streaming_video').css({
            height: '523px'
        });
        $('.search_wrapper.streaming').removeClass('fullscreen');
        $('.search_wrapper.streaming').addClass('regular');

        $('#chat_messages, #chat_qst_container').css({
            height: ''
        });
    }
}

// Enters/exits fullscreen
function player_video_fullscreen(on) {
    fullscreen = (on === true); // check that 'on' is only a boolean
    var action = fullscreen ? 'video_fullscreen_enter' : 'video_fullscreen_exit';
    
    if (fullscreen) {
        $('.fullscreen-button').addClass("active");
    } else {
        $('.fullscreen-button').removeClass("active");
    }
    video_trace('4', action);
    player_resize();
}

// Modifies css values for fullscreen / normal display
function player_resize() {
    if (fullscreen) {
        $('body').css('overflow', 'hidden');
        $('#video_player').css('width', '100%');
        $('#video_player').css('height', '100%');
        $('#video_player').css('position', 'fixed');
        var window_height = $(window).height();
        var window_width = $(window).width();
        var extra_height = 41; // .video_controls = 39px
        var extra_width = 0;

        if (bookmark_form || thread_form) {
            extra_height += 275; // #bookmark_form & #thread_form = 275px
        }

        if (show_panel) { // bookmarks panel on the right
            extra_width += panel_width;
            $('#video_notifications').addClass('panel-active');
        }
        $('.video_controls').css('width', '100%');

        window_height -= extra_height;
        window_width -= extra_width;

        player_bookmarks_panel_fullscreen();
        $('#bookmark_form, #thread_form').css('width', window_width + 'px');

        $('video').css('width', window_width + 'px');
        $('video').css('height', window_height + 'px');

    } else {
        var width = 930;
        var height = 566;
        var extra_height = 41;
        var extra_width = 0;

        $('#video_player').css('height', height + 'px');
        $('#video_notifications').removeClass('panel-active');

        if (bookmark_form || thread_form) {
            extra_height += 275; // #bookmark_form & #thread_form = 275px
        }

        if (show_panel) {
            extra_width += panel_width;
        }
        height -= extra_height;
        width -= extra_width;

        $('.video_controls').css('width', width + 'px');

        player_bookmarks_panel_fullscreen_exit();

        $('#bookmark_form, #thread_form').css('width', width + 'px');

        $('video').css('width', width + 'px');
        $('video').css('height', height + 'px');
        $('#video_player').css('width', width + 'px');
        $('#video_player').css('position', 'relative');
        $('body').css('overflow', 'visible');
    }
}

// ============= B O O K M A R K S    A N D    S H O R T C U T S    P A N E L S  ================ //

// resizes the bookmarks panel (according to the div_right current size)
function player_bookmarks_panel_resize() {
    $('#side_pane').css('height', ($("#div_right").height() - 125) + 'px');
    $('.bookmark_scroll, .toc_scroll').css('height', ($(".side_pane_content").height() - 110) + 'px');
    $('.no_content').css('height', ($(".side_pane_content").height() - 126) + 'px');
}

// displays and adapts the size of bookmarks panel
function player_bookmarks_panel_show() {
    if (fullscreen) {
        $('video, #bookmark_form, #thread_form').animate({
            width: ($(window).width() - panel_width) + 'px'
        });
        $('#div_right').animate({
            right: '0px'
        });
        $('#video_notifications').addClass('panel-active');
    } else {
        $('#div_right').css('height', '652px');
        $('video, .video_controls, #bookmark_form, #thread_form, #video_player').animate({
            width: '699px'
        });
        $('#side_wrapper').animate({
            right: '0px'
        }, function () {
            $('#div_right').css('overflow', 'visible');
        });
    }
    $('.panel-button').addClass('active');
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();
    show_panel = true;
}

// hides the bookmarks panel
function player_bookmarks_panel_hide() {
    if (fullscreen) {
        $('#div_right').animate({
            right: '-300px'
        });
        $('video, #bookmark_form, #thread_form').animate({
            width: '100%'
        });
    } else {
        $('#div_right').css('overflow', 'hidden');
        $('video, .video_controls, #bookmark_form, #thread_form, #video_player').animate({
            width: '930px'
        });
        $('#side_wrapper').animate({
            right: '-232px'
        });

    }
    $('#video_notifications').removeClass('panel-active');
    $('.panel-button').removeClass('active');
    if (camslide)
        (type == 'slide') ? $('#main_video').hide() : $('#secondary_video').hide();
    show_panel = false;

}

// shows/hides the bookmarks panel
function player_bookmarks_panel_toggle() {
    var action = show_panel ? 'panel_hide' : 'panel_show';
    if (show_panel) {
        player_bookmarks_panel_hide();
    } else {
        player_bookmarks_panel_show();
    }
    video_trace('3', action);
}

// modifies the css values of the bookmarks panel for fullscreen mode
function player_bookmarks_panel_fullscreen() {
    var window_height = $(window).height() - 39;
    $('#div_right').css('right', (show_panel) ? '0px' : '-235px');
    $('#side_wrapper').css('right', '0px');
    $('#div_right').css('position', 'fixed');
    $('#div_right').css('height', window_height + 'px');
    $('#div_right').css('background-color', '#F1F1F1');
    $('#side-pane-scroll-area').css('height', '100%');
    $('.side_pane_content').css('height', '100%');
    player_bookmarks_panel_resize();

}

// modifies the css values of the bookmarks panel for normal display mode
function player_bookmarks_panel_fullscreen_exit() {
    $('#div_right').css('position', 'relative');
    $('#div_right').css('right', '0px');
    $('#div_right').css('overflow', (show_panel) ? 'visible' : 'hidden');
    $('#side_wrapper').css('right', (show_panel) ? '0px' : '-235px');
    $('#div_right').css('height', (show_panel) ? '652px' : '80px');
    $('#div_right').css('background-color', '');
    $('#side_pane').css('height', '530px');
    $('#side-pane-scroll-area').css('height', '530px');
    $('.side_pane_content').css('height', '530px');
    $('.bookmark_scroll, .toc_scroll').css('height', '418px');
    $('.no_content').css('height', '402px');
    $('#div_right').css('display', 'block');
}

// shows/hide the shortcuts panel
function player_shortcuts_toggle() {
    var action;
    
    shortcuts = !shortcuts;
    if (shortcuts) {
        $('#video_shortcuts').css('height', '92.4%');
    }
    
    $('.shortcuts').animate({'width': (shortcuts) ? 'show' : 'hide'}, function () {
        $('.shortcuts_tab a').toggleClass('active');
        if (!shortcuts) {
            $('#video_shortcuts').css('height', '10%');
        }
    });
    action = (shortcuts) ? 'show' : 'hide';
    video_trace('4', 'shortcuts_' + action);
}

function video_trace(lvl, action, add_origin) {
    add_origin = typeof add_origin !== 'undefined' ? add_origin : true;
    if(add_origin) {
        origin = get_origin();
        server_trace(new Array(lvl, action, current_album, current_asset, duration, 
                time, type, quality, origin));
    } else {
        server_trace(new Array(lvl, action, current_album, current_asset, duration, 
                time, type, quality));
    }
}

/**
 * Update the time code of bookmark, thread, ... when the time is change
 */
function time_code_update() {
    document.getElementById('bookmark_timecode').value = time;
    document.getElementById('thread_timecode').value = time;
}
