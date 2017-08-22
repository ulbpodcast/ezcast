/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Allows the user (student or admin) to choose the visibility of the new thread
 * (students only | students + teachers)
 * @param {type} value
 * @returns {undefined}
 */
function thread_visibility_choose(value) {
    if (value == 1)
        $('#thread_visibility').attr("checked", "checked")
    else
        $('#thread_visibility').removeAttr("checked");
    if (thread_form_check())
        thread_form_submit();
}

/**
 * Submits the thread creation form to the server
 * @returns {undefined}
 */
function thread_form_submit() {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_add&click=true',
        data: $('#submit_thread_form').serialize(),
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });

    player_thread_form_hide(true);
    close_popup();
}

/**
 * Prepares the thread edition form
 * @param {type} threadId
 * @returns {undefined}
 */
function thread_edit_form_prepare(threadId) {
    if (!$("#edit_thread_message_" + threadId + "_tinyeditor").hasClass('edited')) {
        tinymce.init({
            selector: "textarea#edit_thread_message_" + threadId + "_tinyeditor",
            theme: "modern",
            width: 555,
            height: 100,
            language: 'fr_FR',
            plugins: 'paste',
            paste_as_text: true,
            paste_merge_formats: false,
            menubar: false,
            statusbar: true,
            resize: true,
            toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignjustify | bullist numlist",
            style_formats: [
                {title: 'Titre 1', block: 'h1'},
                {title: 'Titre 2', block: 'h2'},
                {title: 'Titre 3', block: 'h3'},
                {title: 'Indice', inline: 'sub'},
                {title: 'Exposant', inline: 'sup'}
            ]
        });
        $("edit_thread_message_" + threadId + "_tinyeditor").addClass('edited')
    }
    if (tinymce.get("edit_thread_message_" + threadId + "_tinyeditor"))
        tinymce.get("edit_thread_message_" + threadId + "_tinyeditor").focus();
    $('#message-thread').hide();
    $('#thread-options').hide();
    $('#edit_thread_form_' + threadId).show();
    $('#edit_thread_title' + threadId).focus();

}

/**
 * Submits the thread edition form to the server
 * @param {type} threadId
 * @param {type} album
 * @param {type} asset
 * @returns {undefined}
 */
function thread_edit_form_submit(threadId, album, asset) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_edit&click=true',
        data: {
            'thread_id': threadId,
            'thread_title': document.getElementById('edit_thread_title_' + threadId).value,
            'thread_message': document.getElementById('edit_thread_message_' + threadId + '_tinyeditor').value,
            'thread_timecode': document.getElementById('edit_thread_timecode_' + threadId).value,
            'thread_album': album,
            'thread_asset': asset
        },
        success: function (response) {
            $('#edit_thread_form_' + threadId).hide();
            $('#threads').html(response);
            tinymce.remove('textarea');

        }
    });
}

/**
 * Cancels the thread edition form
 * @param {type} threadId
 * @returns {undefined}
 */
function thread_edit_form_cancel(threadId) {
    $('#edit_thread_form_' + threadId).hide();
    $('#message-thread').show();
    $('#thread-options').show();
}

/**
 * shows the comment creation form
 * @returns {undefined}
 */
function thread_comment_form_show() {
    // creates the text editor if it doesn't exist yet.
    if (!$('#comment_message_tinyeditor').hasClass('editor-created')) {
        tinymce.init({
            selector: "textarea#comment_message_tinyeditor",
            theme: "modern",
            height: 100,
            language: 'fr_FR',
            plugins: 'paste',
            paste_as_text: true,
            paste_merge_formats: false,
            menubar: false,
            statusbar: true,
            resize: true,
            toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignjustify | bullist numlist",
            style_formats: [
                {title: 'Titre 1', block: 'h1'},
                {title: 'Titre 2', block: 'h2'},
                {title: 'Titre 3', block: 'h3'},
                {title: 'Indice', inline: 'sub'},
                {title: 'Exposant', inline: 'sup'}
            ]
        });
        $('#comment_message_tinyeditor').addClass('editor-created');
    }

    if (tinymce.get('comment_message_tinyeditor'))
        tinymce.get('comment_message_tinyeditor').focus();
    $('#comment_form').slideDown();
    $("html, body").animate({scrollTop: $(document).height()}, 1000);
    comment_form = true;
    
    server_trace(new Array('4', 'comment_form_show', current_album, current_asset, duration, time, type));
}

/**
 * hides the comment creation form
 * @returns {undefined}
 */
function thread_comment_form_hide() {
    comment_form = false; // declared in lib_player.js
    $('#comment_form').slideUp();
    document.getElementById('comment_message_tinyeditor').value = '';
    
    server_trace(new Array('4', 'comment_form_hide', current_album, current_asset, duration, time, type));
}

/*
 * Hide or show comment form depending on its current state.
 */
