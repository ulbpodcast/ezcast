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
?>

<!--
Allows user to edit settings of an album
You should not have to include this file yourself.
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Edit_album®</h4>
</div>
<form  action="index.php" onsubmit="return false;" method="post">
    <div class="modal-body form-horizontal">
        <input type="hidden" name="action" value="edit_album"/>
        <input type="hidden" name="session_id" value="<?php echo session_id(); ?>" />
        <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
        <input type="hidden" id="moderation" name="moderation" value="<?php echo $moderation; ?>"/>
        
        <div class="form-group">
            <label class="col-sm-2 control-label">®Album®</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    <?php if (isset($album_meta['course_code_public']) && $album_meta['course_code_public']!="") {
    echo $album_meta['course_code_public'];
} else {
    echo $album;
}
                    echo '(';
                    echo(($moderation) ? '®Private_album®' : '®Public_album®');
                    ?>)
                </p>
            </div>
        </div>
        
        <div class="form-group">
            <label for="jingle" class="col-sm-2 control-label">®Jingle®</label>
            <div class="col-sm-10">
                <select class="form-control" name="intro" id="intro">
                    <option value="">®None_intro®</option>
                    <?php
                    foreach ($intros as $intro) {
                        echo '<option ';
                        if ($intro['value'] == $album_intro) {
                            echo 'selected="selected" ';
                        }
                        echo 'value="'. $intro['value'] . '">' . $intro['label']. '</option>';
                    }
                    ?>
                </select>
                <p class="help-block"><a class="info small">®More_info®<span>®Jingle_info®</span></a></p>
            </div>
        </div>
        
        <div class="form-group">
            <label for="add_title" class="col-sm-2 control-label">®Titling®</label>
            <div class="col-sm-10">
                <select class="form-control" name="add_title" id="add_title">
                    <option value="false">®None_titling®</option>
                    <?php
                    foreach ($titlings as $titling) {
                        echo '<option ';
                        if ($titling['value'] == $add_title) {
                            echo 'selected="selected" ';
                        }
                        echo 'value="'. $titling['value'] . '">' . $titling['label']. '</option>';
                    }
                    ?>
                </select>
                <p class="help-block"><a class="info small">®More_info®<span>®Titling_info®</span></a></p>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="downloadable" name="downloadable"
                            <?php if ($downloadable !== 'false') {
                        echo 'checked';
                    } ?>> 
                        <a class="info">
                            ®Downloadable®
                            <span style="font-weight: normal; font-size: 10px;">
                                ®Download_info®
                            </span>
                        </a>
                    </label>
                </div>
            </div>
        </div>
        
        <?php
        global $enable_anon_access_control;
        if ($enable_anon_access_control === true) {
            ?>
        
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <label>
                    <input type="checkbox" id="anon_access" name="anon_access" 
                        <?php if ($anon_access !== 'false') {
                echo 'checked';
            } ?>>
                    <a class="info">
                        ®Anonym_Access®
                        <span style="font-weight: normal; font-size: 10px;">
                            ®warningAnon®
                        </span>
                    </a>
                </label>
            </div>
        </div>
        <?php
        }
        ?>
        
        <?php
        global $enable_recorder_control;
        if ($enable_recorder_control === true) {
            ?>
        
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <label>
                    <input type="checkbox" id="recorder_access" name="recorder_access" 
                        <?php if ($recorder_access == 1) {
                echo 'checked';
            } ?>>
                    <a class="info">
                        ®recorder_control®
                        <span style="font-weight: normal; font-size: 10px;">
                            ®warningrecorder_control®
                        </span>
                    </a>
                </label>
            </div>
        </div>
        <?php
        }
        ?>
        
        <div class="modal-footer">
            <a role="button" href="javascript:submit_edit_form();" class="btn btn-primary">®Update®</a>
        </div>
        <script>
            function submit_edit_form() {
                var intro = encodeURIComponent(document.getElementById('intro').value);
                var add_title = encodeURIComponent(document.getElementById('add_title').value);
                var downloadable = encodeURIComponent(document.getElementById('downloadable').checked);
                var anon_access = encodeURIComponent(document.getElementById('anon_access').checked);
                var recorder_access = encodeURIComponent(document.getElementById('recorder_access').checked);

                $('#modal').modal('hide');
                setTimeout(function(){ 
                    display_bootstrap_modal_url($('#modal'), 'index.php?action=edit_album&session_id=<?php echo session_id(); ?>&album=<?php 
                    echo $album; ?>&moderation=<?php echo $moderation; ?>&intro=' + intro + '&add_title=' + 
                    add_title + '&downloadable=' + downloadable + '&anon_access=' + anon_access+ '&recorder_access=' + recorder_access);
                    $('#modal').modal('show'); 
                }, 500);
            }
        </script>
    </div>
</form>
