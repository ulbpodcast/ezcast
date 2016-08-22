/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Changes the order of bookmarks (chron / reverse chron)
 * @param {type} panel the current bookmark tab (personal | official)
 * @param {type} order the new order (chron | reverse_chron)
 * @param {type} source the current page (asset | details)
 * @returns {undefined}
 */
function bookmarks_sort(panel, order, source) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmarks_sort',
        data: 'panel=' + panel + '&order=' + order + "&source=" + source + "&click=true",
        success: function (response) {
            $('#div_right').html(response);
        }
    });
}

/**
 * Submits the bookmark creation form to the server
 * @returns {undefined}
 */
function bookmark_form_submit() {
    var tab = document.getElementById('bookmark_source').value;
    (tab == 'personal') ? current_tab = 'main' : current_tab = 'toc';
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmark_add&click=true',
        data: $('#submit_bookmark_form').serialize(),
        success: function (response) {
            $('#div_right').html(response);
        }
    });
    // doesn't work in IE < 10
    //   ajaxSubmitForm('submit_bookmark_form', 'index.php', '?action=bookmark_add', 'div_right');  
    player_bookmark_form_hide(true);

}

/**
 * Prepares the fields of bookmark edition form
 * @param {type} index
 * @param {type} tab
 * @param {type} title
 * @param {type} description
 * @param {type} keywords
 * @param {type} level
 * @returns {undefined}
 */
function bookmark_edit(index, tab, title, description, keywords, level, timecode) {
    document.getElementById(tab + '_title_' + index).value = title;
    document.getElementById(tab + '_description_' + index).value = description;
    document.getElementById(tab + '_keywords_' + index).value = keywords;
    document.getElementById(tab + '_level_' + index).value = level;
    document.getElementById(tab + '_timecode_' + index).value = timecode;
    bookmark_edit_form_toggle(index, tab);
}

/**
 * shows/hides the bookmark edition form
 * @param {type} index
 * @param {type} tab
 * @returns {undefined}
 */
function bookmark_edit_form_toggle(index, tab) {
    $('#' + tab + index).toggle();
    $('#' + tab + '_info_' + index).toggle();
    $('#edit_' + tab + '_' + index).toggle();
    $('#' + tab + '_title_' + index).toggle();
    $('#'+ tab + '_' + index + ' .more').toggle();
    $('#'+ tab + '_' + index + ' .bookmark_options').toggle();
}

/**
 * Submits the bookmark edition form to the server
 * This function replaces an existing bookmark.
 * @param {type} index
 * @param {type} tab
 * @returns {undefined}
 */
function bookmark_edit_form_submit(index, tab) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmark_add&click=true',
        data: $('#submit_' + tab + '_form_' + index).serialize(),
        success: function (response) {
            $('#div_right').html(response);
        }
    });

}

/**
 * Sends the bookmark to be deleted to the server
 * @param {type} album
 * @param {type} asset
 * @param {type} timecode
 * @param {type} source
 * @param {type} tab
 * @returns {undefined}
 */
function bookmark_delete(album, asset, timecode, source, tab) {
    makeRequest('index.php', '?action=bookmark_delete' +
            '&album=' + album +
            '&asset=' + asset +
            '&timecode=' + timecode +
            '&source=' + source +
            '&tab=' + tab +
            "&click=true", 'div_right');
    close_popup();
}

/**
 * Deletes all bookmarks for the given album-asset
 * @param {type} album
 * @param {type} asset
 * @returns {undefined}
 */
function bookmarks_delete_all(album, asset) {
    makeRequest('index.php', '?action=bookmarks_delete_all' +
            '&album=' + album +
            '&asset=' + asset +
            "&click=true", 'div_center');
}

/**
 * Copies a personal bookmark to the official bookmarks
 * @param {type} album
 * @param {type} asset
 * @param {type} timecode
 * @param {type} title
 * @param {type} description
 * @param {type} keywords
 * @param {type} level
 * @param {type} source
 * @param {type} tab
 * @returns {undefined}
 */
