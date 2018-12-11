<?php
/*
 * EZCAST EZmanager
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 		    Arnaud Wijns <awijns@ulb.ac.be>
 *                   Antoine Dewilde
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

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    global $ezmanager_safe_url;
    $domain_name = $ezmanager_safe_url;
} else {
    global $ezmanager_url;
    $domain_name = $ezmanager_url;
}

$album_path = $repository_path . "/" . $album."-pub";
$album_metadata = metadata2assoc_array($album_path . "/_metadata.xml");
if (isset($album_metadata['course_code_public']) && $album_metadata['course_code_public']!='') {
    $course_code_public = $album_metadata['course_code_public'];
} else {
    $course_code_public = $album;
}
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="force_close=true;">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title"><?php echo ($enable_audio_submit) ? '®Submit_record_media®' : '®Submit_record®'; ?></h4>
</div>
<form action="<?php echo $domain_name; ?>/index.php" method="post" id="submit_form" enctype="multipart/form-data" 
      onsubmit="return false" target="uploadFrame">
    <div class="modal-body form-horizontal">
        <input type="hidden" id="action" name="action" value="submit_media"/>
        <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
        <input type="hidden" id="moderation" name="moderation" value="<?php echo ($moderation) ? 'true' : 'false'; ?>"/>
        <script> // Render and style the file input 
            initFileUploads()
        </script>
        
        <div class="form-group">
            <label class="col-sm-3 control-label">®Album®</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?php echo $course_code_public; ?> 
                    (<?php echo ($moderation) ? '®Private_album®' : '®Public_album®'; ?>)
                </p>
            </div>
        </div>

        <?php if($enable_audio_submit) { ?>
        <div class="form-group">
            <label for="type" class="col-sm-3 control-label">®Type®</label>
            <div class="btn-group col-sm-9" data-toggle="buttons">
                <label class="btn btn-success active">
                    <input type="radio" name="type_media" id="type_media" onchange="change_radio_button();" value="video" autocomplete="off" checked="checked"> ®Video®
                    <span class="glyphicon glyphicon-facetime-video"></span>
                </label>
                <label class="btn btn-success">
                    <input type="radio" name="type_media" id="type_media" onchange="change_radio_button();" value="audio" autocomplete="off"> ®Audio®
                    <span class="glyphicon glyphicon-music"></span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <div>
                    Le type sélectionné : <b id="type_selected"></b>
                </div>
            </div>
        </div>
        <?php } ?>

        <!--<div class="form-group">
            <label for="type" class="col-sm-3 control-label">®Type®</label>
            <div class="col-sm-9">
                <label class="radio-inline">
                    <input type="radio" name="type_media" id="type_media" onchange="change_radio_button();" value="video" checked="checked"> Vidéo
                    <span class="glyphicon glyphicon-facetime-video"></span>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="type_media" id="type_media" onchange="change_radio_button();" value="audio"> Audio
                    <span class="glyphicon glyphicon-music"></span>
                </label>
            </div>
        </div>-->
        
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">
                ®Title®
                <p class="help-block">®Title_info®</p>
            </label>
            <div class="col-sm-9">
                <input id="title" name="title" class="form-control" type="text" maxlength="70"/>
            </div>
        </div>
        
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">
                ®Description®
                <p class="help-block">®Description_info®</p>
            </label>
            <div class="col-sm-9">
                <textarea id="description" class="form-control" name="description" rows="4" style="resize: vertical;"></textarea>
            </div>
        </div>
        
        <div class="form-group" id='options' style="display: none;">
            <div class="col-sm-offset-3 col-sm-9">
                <button class="btn btn-default" type="button" data-toggle="collapse" 
                        data-target="#more_options_div" aria-expanded="false" aria-controls="more_options_div">
                    ®More_options®
                </button>
            </div>
        </div>
        
        <div class="collapse" id="more_options_div">
            <div class="form-group">
                <label for="type" id="submit_type" class="col-sm-3 control-label">®Type®</label>
                <div class="col-sm-9">
                    <select class="form-control" name="type" id="type" onchange="show_file_input();">
                        <option selected="selected" value="cam">®cam®</option>
                        <option value="slide">®slide®</option>
                        <option value="camslide">®camslide®</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-3 control-label">
                    ®Jingle®
                    <p class="help-block">
                        <a class="info small">®More_info®<span>®Jingle_info®</span></a>
                    </p>
                </label>
                <div class="col-sm-9">
                    <select class="form-control" name="intro" id="intro">
                        <option value="">®None_intro®</option>
                        <?php foreach ($intros as $intro) {
    echo '<option ';
    if ($intro['value'] == $album_intro) {
        echo 'selected="selected" ';
    }
    echo 'value="'.$intro['value'].'">'.$intro['label'].'</option>';
} ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="title" class="col-sm-3 control-label">
                    ®Titling®
                    <p class="help-block">
                        <a class="info small">®More_info®<span>®Titling_info®</span></a>
                    </p>
                </label>
                <div class="col-sm-9">
                    <select class="form-control" name="add_title" id="add_title">
                        <option value="false">®None_titling®</option>
                        <?php foreach ($titlings as $titling) {
    echo '<option ';
    if ($titling['value'] == $add_title) {
        echo 'selected="selected" ';
    }
    echo 'value="'.$titling['value'].'">'.$titling['label'].'</option>';
} ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="title" class="col-sm-3 control-label">
                    ®Credits®
                    <p class="help-block">
                        <a class="info small">®More_info®<span>®Credits_info®</span></a>
                    </p>
                </label>
                <div class="col-sm-9">
                    <select class="form-control" name="credits" id="credits">
                        <option value="false">®None_credits®</option>
                        <?php foreach ($credits as $credit) {
    echo '<option ';
    if ($credit['value'] == $album_credits) {
        echo 'selected="selected" ';
    }
    echo 'value="'.$credit['value'].'">'.$credit['label'].'</option>';
} ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="ratio" class="col-sm-3 control-label">
                    ®Ratio®
                    <p class="help-block">
                        <a class="info small">®More_info®<span>®Ratio_info®</span></a>
                    </p>
                </label>
                <div class="col-sm-3">    
                    <label>
                        <input type="radio" name="ratio" value="auto" checked> Auto
                    </label>
                </div>
                <div class="col-sm-3">
                    <label>
                        <input type="radio" name="ratio" value="16:9"> 16:9
                    </label>
                </div>
                <div class="col-sm-3">
                    <label>
                        <input type="radio" name="ratio" value="4:3" > 4:3
                    </label>
                </div>
            </div>
        
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="keepQuality" name="keepQuality" 
                                   onclick="visibilite('only_small_files_message');"> 
                            ®Keep_quality®
                        </label>
                    </div>
                    <div style="display: none; color: red;" id="only_small_files_message">
                        ®Only_small_files_message®
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="downloadable" name="downloadable" 
                                <?php echo ($downloadable !== 'false') ? 'checked' : '' ?>> 
                            <a class="info">®Downloadable_submit®<span>®Download_info_submit®</span></a>
                        </label>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="form-group" id="submit_cam">
            <label for="loadingfile_label" class="col-sm-3 control-label">
                <span id="file_info">®File®</span>
                <p class="help-block">®File_info®</p>
            </label>
            <div class="col-sm-9">
                <input id="loadingfile" type="file" name="media"/>
            </div>
        </div>
        
        <div class="form-group" id='submit_slide' style="display: none;">
            <label for="loadingfile_label" class="col-sm-3 control-label">
                <span id="file_info2">®slide®</span>
                <p class="help-block">®File_info®</p>
            </label>
            <div class="col-sm-9">
                <input id="loadingfile2" type="file" name="media2"/>
            </div>
        </div>
        
        <div class="progress" id="progressbar_container" style="display: none">
            <div class="progress-bar" id="progressbar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" 
                 style="width: 0%;">
            </div>
        </div>
        
        <iframe id="uploadFrame" src='' name="uploadFrame" style="display:none;"></iframe>
        
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-primary" id="submitButton" onclick="if (check_form()) sendRequest()">®Submit®</button>
    </div>
</form>

<script type="text/javascript">
    var force_close = true; // variable to allow or not to close the popup
    
    var is_xhr2 = supportAjaxUploadProgressEvents();

    if (is_xhr2) {
        document.getElementById('file_info').innerHTML = '®cam®';
    } else {
        document.getElementById('submit_type').innerHTML = '<input type="hidden" id="type" name="type" value="cam" />';
    }

    $( document ).ready(function()
    {
        change_radio_button();

        var type_media = $("input:radio[name=type_media]:checked").val();

        $("#type_selected").text(type_media);

    });

    function show_file_input()
    {
        var type_media = $("input:radio[name=type_media]:checked").val();

        if (typeof type_media === "undefined")
        {
            type_media = 'video';
        }

        if (document.getElementById('type').value === 'camslide')
        {
            document.getElementById('submit_slide').style.display = 'block';
            document.getElementById('file_info').innerHTML = '®cam®';
        } 
        else
        {
            document.getElementById('submit_slide').style.display = 'none';
            if(type_media == 'video')
            {
                type = (document.getElementById('type').value == 'cam') ? '®cam®' : '®slide®';
            }
            else if(type_media == 'audio')
            {
                type = '®file_audio®';
            }
            document.getElementById('file_info').innerHTML = type;
        }
    }

    function change_radio_button()
    {
        var type_media = $("input:radio[name=type_media]:checked").val();

        if (typeof type_media === "undefined")
        {
            type_media = 'video';
        }

        if(type_media == 'video')
        {
            $('#options').css('display', 'block');
            $('#more_options_div').css('display', '');
            $("#type_selected").text(type_media);
            change_color_options();
        }
        else
        {
            $('#options').css('display', 'none');
            $('#more_options_div').css('display', 'none');
            $('#type option').removeAttr('selected');
            $('#type').find('option:eq(0)').prop('selected', true);
            $("#type_selected").text(type_media);
            change_color_options();
        }

        show_file_input();
    }

    function change_color_options()
    {
        $("input[name=type_media]:not(:checked)").parent()
        .css({
            'background-color' : '#D3D3D3',
            'border-color' : '#808080'
        });
        $("input[name=type_media]:checked").parent()
        .css({
            'background-color' : '#5cb85c',
            'border-color' : '#4cae4c'
        });
    }

    function check_form()
    {
        var extensions_video = <?php echo json_encode($valid_extensions_video); ?> ;
        var extensions_audio = <?php echo json_encode($valid_extensions_audio); ?> ;
        var enable_verify_mimeType_extension = <?php echo $enable_verify_mimeType_extension; ?>;

        var type_media = $("input:radio[name=type_media]:checked").val();

        if (typeof type_media === "undefined")
        {
            type_media = 'video';
        }

        if (document.getElementById('title').value == '') {
            window.alert('®No_title®');
            return false;
        }

        var file = document.getElementById('loadingfile').value;
        if (file == '') {
            window.alert('®No_file®');
            return false;
        } else {

            //        Regex doesn't work in IE
            //        var ext = file.match(/^.+\.([^.]+)$/)[1];
            var ext = file.split('.').pop();

            // check if extension is accepted
            if (enable_verify_mimeType_extension == true) {
                var found = false;
                if(type_media == 'video'){
                    //check if extension is accepted
                    for (var i = 0; i < extensions_video.length; i++) {
                        if (found = (extensions_video[i] == ext.toLowerCase()))
                            break;
                    }
                } else if(type_media == 'audio') {
                    //check if extension is accepted
                    for (var i = 0; i < extensions_audio.length; i++) {
                        if (found = (extensions_audio[i] == ext.toLowerCase()))
                            break;
                    }
                }

                if (!found) {
                    window.alert('®bad_extension®');
                    return false;
                }
            }


            if (document.getElementById('type').value === 'camslide') {
                var file2 = document.getElementById('loadingfile2').value;
                if (file2 == '') {
                    window.alert('®No_file® (®slide®)');
                    return false;
                }
                var ext2 = file2.split('.').pop();

                if (enable_verify_mimeType_extension == true) {
                    var found = false;
                    if(type_media == 'video'){
                        //check if extension is accepted
                        for (var i = 0; i < extensions_video.length; i++) {
                            if (found = (extensions_video[i] == ext2.toLowerCase()))
                                break;
                        }
                    }

                    if (!found) {
                        window.alert('®bad_extension® (®slide®)');
                        return false;
                    }
                }
            }
        }
        return true;
    }

    // function that updates the progress bar (called by the web worker)
    function updateProgress(progressRate, type) {
        // upload is finished 
        if (document.getElementById('type').value === 'camslide') {
            type = (type == 'cam') ? '[®cam®] ' : '[®slide®] ';
        } else {
            type = '';
        }

        if (progressRate >= 100) {
            var ablum_refresh = "<?php echo $album; ?>";
            var visible = "<?php echo $visibility; ?>";
            ablum_refresh = ablum_refresh+''+visible;
            $('.modal-title').text("®Upload_finished_title®");
            $('.modal-body').text("®Upload_finished®");
            $('.modal-footer').html('<button type="button" id="close_btn" class="btn btn-primary" ' + 
                    'data-dismiss="modal">®Close_and_return_to_index®</button>');
            $('#close_btn').attr('onclick', 'show_album_details("'+ablum_refresh+'")');
            force_close = true;
        } else {
            document.getElementById('submit_cam').style.display = 'none';
            document.getElementById('submit_slide').style.display = 'none';
            // changes the current progress rate                
            document.getElementById('progressbar_container').style.display = 'block';
            document.getElementById('progressbar').style.width = progressRate + '%';
            $('#submitButton').prop('disabled', true);
            document.getElementById('submitButton').innerHTML = type + '®Upload_in_progress® (' + 
                    (isNaN(progressRate) ? '0' : progressRate) + '%)';
        }
    }

    // function used by browsers that don't support XHR2
    // The file is submitted in a hidden iframe and this function
    // is called each time the iframe is loaded
    // When the iframe is first loaded, its content is empty so
    // nothing happens.
    // When the iframe is loaded after a file submit, its content
    // is not empty and we can change the display
    document.getElementById('uploadFrame').addEventListener('load', uploadFinished());
    
    function uploadFinished() {
        var frame = document.getElementById('uploadFrame');
        if (frame) {
            ret = frame.contentWindow.document.body.innerHTML;
            console.log(ret);
            // on first call, the iframe has no content
            // When the file has been loaded, the iframe has a content
            if (ret.length) {
                updateProgress(100, '');
            }
        }
    }

    // submit the form 
    var worker;
    function sendRequest() {
        force_close = false;
        var file = document.getElementById('loadingfile');
        var id; // id is set after the form has been submitted to the server
        var chunkSize;
        xhr = new XMLHttpRequest();

        if (is_xhr2) {
            // browser supports XHR2 so we can send big chunked files

            // prepares formData that will be sent to the server
            // the file(s) will be added in second step
            fd = new FormData();
            var type = document.getElementById('type').value;
            var type_media = $("input:radio[name=type_media]:checked").val();

            if (typeof type_media === "undefined")
            {
                type_media = 'video';
            }

            if (type === 'camslide') {
                var file2 = document.getElementById('loadingfile2');
                fd.append("cam_filename", file.files[0].name);
                fd.append("slide_filename", file2.files[0].name);
            } else {
                console.log('passe ici => '+type);
                console.log(file.files[0].name);
                fd.append(type + "_filename", file.files[0].name);
            }
            fd.append('album', document.getElementById('album').value);
            fd.append('moderation', document.getElementById('moderation').value);
            fd.append('type', type);
            fd.append('type_media', type_media);
            fd.append('title', document.getElementById('title').value);
            fd.append('description', document.getElementById('description').value);
            fd.append('intro', document.getElementById('intro').value);
            fd.append('add_title', document.getElementById('add_title').value);
            fd.append('credits', document.getElementById('credits').value);
            fd.append('keepQuality', (document.getElementById('keepQuality').checked) ? document.getElementById('keepQuality').value : '');
            fd.append('downloadable', (document.getElementById('downloadable').checked) ? true : false);
            fd.append('ratio', ($('input[name="ratio"]:checked').val()));

            // called when the form has been submitted to the server
            xhr.addEventListener("load", function (evt) {
                // server returns a json array
                response = eval("(" + xhr.responseText + ")");
                // if response.error is set, an error occured server side
                if (response.error)
                    window.alert(response.error);
                else {
                    // sends the file/s via a web worker
                    console.log(response.values);
                    id = response.values.id;
                    chunkSize = response.values.chunk_size;

                    worker = new Worker("js/fileupload.js");
                    console.log(worker);

                    // determines the action to do when the worker sends 
                    // a message from js/fileupload.js
                    worker.onmessage = function (e) {

                        switch (e.data.action) {
                            case 'console': // defined in js/fileupload.js
                                console.log(e.data.message);
                                break;
                                
                            case 'error':
                                window.alert(e.data.message);
                                // ends the upload process
                                this.terminate();
                                // moves the current upload in failed upload
                                xhr2 = new XMLHttpRequest();

                                // prepares formData that will be sent to the server
                                fd2 = new FormData();
                                fd2.append("id", id);
                                xhr2.open("POST", "index.php?action=upload_error", true);
                                xhr2.send(fd2);
                                
                                var ablum_refresh = "<?php echo $album; ?>";
                                var visible = "<?php echo $visibility; ?>";
                                ablum_refresh = ablum_refresh+''+visible;

                                $('.modal-title').text("®Upload_failed_title®");
                                $('.modal-body').text("®Upload_failed®");
                                $('.modal-footer').html('<button type="button" id="close_btn_error" class="btn btn-primary" ' + 
                                        'data-dismiss="modal">®Close_and_return_to_index®</button>');
                                $('#close_btn_error').attr('onclick', 'show_album_details("'+ablum_refresh+'")');
                                
                                force_close = true;                                
                                break;
                                
                            case 'exec':
                                eval(e.data.message);
                                break;
                        }
                    };

                    // sends parameters and action to js/fileupload.js 
                    worker.postMessage({'fct': 'pushValue', 'args': {'key': 'id', 'value': id}});
                    worker.postMessage({'fct': 'pushValue', 'args': {'key': 'url', 'value': '<?php echo $domain_name; ?>'}});
                    worker.postMessage({'fct': 'pushValue', 'args': {'key': 'chunkSize', 'value': chunkSize}});
                    if(type_media === 'video')
                    {
                        if (type === 'camslide')
                        {
                            worker.postMessage({'fct': 'process', 'args': {'blob': file.files[0], 'type': 'cam'}});
                            worker.postMessage({'fct': 'process', 'args': {'blob': file2.files[0], 'type': 'slide'}});
                        }
                        else 
                        {
                            worker.postMessage({'fct': 'process', 'args': {'blob': file.files[0], 'type': type}});
                        }
                    }
                    else if(type_media === 'audio')
                    {
                        worker.postMessage({'fct': 'process', 'args': {'blob': file.files[0], 'type': type, 'type_media': type_media}});
                    }

                }
            }, false);
            // init the upload by sending the metadata over the file/s
            xhr.open("POST", "index.php?action=upload_init", true);
            xhr.send(fd);
        } else {
            // Browser doesn't support XHR2 so we use a hidden iframe to upload the file
            document.getElementById('submit_cam').style.display = 'none';
            document.getElementById('submitButton').innerHTML = '<img src="images/loading_transparent.gif"/> ®Upload_pending®';
            document.getElementById('submit_form').submit();
        }
    }

    // checks if the browser supports XHR2 and web worker 
    // If not, we cannot upload files more than 2Go and we have no progress bar
    function supportAjaxUploadProgressEvents() {
        var xhr = new XMLHttpRequest();
        var blob;
        try {
            blob = new Blob(["blob"]);
        } catch (e) {
            return false;
        }
        return !!(xhr && ('upload' in xhr) && ('onprogress' in xhr.upload))
                && (typeof (Worker) !== "undefined")
                && (typeof (blob.slice) === 'function' || typeof (blob.mozSlice) === 'function');
    }
    
    // When modal will close
    $('#modal').on('hide.bs.modal', function (event) {
        if(!force_close) { // Cancel if not forced
            event.preventDefault();
            event.stopImmediatePropagation();
            return false; 
        } else if(worker != null && worker != 'undefined') {
            worker.terminate();
        }
    });
</script>

