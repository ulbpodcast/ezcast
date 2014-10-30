<!doctype html>

<!--
Main template.
This template is the main frame, the content divs are dynamically filled as the user clicks.

WARNING: Please call template_repository_path() BEFORE including this template
-->
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
        <title>®ezplayer_page_title®</title>
        <link rel="shortcut icon" type="image/ico" href="images/Generale/favicon.ico" />
        <link rel="apple-touch-icon" href="images/ipadIcon.png" /> 
        <link rel="stylesheet" type="text/css" href="css/ezplayer_style.css" />
        <link rel="stylesheet" type="text/css" href="css/reveal.css" />

        <script type="text/javascript" src="js/httpRequest.js"></script>      
        <script type="text/javascript" src="js/jQuery/jquery-1.6.2.min.js"></script>
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
            
            ZeroClipboard.setMoviePath( './swf/ZeroClipboard10.swf' );  
                              
            $(document).ready(function(){
            
                $('#assets_button, .bookmarks_button, .toc_button').localScroll({
                    target:'#side_pane',
                    axis: 'x'
                });
                // import/export menu closes when click outside
                $("*", document.body).click(function(e){
                    if ((e.target.id != "bookmarks_actions") && !$(e.target).hasClass("menu-button") && ($("#bookmarks_actions").css("display") != "none")){
                        $("#bookmarks_actions").css("display","none");
                        $(".settings.bookmarks a.menu-button").toggleClass('active')
                    } else if ((e.target.id != "tocs_actions") && !$(e.target).hasClass("menu-button") && ($("#tocs_actions").css("display") != "none")){
                        $("#tocs_actions").css("display","none");
                        $(".settings.toc a.menu-button").toggleClass('active')
                    }
                });
            });
                
            // Links an instance of clipboard to its position 
            function copyToClipboard(id, tocopy, width) {
                if (width == null || width == '' || width == 0) width = 200;
                if (id == '#share_clip'){
                    clippy = new ZeroClipboard.Client();
                    clip = clippy;
                } else {
                    clip = new ZeroClipboard.Client();                    
                }
                clip.setText('');
                clip.addEventListener('mouseDown', function(){
                    // window.alert("copy ok");        
                    clip.setText(tocopy);
                });
                clip.addEventListener( 'onComplete', function(){
                    alert("®Content_in_clipboard®");
                });
      
                // Set the text to copy in the clipboard
                clip.setText(tocopy);
                $(id).html( clip.getHTML(width, 30) );
            }
            
            function show_album_assets(album, token) {
                // the side pane changes to display the list of all assets contained in the selected album
                current_album = album;
                current_token = token;

                // Getting the content from the server, and filling the div_album_header with it
                document.getElementById('div_center').innerHTML = '<div style="text-align: center;"><img src="images/loading_white.gif" alt="loading..." /></div>';
                
                makeRequest('index.php', '?action=view_album_assets&album=' + album + '&token=' + token + '&click=true' , 'div_center');
            }
            
            function show_asset_details(album, asset, asset_token){
                current_album = album;
                current_asset = asset;
                                
                makeRequest('index.php', '?action=view_asset_details&album=' + album + '&asset=' + asset + '&asset_token=' + asset_token + '&click=true' , 'div_center');
               
            }
            
            function show_asset_bookmark(album, asset, timecode, type){
                current_album = album;
                current_asset = asset;
                                
                makeRequest('index.php', '?action=view_asset_bookmark&album=' + album + '&asset=' + asset + '&t=' + timecode + '&type=' + type +'&click=true' , 'div_center');
                               
            }
            
            function show_search_albums(){
                if ($('#album_radio').is(':checked')){
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
   
            function check_bookmark_form(){
                var timecode = document.getElementById('bookmark_timecode');
                var level = document.getElementById('bookmark_level');
                
                if (isNaN(timecode.value)
                    ||timecode.value == ''
                    || timecode.value < 0){        
                    window.alert('®Bad_timecode®');
                    return false;
                }
    
                if (isNaN(level.value)
                    || level.value < 1
                    || level.value > 3){
                    window.alert('®Bad_level®');
                    return false;
                }
                return true;
            }
   
            function check_edit_bookmark_form(index, tab){
                var timecode = document.getElementById(tab + '_timecode_' + index);
                var level = document.getElementById(tab + '_level_' + index);
                
                if (timecode.value == ''
                    || timecode.value < 0){        
                    window.alert('®Bad_timecode®');
                    return false;
                }
    
                if (isNaN(level.value)
                    || level.value < 1
                    || level.value > 3){
                    window.alert('®Bad_level®');
                    return false;
                }
                return true;
            }              
            
            function sort_bookmarks(panel, order, source){ 
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=sort_asset_bookmark', 
                    data:'panel=' + panel + '&order=' + order + "&source=" + source + "&click=true", 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //     ajaxSubmitForm('submit_' + tab + '_form_' + index, 'index.php', '?action=add_asset_bookmark', 'div_right');  
                
            }
            
            function submit_bookmark_form(){ 
                var tab = document.getElementById('bookmark_source').value;
                (tab == 'custom') ? current_tab = 'main' : current_tab = 'toc';
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=add_asset_bookmark&click=true', 
                    data:$('#submit_bookmark_form').serialize(), 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('submit_bookmark_form', 'index.php', '?action=add_asset_bookmark', 'div_right');  
                hide_bookmark_form();
                
            }
            
            function submit_edit_bookmark_form(index, tab){ 
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=add_asset_bookmark&click=true', 
                    data:$('#submit_' + tab + '_form_' + index).serialize(), 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //     ajaxSubmitForm('submit_' + tab + '_form_' + index, 'index.php', '?action=add_asset_bookmark', 'div_right');  
                
            }
                        
            function submit_search_form(){ 
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=search_bookmark&click=true', 
                    data:$('#search_form').serialize(), 
                    success: function(response) {    
                        $('#popup_search_result').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'popup_search_result');  
        
                $('#popup_search_result').reveal($(this).data());                
            }     
            
            function search_keyword(keyword){ 
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=search_bookmark&click=true', 
                    data:'search=' + keyword + '&target=global&albums%5B%5D=&fields%5B%5D=keywords&tab%5B%5D=official&tab%5B%5D=custom&level=0', 
                    success: function(response) {    
                        $('#popup_search_result').html(response);
                    }
                });
                // doesn't work in IE < 10
                //        ajaxSubmitForm('search_form', 'index.php', '?action=search_bookmark', 'popup_search_result');  
        
                $('#popup_search_result').reveal($(this).data());                
            }
            
            function submit_import_bookmarks_form(source){
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=import_bookmarks&click=true&source=' + source, 
                    data:$('#select_import_bookmark_form').serialize(), 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('select_import_bookmark_form', 'index.php', '?action=import_bookmarks'+ 
                //       '&source=' + source, 'div_right');    
                close_popup();
            }
            
            function submit_delete_bookmarks_form(source){
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=delete_bookmarks&click=true&source=' + source, 
                    data:$('#select_delete_bookmark_form').serialize(), 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //   ajaxSubmitForm('select_delete_bookmark_form', 'index.php', '?action=delete_bookmarks'+ 
                //       '&source=' + source, 'div_right');    
                close_popup();
            }
            
            function submit_delete_tocs_form(source){                
                $.ajax({
                    type:'POST', 
                    url: 'index.php?action=delete_bookmarks&click=true&source=' + source, 
                    data:$('#select_delete_toc_form').serialize(), 
                    success: function(response) {    
                        $('#div_right').html(response);
                    }
                });
                // doesn't work in IE < 10
                //    ajaxSubmitForm('select_delete_toc_form', 'index.php', '?action=delete_bookmarks'+ 
                //        '&source=' + source, 'div_right');    
                close_popup();
            }
            
            function close_popup(){
                var e = jQuery.Event("click"); 
                $(".reveal-modal-bg").trigger(e); // trigger it on document
            }
            
            function edit_bookmark(index, tab, title, description, keywords, level){
                document.getElementById(tab + '_title_' + index).value=title;
                document.getElementById(tab + '_description_' + index).value=description;
                document.getElementById(tab + '_keywords_' + index).value=keywords;
                document.getElementById(tab + '_level_' + index).value=level;
                toggle_edit_bookmark_form(index, tab);
            }
            
            function toggle_edit_bookmark_form(index, tab){
                $('#' + tab + index).toggle();
                $('#' + tab + '_info_' + index).toggle();
                $('#edit_' + tab + '_' + index).toggle();
                $('#' + tab + '_title_' + index).toggle();
            }
            
            function remove_bookmark(album, asset, timecode, source, tab){               
                makeRequest('index.php', '?action=remove_asset_bookmark'+ 
                    '&album=' + album + 
                    '&asset=' + asset +
                    '&timecode=' + timecode +
                    '&source=' + source +
                    '&tab=' + tab +
                    "&click=true", 'div_right');
            }
            
            function remove_bookmarks(album, asset){               
                makeRequest('index.php', '?action=remove_asset_bookmarks'+ 
                    '&album=' + album + 
                    '&asset=' + asset +
                    "&click=true", 'div_center');
            }
            
            function copy_bookmark(album, asset, timecode, title, description, keywords, level, source, tab){               
                makeRequest('index.php', '?action=copy_bookmark'+ 
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
            
            function delete_album_token(album){
                makeRequest('index.php', '?action=delete_album_token'+ 
                    '&album=' + album +
                    '&click=true', 'div_center');                
            }
            
            function move_album_token(index, upDown){
                makeRequest('index.php', '?action=move_album_token'+ 
                    '&index=' + index + '&up_down=' + upDown + "&click=true", 'div_center');                
            }
            
            function toggle_detail(index, pane, elem){
                $('#' + pane + '_detail_' + index).slideToggle();
                $('#' + pane + '_' + index).toggleClass('active');
                elem.toggleClass('active');
                var millisecondsToWait = 350;
                setTimeout(function() {
                    $('.' + pane + '_scroll').scrollTo('#' + pane + '_' + index);
                    // Whatever you want to do after the wait
                }, millisecondsToWait);
            }

            function toggle(elem){
                $(elem).toggle(200);
            }
            
            function toggle_checkboxes(source, target) {
                var checkboxes = document.getElementsByName(target);
                for(var i = 0; i < checkboxes.length; i++)
                    checkboxes[i].checked = source.checked;
            }
            
            function scroll(direction, element) {  
                var scrolled = $(element).scrollTop();
                if (direction == 'up') {
                    var scroll = scrolled + 55;
                }else{
                    var scroll = scrolled - 55;
                }

                $(element).animate({ scrollTop: scroll }, "fast");
            }
            
            function setActivePane(elem){
                if (elem == '.bookmarks_button'){                    
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
                if (!W3CDOM) return;
                var fakeFileUpload = document.createElement('div');                
                fakeFileUpload.className = 'fakefile';
                var input = document.createElement('input');
                input.style.width='140px';
                fakeFileUpload.appendChild(input);
                var span = document.createElement('span');
                span.innerHTML='®select®';
                fakeFileUpload.appendChild(span);
                var x = document.getElementsByTagName('input');
                for (var i=0;i<x.length;i++) {
                    if (x[i].type != 'file') continue;
                    if (x[i].parentNode.className != 'fileinputs') continue;
                    x[i].className = 'file hidden';
                    var clone = fakeFileUpload.cloneNode(true);
                    x[i].parentNode.appendChild(clone);
                    x[i].relatedElement = clone.getElementsByTagName('input')[0];
                    x[i].onchange = x[i].onmouseout = function () {
                        this.relatedElement.value = this.value;
                    }
                }
            }
            
            function check_upload_form() {
                var file = document.getElementById('loadingfile').value;
                if (file == ''){
                    window.alert('®No_file®');
                    return false;
                } else {
                    var ext =  file.split('.').pop();
                    var extensions= <?php
global $valid_extensions;
echo json_encode($valid_extensions);
?>;         
                 
            // check if extension is accepted
            var found = false;
            for (var i = 0; i < extensions.length; i++) {
                if (found = (extensions[i] == ext.toLowerCase())) 
                    break;
            }
            if (!found){
                window.alert('®bad_extension®');
                return false;
            }                 
        } 
        return true;
    }
            
            
    function submit_upload_bookmarks(){
        if (ie_browser){
            document.forms["upload_bookmarks"].submit();
            $('#upload_target').load(function() {
                document.getElementById('popup_import_bookmarks').innerHTML=$("#upload_target").contents().find("body").html();
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
                if ($_SESSION['browser_version'] >= 22
                        && ($_SESSION['user_os'] == "Windows" || $_SESSION['user_os'] == "Android"))
                    $warning = false;
                break;
        }
        if ($warning) {
            ?>
            <div id="warning">
                <div>
                    <a href="#" onclick="document.getElementById('warning').style.display='none'; ">&#215;</a> 
                    ®Warning_browser® :
                    <ul>
                        <li><b>Safari 5+</b> | </li>
                        <li><b>Google Chrome</b> | </li>
                        <?php if ($_SESSION['user_os'] == "Windows") {?>
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
            <!-- FOOTER - INFOS COPYRIGHT -->
            <?php include_once template_getpath('div_main_footer.php'); ?>
            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
        </div><!-- Container fin -->
        
        
    </body>
</html>
