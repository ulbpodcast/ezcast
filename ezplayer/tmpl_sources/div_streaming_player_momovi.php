<?php
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
?>
<script>
    function redirect_at_end() {
    }
</script>

<div id="streaming_config">
    <link href="momovi/static/css/video.css" rel="stylesheet" type="text/css">
    <!--[if IE 9]>
    <link rel="stylesheet" type="text/css" href="momovi/static/css/ie9.css">
    <![endif]-->

    <!--[if IE 8]>
    <link rel="stylesheet" type="text/css" href="momovi/static/css/ie8.css">
    <![endif]-->


    <div id="video_player" class="streaming remove_full">
        <div id="streaming_video"></div>
        <?php if ($asset_meta['record_type'] === 'camslide') {
    ?>
            <div class="video_controls streaming">
                <ul>

                    <li>
                        <a class="movie-button <?php echo ($_SESSION['current_type'] === 'cam') ? 'active' : ''; ?>" title="®Watch_video®" href="javascript:switch_prepare('cam');"></a>
                        <a class="slide-button <?php echo ($_SESSION['current_type'] === 'slide') ? 'active' : ''; ?>" title="®Watch_slide®" href="javascript:switch_prepare('slide');"></a>
                    </li>
                </ul>
            </div>
        <?php
} ?>
    </div>
</div>
<script>
    $.ajaxSetup({
        cache: false
    });

    function resizeVideo() {
        if ($('#momovi_video').length) {
            $("#momovi_video").width("930px");
            $("#momovi_video").height("525px");
        }
        if ($('#momovi_video_html5_api').length) {
            $("#momovi_video_html5_api").width("930px");
            $("#momovi_video_html5_api").height("525px");
        }
    }

    $(document).ready(function () {
        resizeVideo();
    });

    function newplayer(data) {
        var poster = data.poster

        var posterimg = "<div id=\"streaming_video\" class=\"streaming\" ><img id=\"momovi_video\" src=\"" + poster + "\"></div>";
        $("#streaming_video").replaceWith(posterimg);
        hlsplayer(data)
    }

    function checkm3u8available_helper(hlsdata, depth) {
        if (depth > 20) {
            alert("Could not open stream");
            return
        }
        var is_msie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
        if (is_msie) {
            runplayer(hlsdata);
        } else {
            var hlsurl = hlsdata.stream_url
            $.get(hlsurl, function (data, status) {
                if (data.indexOf(".ts") != -1 || data.indexOf("STREAM-INF") != -1) {
                    runplayer(hlsdata);
                } else {
                    depth = depth + 1;
                    setTimeout(function () {
                        checkm3u8available_helper(hlsdata, depth)
                    }, 350);
                }
            });
        }
    }

    function checkm3u8available(data) {
        var is_available = checkm3u8available_helper(data, 0);

    }

    function hlsplayer(data) {
        checkm3u8available(data);
    }
    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

    function runplayer(data) {
        var autoplay = "autoplay",
                loop = "",
                cast = "",
                controls = "<?php echo(in_array(strtolower($_SESSION['user_os']), array('linux', 'windows', 'os x')) ? "" : "controls"); ?>";
        //        controls = "";
        var hlsurl = data.stream_url;
        var poster = data.poster;
        var urlvars = getUrlVars();
        if (urlvars['autoplay'] != null) {
            if (urlvars['autoplay'] == "false") {
                autoplay = ""
            }
        }
        if (urlvars['loop']) {
            if (urlvars['loop'] == "true") {
                loop = "loop"
            }
        }

        if (urlvars['cast']) {
            if (urlvars['cast'] == "true") {
                cast = "cast"
            }
        }

        if (autoplay == "autoplay") {
            if (urlvars['controls']) {
                if (urlvars['controls'] == "false") {
                    controls = "";
                }
            }
        }
        var vidplayer = "<video " + controls + "  " + cast + "id=\"momovi_video\" class=\"video-js vjs-default-skin\" preload=\"none\" width=\"300px\" height=\"200px\" " + autoplay + " " + loop + " poster=\"" + poster + "\"    data-setup=\'{ }'\>  <source src=\"" + hlsurl + "\" type=\'video/mp4\' /></video>"
        $("#streaming_video").replaceWith(vidplayer);
        vjs.autoSetup();
        resizeVideo();
    }
</script>
<div id="jsonploader"></div>

<script language="JavaScript">
    var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    var is_msie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
    var is_safari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
    var is_idevice = (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i));
    var poster = "images/Generale/¤poster-streaming¤.png";

    var is_camslide = <?php echo json_encode($asset_meta['record_type'] == 'camslide'); ?>;
    var main_stream_url = "<?php echo $m3u8_live_stream; ?>";
    var current_type = "<?php echo $_SESSION['current_type']; ?>";

    newplayer({"stream_url": main_stream_url, "poster": poster});


    function player_kill() {
        $("video").each(function () {
            this.pause(); // can't hurt
            this.src = '';
            delete this; // @sparkey reports that this did the trick (even though it makes no sense!)
            $(this).remove(); // this is probably what actually does the trick
        });
        $("#streaming_video_wrapper").empty();
    }

    function switch_prepare(new_type) {
        if (new_type === current_type)
            return;
        switch_do();
    }

    function switch_do() {
        // Request to the server
        close_popup();
        player_kill();
        $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
        $.ajax({
            type: 'POST',
            url: 'index.php?action=streaming_config_update' + '&type=' + ((current_type == 'cam') ? 'slide' : 'cam'),
            success: function (response) {
                $('#streaming_config_wrapper').html(response);
            }
        });
    }

    function popup_streaming_live() {
        $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
        $.ajax({
            type: 'POST',
            url: 'index.php?action=live_stream_popup&display=video_switch',
            success: function (response) {
                $('#div_popup').html(response);
            }
        });
        $('#div_popup').reveal($(this).data());
    }

</script>





