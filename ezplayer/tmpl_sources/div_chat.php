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

<div id="chat">
    <div id="chat_header">
        <span id="chat_logo"></span>
        <span>®Chat_header®</span>
    </div>
    <div id="chat_menu">
        <ul>
            <li><a id='chat_text_button' href="#chat_text" title="®Chat_messages®" class="active"><span>Messages</span></a></li>
            <li><a id='chat_questions_button' title="®Chat_questions®" href="#chat_questions"><span>#questions</span></a></li>
            <li><a id='chat_help_button' title="®Chat_help®" href="#chat_help"><span>®Help®</span></a></li>
        </ul>
    </div>
    <div id="chat_wrapper">
        <div id="chat_scrollable_area">
            <div id='chat_help'>

                <div class="chat_ad">
                    ®Chat_help_ad®
                </div>
                <ul>
                    <li><span><b>®Chat_date®</b></span> ®Chat_date_desc®</li>
                    <li><span><b>®Chat_ref®</b></span> ®Chat_ref_desc®</li>
                    <li><span><b>®Chat_url®</b></span> ®Chat_url_desc®</li>
                </ul>
            </div>
            <div id="chat_questions">
                <div class="chat_ad">
                    ®Chat_qst_ad®
                </div>
                <div id='chat_qst_container'>

                </div>
            </div>
            <div id="chat_text" >
                <div id="chat_messages">
                    <?php include_once template_getpath("div_chat_messages.php"); ?>
                </div>    
                <div class="chat_ad">
                    ®Chat_text_ad®
                </div>
                <form action="index.php" method="post" id="submit_chat_form" onsubmit="return false;">        
                    <input type="hidden" name="chat_album" id="chat_album" value="<?php echo $album; ?>"/>  
                    <input type="hidden" name="chat_asset" id="chat_asset" value="<?php echo $asset; ?>"/>
                    <input type="hidden" name="chat_timecode" id="chat_timecode" value=""/>
                    <textarea placeholder="®Chat_placeholder®" name="chat_message" id="chat_message"></textarea>
                </form>
                <div class="chat_button">
                    <a class="button" href="javascript: if(chat_form_check()) chat_form_submit();"><span>®Send®</span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#chat_message').keydown(function (e) {
        if (e.keyCode == 13) {
            if (!e.shiftKey) {
                e.preventDefault();
                if (chat_form_check()) {
                    chat_form_submit();
                }
            }
        }
    });

    // #chat_menu must exist before calling this 
    // (therefor, scripts are placed at the bottom of the page)
    $('#chat_menu').localScroll({
        target: '#chat_wrapper',
        axis: 'x',
        duration: 500
    });

    $('#chat_menu ul li a').click(function () {
        $('#chat_menu ul li a').removeClass('active');
        $(this).addClass('active');
        current_chat_panel = $(this).attr('href');
    });
</script>