function thread_comment_form_toggle() {
    if (comment_form) { // from lib_player.js
        thread_comment_form_hide();
    } else {
        thread_comment_form_show();
        $("#comment_message").focus();
    }
}

/**
 * Submits the comment creation form to the server
 * @returns {undefined}
 */
function thread_comment_form_submit() {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_comment_add&click=true',
        data: $('#submit_comment_form').serialize(),
        success: function (response) {
            thread_comment_form_hide();
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}

/**
 * Prepares the comment edition form
 * Creates a tinymce textarea if it doesn't exist yet
 * @param {type} comId
 * @returns {undefined}
 */
function thread_comment_edit_form_prepare(comId) {

    if (!$("#edit_comment_message_" + comId + "_tinyeditor").hasClass('edited')) {
        tinymce.init({
            selector: "textarea#edit_comment_message_" + comId + "_tinyeditor",
            theme: "modern",
            height: 100,
            language: 'fr_FR',
            plugins: 'paste',
            paste_as_text: true,
            paste_merge_formats: false,
            menubar: false,
            statusbar: true,
            resize: true,
            toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignjustify | bullist numlist",
            style_formats: [
                {title: 'Titre 1', block: 'h1'},
                {title: 'Titre 2', block: 'h2'},
                {title: 'Titre 3', block: 'h3'},
                {title: 'Indice', inline: 'sub'},
                {title: 'Exposant', inline: 'sup'}
            ]
        });
        $("#edit_comment_message_" + comId + "_tinyeditor").addClass('edited');
    }
    if (tinymce.get("edit_comment_message_" + comId + "_tinyeditor"))
        tinymce.get("edit_comment_message_" + comId + "_tinyeditor").focus();
    $('.comment-options').hide();
    $('#comment_message_id_' + comId).hide();
    $('#edit-options-' + comId).show();
    $('#edit_comment_' + comId).show();
    $('#comment_message_' + comId).focus();
}

/**
 * Submits the comment edition form to the server 
 * @param {type} comment_id
 * @returns {Boolean}
 */
function thread_comment_edit_form_submit(comment_id) {
    $('#edit_comment_message_' + comment_id + '_tinyeditor').html(tinymce.get('edit_comment_message_' + comment_id + '_tinyeditor').getContent());
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
        url: 'index.php?action=thread_comment_edit&click=true',
        data: {'comment_id': comment_id, 'comment_message': message, 'thread_id': thread, 'album': album, 'asset': asset},
        success: function (response) {
            thread_comment_edit_form_hide(comment_id);
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}

/**
 * Hides the comment edition form
 * @param {type} comId
 * @returns {undefined}
 */
function thread_comment_edit_form_hide(comId) {
    $('#comment_message_id_' + comId).show();
    $('.comment-options').show();
    $('#edit-options-' + comId).hide();
    $('#edit_comment_' + comId).hide();
}

/**
 * Cancels the comment edition form
 * @param {type} comId
 * @returns {undefined}
 */
function thread_comment_edit_form_cancel(comId) {
    tinymce.get("edit_comment_message_" + comId + "_tinyeditor").setContent($('#comment_message_id_' + comId).text());
    thread_comment_edit_form_hide(comId);
}

// displays comment form (for reply) and create editor if it doesn't exist yet
function comment_answer_form_show(id) {
    // checks whether the editor already exists or not.
    // if it doesn't exist, it creates it.
    if (!$('#answer_comment_message_' + id + '_tinyeditor').hasClass('editor-created')) {
        tinymce.init({
            selector: 'textarea#' + 'answer_comment_message_' + id + '_tinyeditor',
            theme: "modern",
            height: 100,
            language: 'fr_FR',
            plugins: 'paste',
            paste_as_text: true,
            paste_merge_formats: false,
            menubar: false,
            statusbar: true,
            resize: true,
            toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignjustify | bullist numlist",
            style_formats: [
                {title: 'Titre 1', block: 'h1'},
                {title: 'Titre 2', block: 'h2'},
                {title: 'Titre 3', block: 'h3'},
                {title: 'Indice', inline: 'sub'},
                {title: 'Exposant', inline: 'sup'}
            ]
        });

        $('#answer_comment_message_' + id + '_tinyeditor').addClass('editor-created');
    }
    if (tinymce.get('answer_comment_message_' + id + '_tinyeditor'))
        tinymce.get('answer_comment_message_' + id + '_tinyeditor').focus();
    $('.comment-options').hide();

    $('#answer_comment_form_' + id).slideDown();
    $("#answer_comment_message_" + id).focus();
    server_trace(new Array('4', 'answer_form_show', current_album, current_asset, duration, time, type, id));
}

/**
 * hides the comment answer form
 * @param {type} id
 * @returns {undefined}
 */
function comment_answer_form_hide(id) {
    $('.comment-options').show();
    $('#answer_comment_form_' + id).slideUp();
    document.getElementById('answer_comment_message_' + id + '_tinyeditor').value = '';
    $('#answer_comment_message_' + id + '_tinyeditor').addClass('editor-created');
    server_trace(new Array('4', 'answer_form_hide', current_album, current_asset, duration, time, type, id));
}

/**
 * Creates a reply for the given comment
 * @param {type} id
 * @returns {undefined}
 */
function comment_answer_form_submit(id) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=comment_reply_add&click=true',
        data: {'answer_message': document.getElementById('answer_comment_message_' + id + '_tinyeditor').value, 'answer_parent': document.getElementById('answer_parent_' + id).value, 'thread_id': document.getElementById('answer_thread_' + id).value, 'answer_nbChilds': document.getElementById('answer_nbChilds_' + id).value, 'album': document.getElementById('answer_album').value, 'asset': document.getElementById('answer_asset').value},
        success: function (response) {
            comment_answer_form_hide(id);
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}

/**
 * Refreshes the list of threads
 * @param {type} refresh
 * @returns {undefined}
 */
function threads_list_update(refresh) {
    var trace_action;
    if (refresh) {
        trace_action = 'thread_list_refresh';
    } else {
        trace_action = 'thread_list_back';
    }
    server_trace(new Array('3', trace_action, current_album, current_asset));
    $.ajax({
        type: 'POST',
        url: 'index.php?action=view_threads_list&click=true',
        data: {'album': current_album, 'asset': current_asset},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}

/**
 * Refreshes the thread details
 * @param {type} thread_id
 * @param {type} from_notif
 * @returns {undefined}
 */
function thread_details_update(thread_id, from_notif) {
    var trace_action;
    if (from_notif) {
        trace_action = 'thread_detail_from_notif';
    } else {
        trace_action = 'thread_detail_refresh';
    }
    server_trace(new Array('3', trace_action, current_album, current_asset, thread_id));
    $.ajax({
        type: 'POST',
        url: 'index.php?action=view_thread_details&click=true',
        data: {'thread_id': thread_id},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
            $.scrollTo('#threads');
        }
    });
}

/**
 * Sends the thread to be deleted to the server
 * @param {type} thread_id
 * @param {type} album
 * @param {type} asset
 * @returns {undefined}
 */
function thread_delete(thread_id, album, asset) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_delete&click=true',
        data: {'thread_id': thread_id, 'thread_album': album, 'thread_asset': asset},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
    close_popup();
}

/**
 * Sends the comment to be deleted to the server
 * @param {type} thread_id
 * @param {type} comment_id
 * @returns {undefined}
 */
function thread_comment_delete(thread_id, comment_id) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_comment_delete&click=true',
        data: {'thread_id': thread_id, 'comment_id': comment_id},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
    close_popup();
}

/**
 * Shows/hides more information about the thread in the threads list
 * @param {type} _id
 * @returns {undefined}
 */
function thread_more_toggle(_id) {
    if ($('#hidden-item-thread-' + _id).is(":hidden")) {
        $('#hidden-item-thread-' + _id).slideDown();
        $('.more-button-' + _id).addClass("active");
    } else {
        $('#hidden-item-thread-' + _id).slideUp();
        $('.more-button-' + _id).removeClass("active");
    }
}

/**
 * Adds a vote on a comment
 * @param {type} user
 * @param {type} comment
 * @param {type} type
 * @returns {undefined}
 */
function thread_comment_vote(user, comment, type) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_comment_vote&click=true',
        data: {'login': user, 'comment': comment, 'vote_type': type},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}

