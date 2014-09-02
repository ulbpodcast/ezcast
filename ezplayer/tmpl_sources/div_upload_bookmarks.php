<?php
/*
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
 */
?>

<h3>®Submit_bookmarks_file®</h3>
<br/>
<p>®Submit_bookmarks_file_message®</p>
<br/>
<script>
    document.getElementById('import_target').value=(current_tab == 'toc')?'official':'custom';
</script>

<a class="close-reveal-modal"href="javascript:close_popup();">&#215;</a>
<div class="form">
    <form name="upload_bookmarks" target="" action="#" method="post" id="submit_import_form" enctype="multipart/form-data" onsubmit="return false">
        <input type="hidden" name="action" value="upload_bookmarks" />
        <input type="hidden" name="MAX_FILE_SIZE" value="2147483" />
        <input type="hidden" name="album" value="<?php echo $album; ?>" />
        <input type="hidden" name="asset" value="<?php echo $asset; ?>" />
        <input type="hidden" name="target" id="import_target" value="" />

        <script>
            // Render and style the file input 
            initFileUploads()
        </script>

        <br />
        <div style="margin-left: 75px;">
            <label id="loadingfile_label"> 
                ®File®&nbsp;:
                <span class="small">®File_info®</span>
            </label>
            <!--input id="loadingfile" type="file" name="media"/> <br/><br /-->
            <div id="fileinputs_container" style="float:left;" onmouseover="$('.fileinputs span').css('background-color','#11acea');" onmouseout="$('.fileinputs span').css('background-color','#DDDDDD');">           
                <div class="fileinputs">
                    <input id="loadingfile" type="file" name="XMLbookmarks"/>
                </div>
            </div>
        </div>
        <br/><br />

        <!-- Submit button -->
        <br/>
        <a class="simple-button" style="clear:both;" href="javascript: if(check_upload_form()) submit_upload_bookmarks()">®Submit®</a>
        <a class="close-reveal-modal-button" href="javascript:close_popup();">®Cancel®</a>
    </form>
    
                    <!--[if IE]>    
                    <script>document.upload_bookmarks.target="upload_target"; ie_browser = true;</script>
                    <iframe id="upload_target" content="text/html; charset=UTF-8" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe> 
                    <![endif]-->
</div>
