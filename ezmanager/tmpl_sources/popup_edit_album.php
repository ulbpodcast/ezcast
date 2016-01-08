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
?>

<!--
Allows user to edit settings of an album
You should not have to include this file yourself.
-->
<div class="popup" id="popup_edit_album" style="width: 415px; height: 300px;">
    <h2>®Edit_album®</h2>
    <div id="form">
        <form  action="index.php" onsubmit="return false;" method="post" id="edit_form">
            <input type="hidden" name="action" value="edit_album"/>
            <input type="hidden" name="session_id" value="<?php echo session_id(); ?>" />
            <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
            <input type="hidden" id="moderation" name="moderation" value="<?php echo $moderation; ?>"/>
            <p>
                Album&nbsp;: <?php echo $album; ?> (<?php echo ($moderation) ? '®Private_album®' : '®Public_album®'; ?>)
                <br/><br/>

                <!-- Jingle dropdown list -->
                <label>®Jingle®&nbsp;:
                    <span class="small"><a class="info small">®More_info®<span>®Jingle_info®</span></a></span></label>   
                <select name="intro" id="intro" style="width: 200px;">
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

                <br/><br />

                <!-- Titling dropdown list -->
                <label>®Titling®&nbsp;:
                    <span class="small"><a class="info small">®More_info®<span>®Titling_info®</span></a></span></label>
                <select name="add_title" id="add_title" style="width: 200px;">
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
                <input type="checkbox" id="downloadable" name="downloadable" <?php if($downloadable !== 'false') echo 'checked'; ?> style="width: 13px; clear:left; margin: 0px 10px 0px 82px; padding: 0px;"/>
                <label class="labelcb" for="downloadable"><span><a class="info">®Downloadable®<span style="font-weight: normal; font-size: 10px;">®Download_info®</span></a></span></label>
            <div class="spacer"></div>

            <br/><br/>
            <div id="submitButton">
                <!-- <span class="Bouton"><span><input type="submit" value="®Submit®" /></span></span> -->
                <!--span class="Bouton"><a href="javascript:document.forms['edit_form'].submit();"><span>®Update®</span></a></span-->
                <span class="Bouton"><a href="javascript:submit_edit_form();"><span>®Update®</span></a></span>
            </div>
            <script >
                function submit_edit_form() {
                    var intro = encodeURIComponent(document.getElementById('intro').value);
                    var add_title = encodeURIComponent(document.getElementById('add_title').value);
                    var downloadable = encodeURIComponent(document.getElementById('downloadable').checked);
                    show_popup_from_outer_div('index.php?action=edit_album&session_id=<?php echo session_id(); ?>&album=<?php echo $album; ?>&moderation=<?php echo $moderation; ?>&intro=' + intro + '&add_title=' + add_title + '&downloadable=' + downloadable, true);
                }
            </script>
        </form>
    </div>
</div>