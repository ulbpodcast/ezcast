<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2014 Université libre de Bruxelles
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
?>

<div class="popup" id="submit_media" style="width: 415px;height: 575px;">
    <h2 style="display:inline;">®Submit_record®</h2>

    <div id="form">
        <form action="<?php
        echo $domain_name;
        ?>/index.php" method="post" id="submit_form" enctype="multipart/form-data" onsubmit="return false" target="uploadFrame">
            <input type="hidden" id="action" name="action" value="submit_media"/>
            <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
            <input type="hidden" id="moderation" name="moderation" value="<?php echo ($moderation) ? 'true' : 'false'; ?>"/>
            <!--input type="hidden" name="MAX_FILE_SIZE" value="1999999999" /-->

            <script>
                // Render and style the file input 
                initFileUploads()
            </script>

            <p>Album&nbsp;: <?php echo $album; ?> (<?php echo ($moderation) ? '®Private_album®' : '®Public_album®'; ?>)</p>

            <br/>

            <div id='submit_type'>
                <label>®Type®&nbsp;: 
                </label>
                <select id="type" name="type" onchange="show_file_input();">
                    <option selected="selected"  value="cam">®cam®</option>
                    <option value="slide">®slide®</option>
                    <option value="camslide">®camslide®</option>
                </select>   
            </div>
            <!-- Title field -->           
            <label>®Title®&nbsp;:
                <span class="small">®Title_info®</span>
            </label>
            <input id="title" name="title" type="text" maxlength="70"/>

            <br/><br/>

            <!-- Description field -->
            <label>®Description®&nbsp;:
                <span class="small">®Description_info®</span>
            </label>
            <textarea id="description" name="description" rows="4" ></textarea>

            <br/>

            <br /><br />      

            <div class="spacer"></div>

            <span id="more_options_button" class="BoutonMoreOptions"><a onclick="getElementById('more_options_button').className == 'BoutonMoreOptionsClic' ? getElementById('more_options_button').className = 'BoutonMoreOptions' : getElementById('more_options_button').className = 'BoutonMoreOptionsClic'" href="javascript:show_div('more_options_div')">®More_options®</a></span>
            <br />
            <div class="spacer"></div>

            <div id="more_options_div" style="display:none;">

                <!-- Jingle dropdown list -->
                <label>®Jingle®&nbsp;: 
                    <span class="small"><a class="info small">®More_info®<span>®Jingle_info®</span></a></span>
                </label>
                <select id="intro" name="intro">
                    <option value="">®None_intro®</option>
                    <?php
                    foreach ($intros as $intro) {
                        if ($intro['value'] == $album_intro) {
                            ?>                             
                            <option selected="selected" value="<?php echo $intro['value']; ?>"><?php echo $intro['label']; ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $intro['value']; ?>"><?php echo $intro['label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>   

                <br/><br/>

                <!-- Titling dropdown list -->
                <label>®Titling®&nbsp;:                      
                    <span class="small"><a class="info small">®More_info®<span>®Titling_info®</span></a></span>
                </label>
                <select id="add_title" name="add_title">
                    <option value="false">®None_titling®</option>
                    <?php
                    foreach ($titlings as $titling) {
                        if ($titling['value'] == $add_title) {
                            ?>                             
                            <option selected="selected" value="<?php echo $titling['value']; ?>"><?php echo $titling['label']; ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $titling['value']; ?>"><?php echo $titling['label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>

                <br/><br/>  
                <label>®Ratio®&nbsp;:                      
                    <span class="small"><a class="info small">®More_info®<span>®Ratio_info®</span></a></span>
                </label>    
                <div id='ratio'>    
                    <input type="radio" name="ratio" value="auto" checked> Auto
                    <input type="radio" name="ratio" value="16:9"> 16:9
                    <input type="radio" name="ratio" value="4:3" > 4:3
                </div>
                <br/><br/>

                <!-- Super highres checkbox --> 
                <input type="checkbox" id="keepQuality" name="keepQuality" onclick="visibilite('only_small_files_message');" style="width: 13px; clear:left; margin: 0px 10px 0px 120px; padding: 0px;"/>
                <label class="labelcb" for="keepQuality">&nbsp;®Keep_quality®</label>
                <div class="spacer"></div>
                <div style="display: none; color: red;" id="only_small_files_message">
                    ®Only_small_files_message®
                </div>

                <br/>

                <input type="checkbox" id="downloadable" name="downloadable" <?php echo ($downloadable !== 'false') ? 'checked' : '' ?> style="width: 13px; clear:left; margin: 0px 10px 0px 120px; padding: 0px;"/>
                <label class="labelcb" for="downloadable"><span><a class="info" style="font-size: 11px;">®Downloadable_submit®<span style="font-weight: normal; font-size: 10px;">®Download_info_submit®</span></a></span></label>

                <br/><br/>

                <div class="spacer"></div>
            </div> <!-- END more options -->



            <div id='submit_cam'>
                <label id="loadingfile_label"> 
                    ®File®&nbsp;:
                    <span id='file_info' class="small">®File_info®</span>
                </label>
                <!--input id="loadingfile" type="file" name="media"/> <br/><br /-->
                <div id="fileinputs_container" style="float:left;" onmouseover="$('.fileinputs span').css('background-color', '#99CCFF');" onmouseout="$('.fileinputs span').css('background-color', '#DDDDDD');">           
                    <div class="fileinputs">
                        <input id="loadingfile" type="file" name="media"/>
                    </div>
                </div>
            </div>

            <div id='submit_slide' style="display: none;">
                <label id="loadingfile_label2"> 
                    ®File®&nbsp;:
                    <span id='file_info2' class="small">®slide®</span>
                </label>
                <!--input id="loadingfile" type="file" name="media"/> <br/><br /-->
                <div id="fileinputs_container2" style="float:left;" onmouseover="$('.fileinputs span').css('background-color', '#99CCFF');" onmouseout="$('.fileinputs span').css('background-color', '#DDDDDD');">           
                    <div class="fileinputs">
                        <input id="loadingfile2" type="file" name="media2"/>
                    </div>
                </div>
            </div>
            <br/><br />

            <!-- Progress bar -->
            <div id="progressbar_container" style="margin-top: 20px; margin-bottom: 20px; border: 1px solid #999999; display: none; height: 4px; width: 98%; padding:2px;">
                <div id="progressbar" style="height: 4px; background-image:url(images/prog.png); "> </div>
            </div> 



            <br/><br/>
            <!-- Submit button -->
            <div id="submitButton">
                <button onclick="if (check_form())
                            sendRequest()">®Submit®</button>
            </div>
            <br />

            <!-- show more options -->   
            <!--a class="greyLink" id="more_options_a" style="border:none;" href="#" onclick="show_div('more_options_div')">®More_options®</a-->

            <div class="spacer"></div>
            <iframe id="uploadFrame" src='' name="uploadFrame" style="display:none;" onload="uploadFinished();">           
            </iframe>
        </form>
    </div>

    <script type="text/javascript">
        var is_xhr2 = supportAjaxUploadProgressEvents();

        if (is_xhr2) {
            document.getElementById('file_info').innerHTML = '®cam®';
        } else {
            document.getElementById('submit_type').innerHTML = '<input type="hidden" id="type" name="type" value="cam" />';
        }

        function show_file_input() {
            if (document.getElementById('type').value === 'camslide') {
                document.getElementById('submit_slide').style.display = 'block';
                document.getElementById('file_info').innerHTML = '®cam®';
            } else {
                document.getElementById('submit_slide').style.display = 'none';
                type = (document.getElementById('type').value == 'cam') ? '®cam®' : '®slide®';
                document.getElementById('file_info').innerHTML = type;
            }
        }


        function check_form() {

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
                var extensions = <?php
                    global $valid_extensions;
                    echo json_encode($valid_extensions);
                    ?>;

                // check if extension is accepted
                var found = false;
                for (var i = 0; i < extensions.length; i++) {
                    if (found = (extensions[i] == ext.toLowerCase()))
                        break;
                }
                if (!found) {
                    window.alert('®bad_extension®');
                    return false;
                }

                if (document.getElementById('type').value === 'camslide') {
                    var file2 = document.getElementById('loadingfile2').value;
                    if (file2 == '') {
                        window.alert('®No_file® (®slide®)');
                        return false;
                    }
                    var ext2 = file2.split('.').pop();

                    var found = false;
                    for (var i = 0; i < extensions.length; i++) {
                        if (found = (extensions[i] == ext2.toLowerCase()))
                            break;
                    }
                    if (!found) {
                        window.alert('®bad_extension® (®slide®)');
                        return false;
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
                document.getElementById('submit_media').innerHTML = '<h2>®Upload_finished_title®</h2>®Upload_finished®<br/><br/><br/><span class="Bouton"><a href="#" onclick="close_popup();"><span>®Close_and_return_to_index®</span></a></span>';
            } else {
                document.getElementById('submit_cam').style.display = 'none';
                document.getElementById('submit_slide').style.display = 'none';
                // changes the current progress rate                
                document.getElementById('progressbar_container').style.display = 'block';
                document.getElementById('progressbar').style.width = progressRate + '%';
                document.getElementById('submitButton').innerHTML = type + '®Upload_in_progress® (' + (isNaN(progressRate) ? '0' : progressRate) + '%)';
            }
        }

        // function used by browsers that don't support XHR2
        // The file is submitted in a hidden iframe and this function
        // is called each time the iframe is loaded
        // When the iframe is first loaded, its content is empty so
        // nothing happens.
        // When the iframe is loaded after a file submit, its content
        // is not empty and we can change the display
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
        function sendRequest() {

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
                if (type === 'camslide') {
                    var file2 = document.getElementById('loadingfile2');
                    fd.append("cam_filename", file.files[0].name);
                    fd.append("slide_filename", file2.files[0].name);
                } else {
                    fd.append(type + "_filename", file.files[0].name);
                }
                fd.append('album', document.getElementById('album').value);
                fd.append('moderation', document.getElementById('moderation').value);
                fd.append('type', type);
                fd.append('title', document.getElementById('title').value);
                fd.append('description', document.getElementById('description').value);
                fd.append('intro', document.getElementById('intro').value);
                fd.append('add_title', document.getElementById('add_title').value);
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

                        var worker = new Worker("js/fileupload.js");
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
                                    document.getElementById('submit_media').innerHTML = '<h2>®Upload_failed_title®</h2>®Upload_failed®<br/><br/><br/><span class="Bouton"><a href="#" onclick="close_popup();"><span>®Close_and_return_to_index®</span></a></span>';

                                    break;
                                case 'exec':
                                    eval(e.data.message);
                                    break;
                            }
                        }

                        // sends parameters and action to js/fileupload.js 
                        worker.postMessage({'fct': 'pushValue', 'args': {'key': 'id', 'value': id}});
                        worker.postMessage({'fct': 'pushValue', 'args': {'key': 'url', 'value': '<?php echo $domain_name; ?>'}});
                        worker.postMessage({'fct': 'pushValue', 'args': {'key': 'chunkSize', 'value': chunkSize}});
                        if (type === 'camslide') {
                            worker.postMessage({'fct': 'process', 'args': {'blob': file.files[0], 'type': 'cam'}});
                            worker.postMessage({'fct': 'process', 'args': {'blob': file2.files[0], 'type': 'slide'}});
                        } else {
                            worker.postMessage({'fct': 'process', 'args': {'blob': file.files[0], 'type': type}});
                        }

                    }
                }, false);
                // init the upload by sending the metadata over the file/s
                xhr.open("POST", "index.php?action=upload_init", true);
                xhr.send(fd);
            } else {
                // Browser doesn't support XHR2 so we use a hidden iframe to upload the file
                document.getElementById('submit_cam').style.display = 'none';
                document.getElementById('submitButton').innerHTML = '®Upload_pending®<br/><br/><img src="images/loading_white.gif"/>';
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
        ;
    </script>

</div>
