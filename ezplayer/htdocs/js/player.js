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

window.addEventListener("keyup", function(e) {
    var el = document.activeElement;

    if (lvl == 3 && (!el || (el.tagName.toLowerCase() != 'input' &&
            el.tagName.toLowerCase() != 'textarea'))) {
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
                video_navigate('backward');
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
        if ([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
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
    var videos = document.getElementsByTagName('video');
    for (var i = 0, max = videos.length; i < max; i++) {
        videos[i].addEventListener("seeked", function() {
            document.getElementById('bookmark_timecode').value = Math.round(this.currentTime);
        }, false);
    }
    var elem = media.split('_');
    quality = elem[0];
    type = elem[1];

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
        to_hide.pause();
        if ((media_type == 'cam' && cam_loaded) || (media_type == 'slide' && slide_loaded)) {
            to_show.currentTime = oldCurrentTime;
            to_show.play();
            document.getElementById("load_warn").style.display = 'none';
        } else {
            document.getElementById("load_warn").style.display = 'block';
        }
    } else if (/Android/i.test(navigator.userAgent)) {
        to_hide.pause();
        to_show.currentTime = oldCurrentTime;
    } else {
        to_show.currentTime = oldCurrentTime;
        if (!to_hide.paused) {
            to_hide.pause();
            to_show.play();
        }
    }
    to_hide.style.display = 'none';
    to_show.style.display = 'block';
    var elem = media.split('_');
    quality = elem[0];
    type = elem[1];
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
    paused ? video.pause() : video.play();
    var elem = media.split('_');
    quality = elem[0];
    type = elem[1];
    $('.high-button, .low-button').toggleClass('active');

}

function seek_video(time, bookmark_type) {
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
    video.currentTime = time;
    paused ? video.pause() : video.play();
}

function video_playbackspeed(rate) {
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

    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').playbackRate = playbackSpeed;
    }
    video.playbackRate = playbackSpeed;
    document.getElementById('toggleRate').innerHTML = (playbackSpeed.toFixed(1) + 'x');
}

function toggle_playbackspeed() {
    var video = document.getElementById('main_video');
    var playbackSpeed = video.playbackRate;

    if (playbackSpeed == 0.5) {
        playbackSpeed = 1;
    } else if (playbackSpeed < 2) {
        playbackSpeed = playbackSpeed + 0.2;
    } else {
        playbackSpeed = 0.5;
    }

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

    video.pause();
    document.getElementById('bookmark_timecode').value = Math.round(video.currentTime);
    document.getElementById('bookmark_source').value = source;
    document.getElementById('bookmark_type').value = type;
    if (source == 'official') {
        $('.add-bookmark-button').removeClass("active");
        $('.add-toc-button').addClass("active");
    } else {
        $('.add-toc-button').removeClass("active");
        $('.add-bookmark-button').addClass("active");
    }
    //  $('video').css('height', (fullscreen) ? '70%' : '50%');
    video_resize();
    $('#bookmark_form').slideDown();
    bookmark_form = true;

}


function hide_bookmark_form() {
    bookmark_form = false;
    $("#video_shortcuts").css("display", "block");
    $('video').animate({'height': '92.12%'});
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

}

function toggle_bookmark_form(source) {
    if (bookmark_form) {
        hide_bookmark_form();
    } else {
        show_bookmark_form(source);
        $("#bookmark_title").focus();
    }
}

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

function video_navigate(forwardBackward) {
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
    video.currentTime = (forwardBackward == 'forward') ? video.currentTime + 15 : video.currentTime - 15;
    paused ? video.pause() : video.play();
}

function video_volume(upDown) {
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
}

function toggle_mute() {
    var video = document.getElementById('main_video');
    video.muted = !video.muted;
    if (document.getElementById('secondary_video')) {
        document.getElementById('secondary_video').muted = !video.muted;
    }
}

function video_link() {
    $(".share-button").click();
}

function video_fullscreen(on) {
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
        panel_fullscreen();
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
        panel_exit_fullscreen();
    }
}

function video_resize() {
    pct = (100 - (5.51 + (225 * 100 / $(window).height())));
    $('video').animate({'height': (fullscreen) ? pct + '%' : '51%'});
    if (fullscreen) {
        $('#div_right').animate({'height': (pct + 1) + '%'}, function() {
            panel_resize()
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
    if (show_panel) {
        panel_hide();
    } else {
        panel_show();
    }
    $('.panel-button').toggleClass('active');
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
    shortcuts = !shortcuts;
    $('.shortcuts').animate({'width': (shortcuts) ? 'show' : 'hide'}, function() {
        $('.shortcuts_tab a').toggleClass('active');
    });
}