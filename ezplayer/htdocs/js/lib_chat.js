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

var current_chat_panel = 'chat_messages';
/**
 * checks the chat message creation form before submitting it
 * @returns {Boolean}
 */
function chat_form_check() {
    var message = document.getElementById('chat_message').value;
    message = message.replace(/^\s+|\s+$/g, '');
    if (message == '' || message == '#question')
        return false;

    return true;
}

/**
 * Submits the chat message creation form to the server
 * @returns {undefined}
 */
function chat_form_submit() {
    $('#chat_timecode').val(Math.round(time));
    $.ajax({
        type: 'POST',
        url: 'index.php?action=chat_message_add',
        data: $('#submit_chat_form').serialize() ,
        success: function (response) {
            var chat_messages = $('#chat_messages');
            chat_messages.append(response);
            chat_scroll_to_end();
        }
    });
    $("#chat_message").val('');

}

/**
 * Retrieves the new messages since the last call.
 * Appends the new messages in the chat
 * @returns {undefined}
 */
function chat_messages_get_last() {
    var scroll = false;
    var chat_messages = $('#chat_messages');
    // verifies if the scoll bar is at the end of the div 
    if (chat_messages.scrollTop() + chat_messages.innerHeight() >= chat_messages[0].scrollHeight) {
        // the scroll bar is at the end, so we need to scroll at the end after refreshing the chat messages
        scroll = true;
    }
    $.ajax({
        type: 'POST',
        url: 'index.php?action=streaming_chat_get_last&album=' + current_album + '&asset=' + current_asset,
        success: function (response) {
            chat_messages.append(response);
            if (scroll) {
                // scroll to the end of the chat_messages div 
                chat_scroll_to_end();
            }
        }
    });
}

/**
 * Scrolls at the bottom of the chat / questions
 * @returns {undefined}
 */
function chat_scroll_to_end() {
    $('#chat_messages').scrollTop(100000000);
    $('#chat_qst_container').scrollTop(100000000);
}

/**
 * Inserts a keyword at the beginning of the message form to specify
 * who the message is aimed to.
 * Inserted keyword is of type : '@netid'
 * @param {type} netid
 * @returns {undefined}
 */
function chat_to(netid) {
    var chat_message = $("#chat_message");
    chat_message.focus();
    chat_message.val('@' + netid + ' ' + chat_message.val());
}

/**
 * Scrolls to the given div_id.
 * Aimed for 'chat_messages' | 'chat_questions' | 'chat_help'
 * @param {type} div_id
 * @returns {undefined}
 */
function chat_scroll_to(div_id) {
    $('#chat_menu ul li a').removeClass('active');
    $(div_id + '_button').addClass('active');
    $('#chat_wrapper').scrollTo(div_id);
    current_chat_panel = div_id;
}

/**
 * Inserts a hashtag '#question' in the message form
 * @returns {undefined}
 */
function chat_question_add() {
    var chat_message = $("#chat_message");
    chat_message.focus();
    chat_message.val(chat_message.val() + ' #question ');
}

/**
 * Appends a message in the questions panel
 * @param {type} author
 * @param {type} message
 * @returns {undefined}
 */
function chat_question_append(author, message, date) {
    var chat_qst = $("#chat_qst_container");
    console.log("author: " + author);
    console.log("message: " + message);
    var to_append =
            "<div class='chat_msg_wrapper'>" +
            "   <div class='chat_msg_info'>" +
            "       <b>" + author + "</b>" +
            "       <div class='chat_msg_date'>" +
            "           <i class='slash item-thread-slash'>//</i>" +
            "           <span style='font-style: italic; font-size: 11px;'>" + date + "</span>" +
            "       </div>" +
            "   </div>" +
            "   <div class='chat_msg' onclick='chat_date_toggle($(this));' > " +
            "       <div class='chat_msg_txt'> " +
            "           " + message +
            "       </div>" +
            "   </div>" +
            "</div>";
    chat_qst.append(to_append);
}

/**
 * Shows / hides the date of a message
 * @param {type} el
 * @returns {undefined}
 */
function chat_date_toggle(el) {
    var chat_date = el.prev().find(".chat_msg_date");
    chat_date.toggleClass('visible');
}

/**
 * Changes the current display of the chat
 * - a column on the right of the player
 * - a chat below the player
 * @returns {undefined}
 */
function chat_resize(){
    $("#main_player").toggleClass('small_display');
    $(".panel-button").toggleClass('active');
    player_streaming_fullscreen(fullscreen);
    chat_scroll_to(current_chat_panel);
}