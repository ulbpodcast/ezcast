<!-- 
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
-->

<div class="popup" id="submit_media" style="width: 415px;height: 515px;">
    <h2 style="display:inline;">®Submit_record®</h2>
        
    <div id="form">
        <form action="#" target="uploadFrame" method="post" id="submit_form" enctype="multipart/form-data" onsubmit="return false">
           <input type="hidden" name="action" value="submit_media"/>
           <input type="hidden" name="session_id" value="<?php echo session_id();?>" />
           <input type="hidden" id="keyFile" name="<?php echo ini_get('apc.rfc1867_name'); ?>" value="<?php echo uniqid(); ?>" />
           <input type="hidden" name="tmpName" value="<? echo $folder_path_repository ?>" />
           <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
           <input type="hidden" id="moderation" name="moderation" value="<?php echo ($moderation) ? 'true' : 'false'; ?>"/>
           <input type="hidden" name="MAX_FILE_SIZE" value="2147483648" />
           <input type="hidden" name="type" id="type" value="cam" />
          
           <script>
               // Render and style the file input 
               initFileUploads()
           </script>
           
            <p>Album&nbsp;: <?php echo $album; ?> (<?php echo ($moderation) ? '®Private_album®' : '®Public_album®'; ?>)</p>
           
            <br/>
            
            <!-- Title field -->           
            <label>®Title®&nbsp;:
                <span class="small">®Title_info®</span>
            </label>
            <input name="title" id="title" type="text" maxlength="70"/>
            
            <br/><br/>
            
            <!-- Description field -->
            <label>®Description®&nbsp;:
                <span class="small">®Description_info®</span>
            </label>
            <textarea name="description" id="description" rows="4" ></textarea>
            
            <br/>
            
            <br /><br />      
                          
            <div class="spacer"></div>
            
            <span id="more_options_button" class="BoutonMoreOptions"><a onclick="getElementById('more_options_button').className=='BoutonMoreOptionsClic'?getElementById('more_options_button').className='BoutonMoreOptions':getElementById('more_options_button').className='BoutonMoreOptionsClic'" href="javascript:show_div('more_options_div')">®More_options®</a></span>
            <br />
            <div class="spacer"></div>
             
            <div id="more_options_div" style="display:none;">
                
                <!-- Jingle dropdown list -->
                <label>®Jingle®&nbsp;: 
                    <span class="small"><a class="info small">®More_info®<span>®Jingle_info®</span></a></span>
                </label>
                 <select name="intro" id="intro">
                     <option value="">®None_intro®</option>
                     <?php foreach ($intros as $intro){ 
                         if ($intro['value'] == $album_intro) {?>                             
                        <option selected="selected" value="<?php echo $intro['value']; ?>"><?php echo $intro['label']; ?></option>
                     <?php } else { ?>
                        <option value="<?php echo $intro['value']; ?>"><?php echo $intro['label']; ?></option>
                     <?php } 
                     } ?>
                 </select>   
                                
                <br/><br/>
                
                <!-- Titling dropdown list -->
                <label>®Titling®&nbsp;:                      
                    <span class="small"><a class="info small">®More_info®<span>®Titling_info®</span></a></span>
                </label>
                 <select name="add_title" id="add_title">
                     <option value="false">®None_titling®</option>
                     <?php foreach ($titlings as $titling){ 
                         if ($titling['value'] == $add_title) {?>                             
                        <option selected="selected" value="<?php echo $titling['value']; ?>"><?php echo $titling['label']; ?></option>
                     <?php } else { ?>
                        <option value="<?php echo $titling['value']; ?>"><?php echo $titling['label']; ?></option>
                     <?php } 
                     } ?>
                 </select>
                
                <br/><br/>
                
                <!-- Super highres checkbox --> 
                <input type="checkbox" id="keepQuality" name="keepQuality" onclick="visibilite('only_small_files_message');" style="width: 13px; clear:left; margin: 0px 10px 0px 82px; padding: 0px;"/>
                <span class="labelcb">&nbsp;®Keep_quality®</span>
                <div class="spacer"></div>
                <div style="display: none; color: red;" id="only_small_files_message">
                    ®Only_small_files_message®
                </div>
                <br/>
             
           </div> <!-- END more options -->
           
           
            
           <!-- Progress bar -->
           <label id="loadingfile_label"> 
               ®File®&nbsp;:
               <span class="small">®File_info®</span>
           </label>
            <!--input id="loadingfile" type="file" name="media"/> <br/><br /-->
            <div id="fileinputs_container" style="float:left;" onmouseover="$('.fileinputs span').css('background-color','#99CCFF');" onmouseout="$('.fileinputs span').css('background-color','#DDDDDD');">           
                <div class="fileinputs">
                    <input id="loadingfile" type="file" name="media"/>
                </div>
            </div>
            <br/><br />

            <div id="progressbar_container" style="margin-top: 20px; margin-bottom: 20px; border: 1px solid #999999; display: none; height: 4px; width: 98%; padding:2px;">
            <div id="progressbar" style="height: 4px; background-image:url(images/prog.png); "> </div>
            </div> 
            
           
           
           <br/><br/>
           <!-- Submit button -->
           <div id="submitButton">
                 <!-- <span class="Bouton"><span><input type="submit" value="®Submit®" /></span></span> -->
                 <!--span class="Bouton"><a href="javascript: if(document.getElementById('title').value == '') window.alert('®No_title®'); else submit_upload_form();"><span>®Submit®</span></a></span-->
                 <span class="Bouton"><a href="javascript: if(check_form()) submit_upload_form();"><span>®Submit®</span></a></span>
           </div>
           <br />
           
            <!-- show more options -->   
            <!--a class="greyLink" id="more_options_a" style="border:none;" href="#" onclick="show_div('more_options_div')">®More_options®</a-->
           
           <div class="spacer"></div>
           <iframe id="uploadFrame" name="uploadFrame" src="#" style="display:none"></iframe>
       </form>
    </div>
</div>