function bookmark_copy(album, asset, timecode, title, description, keywords, level, source, tab) {
    current_tab = 'toc';
    makeRequest('index.php', '?action=bookmark_copy' +
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
    close_popup();
}

/**
 * Submits the xml file containing the bookmarks to the server
 * @returns {undefined}
 */
function bookmarks_upload_form_submit() {
    if (ie_browser) {
        document.forms["upload_bookmarks"].submit();
        $('#upload_target').load(function () {
            document.getElementById('div_popup').innerHTML = $("#upload_target").contents().find("body").html();
        });
    } else {
        ajaxUpload('XMLbookmarks', 'loadingfile', 'index.php', '?action=bookmarks_upload', 'div_popup');
    }
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
            
}

/**
 * Submits the list of bookmarks to be imported to the server
 * @param {type} source
 * @returns {undefined}
 */
function bookmarks_import_form_submit(source) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmarks_import&click=true&source=' + source,
        data: $('#select_import_bookmark_form').serialize(),
        success: function (response) {
            $('#div_right').html(response);
        }
    });

    close_popup();
}

/**
 * Submit the list of bookmarks to be deleted to the server
 * @param {type} source
 * @returns {undefined}
 */
function bookmarks_delete_form_submit(source) {
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmarks_delete&click=true&source=' + source,
        data: $('#select_delete_bookmark_form').serialize(),
        success: function (response) {
            $('#div_right').html(response);
        }
    });
    close_popup();
}

/**
 * Shows/hides more information about the bookmark
 * @param {type} index
 * @param {type} pane
 * @param {type} elem
 * @returns {undefined}
 */
function bookmark_more_toggle(index, pane, elem) {
    $('#' + pane + '_detail_' + index).slideToggle();
    $('#' + pane + '_' + index).toggleClass('active');
    elem.toggleClass('active');

    server_trace(new Array('3', elem.hasClass('active') ? 'bookmark_show' : 'bookmark_hide', current_album, current_asset, current_tab));
    var millisecondsToWait = 350;
    setTimeout(function () {
        $('.' + pane + '_scroll').scrollTo('#' + pane + '_' + index);
        // Whatever you want to do after the wait
    }, millisecondsToWait);
}

/**
 * Scrolls up/down the bookmarks list
 * @param {type} direction
 * @param {type} element
 * @returns {undefined}
 */
function bookmarks_scroll(direction, element) {
    var scrolled = $(element).scrollTop();
    if (direction == 'up') {
        var scroll = scrolled + 55;
    } else {
        var scroll = scrolled - 55;
    }

    $(element).animate({scrollTop: scroll}, "fast");
}

// ============== P O P - U P ============= //

/**
 * Renders a modal window for importing bookmarks (xml files)
 * @returns {undefined}
 */
function popup_bookmarks_import() {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmarks_upload_prepare',
        success: function (response) {
            $('#div_popup').html(response);
        }
    });
    $('#div_popup').reveal($(this).data());
}

/**
 * Renders a modal window containing a bookmark
 * @param {type} album
 * @param {type} asset
 * @param {type} timecode
 * @param {type} tab
 * @param {type} source
 * @param {type} display the action to be shown in the modal window (delete | copy | ...)
 * @returns {undefined}
 */
function popup_bookmark(album, asset, timecode, tab, source, display) {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmark_popup&click=true',
        data: 'album=' + album + '&asset=' + asset + '&timecode=' + timecode + '&tab=' + tab + '&source=' + source + '&display=' + display,
        success: function (response) {
            $('#div_popup').html(response);
        }
    });
    // doesn't work in IE < 10
    //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'div_popup');  

    $('#div_popup').reveal($(this).data());
}

/**
 * Renders a modal window containing a list of bookmarks
 * @param {type} album 
 * @param {type} asset
 * @param {type} tab
 * @param {type} source
 * @param {type} display the action to be shown in the modal window (delete | export | ...)
 * @returns {undefined}
 */
function popup_bookmarks(album, asset, tab, source, display) {
    $('#div_popup').html('<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>');
    $.ajax({
        type: 'POST',
        url: 'index.php?action=bookmarks_popup&click=true',
        data: 'album=' + album + '&asset=' + asset + '&tab=' + tab + '&source=' + source + '&display=' + display,
        success: function (response) {
            $('#div_popup').html(response);
        }
    });

    $('#div_popup').reveal($(this).data());
}

