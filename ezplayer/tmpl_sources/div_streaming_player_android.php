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
    paused = false;
    var poster = "images/Generale/¤poster-streaming¤.png";
    var is_camslide = <?php echo json_encode($asset_meta['record_type'] == 'camslide'); ?>;
    var main_stream_url = "<?php echo $m3u8_live_stream; ?>";
    var current_type = "<?php echo $_SESSION['current_type']; ?>";
    time = 0;


    // prevents memory leak in Safari
    function player_kill() {
        return true;
    }

    function switch_prepare(new_type) {
        if (new_type === current_type)
            return;
            switch_do();
    }

    function switch_do() {
        // Request to the server
        $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
        $.ajax({
            type: 'POST',
            url: 'index.php?action=streaming_config_update' + '&type=' + ((current_type == 'cam') ? 'slide' : 'cam'),
            success: function (response) {
                $('#streaming_config_wrapper').html(response);
                player_streaming_fullscreen(fullscreen);
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
<div id="streaming_config">
    <link rel="stylesheet" href="flowplayer/skin/skin.css">


    <div id="video_player" class="streaming remove_full">
        <div id="streaming_video">
            <video width="100%" height="523px" autoplay="true" src="<?php echo $m3u8_live_stream; ?>" type="application/x-mpegurl" controls="controls" poster="images/Generale/¤poster-streaming¤.png"></video>
        </div>
        <div class="video_controls streaming">
            <ul>

                <?php if ($asset_meta['record_type'] === 'camslide') {
    ?>
                    <li>
                        <a class="movie-button <?php echo ($_SESSION['current_type'] === 'cam') ? 'active' : ''; ?>" title="®Watch_video®" href="javascript:switch_prepare('cam');"></a>
                        <a class="slide-button <?php echo ($_SESSION['current_type'] === 'slide') ? 'active' : ''; ?>" title="®Watch_slide®" href="javascript:switch_prepare('slide');"></a>
                    </li>
                <?php
} ?>
                <li>
                    <a class="fullscreen-button" href="javascript:player_streaming_fullscreen(!fullscreen);" title="®Toggle_fullscreen®" ></a>
                </li>   
                <li>
                    <a class="panel-button active" title="®Chat_size®" href="javascript:chat_resize();"></a>
                </li>
            </ul>
        </div>
    </div>
</div>






