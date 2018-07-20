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
This popup appears when the user clicks on "ezrecorder".
It allows to select all albums needed to be shown in ezrecorder in classrooms and set the pw for ezrecorder
-->
<div id="popup_ezrecorder">
<div  class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®recordingsetting®</h4>
    ®SelEzrecorderCoursesDesc®
</div>
<div class="modal-body">
    <h3 class='text-center'>®SelEzrecorderCourses®</h3><br>

        <form class="form-horizontal" id="form_ezrecorder" onSubmit="submitForm();return false;">
            <div class="form-group">
               
        <div class="row">
            <br />
            <div class="col-md-12">
                <table class="table table-hover text-left" >
                    <?php foreach ($album_view as $album_item) {
        // sortir des template...
        // Note: upon clicking this link, the JS function defined in show_details_functions.js will call the web_index
        // with an action "create_album". Once the processing is over, this div will be updated with the confirmation message.
 ?>
        <tr>
        <td class="album_name col-md-2" style="font-weight: bold;">
          <?php echo $album_item['course_code_public']; ?>
        </td>
        <td class="album_name col-md-2" style="font-weight: bold;">
        <div class="col-sm-offset-2 col-sm-10">
                <label>
                    <input type="checkbox" id="recorder_access" name="recorder_access[<?php echo $album_item['course_code'] ?>]"  
                        <?php  if ($album_item['in_recorders']) echo 'checked'; ?> >
                    <!--<a class="info">
                        øezrecorderø
                        <span style="font-weight: normal; font-size: 10px;">
                            ®warningrecorder_control®
                        </span>
                    </a>
                    -->
                </label>
            </div>
        </td>
       
        </tr>
      <?php } ?>
   
                </table>
            </div>
        </div> 
            </div>
            <h3 class='text-center'>®SelEzrecorderPw®</h3><br>
            <?php if(!$user_has_ezrecorder_pw){ ?> 
            <div class="form-group" id="course_code_line">
              Definissez un mot de passe pour enregistrer avec ezrecorder
            </div>    
            <?php } ?>
            <div class="form-group" id="course_code_line">
                <label for="course_code" id="labelcodeCours" class="col-sm-3 control-label">®Pw®</label>
                <div class="col-sm-9">
                    <input  maxlength="25" type="password" class="form-control" id="ezrecorder_pw" name="ezrecorder_pw" value="<?php if($user_has_ezrecorder_pw) echo "PASSWORD" ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="modal-footer">
                   <button type="submit" id="postUrl" class="btn btn-primary">®Update®</button>
                </div>
            </div>
        </form>
    
</div>
</div>
<script>		
  function submitForm() {
var form = document.getElementById('form_ezrecorder');

var dataString = $(form).serialize();
$.ajax({
type:'POST',
url:'index.php?action=update_ezrecorder',
data: dataString,
success: function(data){
    $('#popup_ezrecorder').replaceWith(data);
}
});
return false;
}   
    
</script>