/**
 * Adds/removes an approval on a comment
 * @param {type} comment
 * @returns {undefined}
 */
function thread_comment_approve(comment) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_comment_approve&click=true',
        data: {'approved_comment': comment},
        success: function (response) {
            $('#threads').html(response);
            tinymce.remove('textarea');
        }
    });
}
// ============== P O P - U P ================ //

/**
 * Renders a modal window for thread visibility choice
 * @returns {undefined}
 */
function popup_thread_visibility() {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_visibility',
        success: function (response) {
            $('#div_popup').html(response);
        }
    });
    $('#div_popup').reveal($(this).data());
}

/**
 * Renders a modal window containing the thread information
 * 
 * @param {type} thread_id
 * @param {type} display the action to be shown in the modal window (delete | ...)
 * @returns {undefined}
 */
function popup_thread(thread_id, display) {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_popup&click=true',
        data: 'thread_id=' + thread_id + '&display=' + display,
        success: function (response) {
            $('#div_popup').html(response);
        }
    });
    $('#div_popup').reveal($(this).data());
}

/**
 * Renders a modal window containing the comment information
 * 
 * @param {type} comment_id
 * @param {type} display the action to be shown in the modal window (delete | ...)
 * @returns {undefined}
 */
function popup_thread_comment(comment_id, display) {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=thread_comment_popup&click=true',
        data: 'comment_id=' + comment_id + '&display=' + display,
        success: function (response) {
            $('#div_popup').html(response);
        }
    });
    $('#div_popup').reveal($(this).data());
}