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
This popup appears when the user clicks on "create an album".
It asks for basic information about the album to create.

You should not have to use this template on its own. however, if you do, please
make sure $not_created_albums_with_descriptions is initialized and is an array containing the album names (without any 
suffix) as keys, and albums descriptions as values
for every album the user can create.
-->

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Create_album®</h4>
</div>
<div class="modal-body">
    <h3 class='text-center'>®addexistingCourse® </h3> <br>
    <?php if (empty($not_created_albums_with_descriptions)) {
    echo "<p>®All_albums_already_created®</p>";
} else {
    echo "<p>®Create_album_message®</p>"; ?>
        
        <div class="row">
            <br />
            <div class="col-md-12">
                <table class="table table-hover text-left" >
                    <?php foreach ($not_created_albums_with_descriptions as $album_name => $album_description) {
        // sortir des template...
        $cours_info=db_course_read($album_name);
        if (isset($cours_info['course_code_public']) && $cours_info['course_code_public']!='') {
            $course_code_public = $cours_info['course_code_public'];
        } else {
            $course_code_public = $album_name;
        }
                        
        // Note: upon clicking this link, the JS function defined in show_details_functions.js will call the web_index
        // with an action "create_album". Once the processing is over, this div will be updated with the confirmation message.
        echo '<tr>';
        echo '<td class="album_name col-md-2" style="font-weight: bold;">';
        echo '<a href="index.php?action=create_album&amp;course_code=' . $album_name . '" ' .
                                        'onClick=\'setTimeout(function(){ display_bootstrap_modal($("#modal"), '.
                                                '$("#create_'.$album_name.'_album"));$("#modal").modal("show"); }, 500);\' ' .
                                        'data-dismiss="modal" id="create_'.$album_name.'_album">';
        if (isset($course_code_public) && $course_code_public!="") {
            echo $course_code_public;
        } else {
            echo $album_name;
        }
        echo '</a>';
        echo '</td>';
        echo '<td class="album_description">';
        echo $album_description;
        echo '</td>';
        echo '</tr>';
    } ?>
                </table>
            </div>
        </div>
    <?php
}
    
    global $enable_course_creation;
    global $enable_channel_creation;
    global $max_course_code_size;
    global $max_album_label_size;
    
    if ($enable_course_creation) {
        ?>
    <hr />
    <h3 class='text-center'>®createnewCourse®</h3><br>

        <form class="form-horizontal" id="form_new_ablbum" action="index.php" method="post">
            <div class="form-group">
                
            <?php if ($enable_channel_creation) {
            ?>
                <label for="selectType" class="col-sm-3 control-label">®Album_type®</label>
                <div class="col-sm-9">
                    <select id="selectType" name="album_type" class="form-control">
                        <option id="opt_course" value="course">®Course®</option>
                        <option id="opt_other" value="other">®othertypealbum®</option>
                      
                    </select>
                </div>
            <?php
        } ?>
            </div>
            
            <div class="form-group" id="course_code_line">
                <label for="course_code" id="labelcodeCours" class="col-sm-3 control-label">®Course_code®</label>
                <div class="col-sm-9">
                    <input  maxlength="<?php echo $max_course_code_size ?>" type="text" class="form-control" id="course_code" name="course_code">
                </div>
            </div>
            
            <div class="form-group labelclass" id="label_course_line">
                <label for="label_course" id="labelIdCours" class="col-sm-3 control-label">®Album_name®</label>
                <div class="col-sm-9">
                    <input maxlength="<?php echo $max_album_label_size ?>" type="text" class="form-control" id="label_course" name="label">
                </div>
            </div>
            
            <div class="form-group labelclass" id="label_other_line">
                <label for="label_other" id="labelIdother" class="col-sm-3 control-label">®Album_other_name®</label>
                <div class="col-sm-9">
                    <input maxlength="<?php echo $max_album_label_size ?>" type="text" class="form-control" id="label_other" name="label">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" id="postUrl" class="btn btn-default">®Submit_create_album®</button>
                </div>
            </div>
        </form>
    <?php
    } ?>
</div>

<script>		
    $('#label_other_line').hide();
    $("#selectType").change(function(){
        var id = $(this).find("option:selected").attr("id");
        switch (id){
            case "opt_course":
                $('#course_code_line').show();
                $('#label_other_line').hide();
                $('#label_course_line').show();
                break;
                
            case "opt_other":
                $('#course_code_line').hide();
                $('#label_other_line').show();
                $('#label_course_line').hide();
                break;
        }
    });

    $('#form_new_ablbum').submit(function(e) {
        e.preventDefault();
        var label = encodeURIComponent($('#label_course').val());
        if (label == '')
            label = encodeURIComponent($('#label_other').val());
        
        var course_code = encodeURIComponent($('#course_code').val());																
        var selectType = encodeURIComponent($('#selectType').val());
        
        var error=0;
        if (label == ''){
            $(".labelclass").addClass('has-error');
            error+=1;
        }
        else 
            $(".labelclass").removeClass('has-error');
        
        if (selectType == 'course' && course_code==''){
            $("#course_code_line").addClass('has-error');
            error+=1;
        }
        else
            $("#course_code_line").removeClass('has-error');

        
        if(error===0){
            $("#modal").modal("hide"); 
            setTimeout(function() {
                display_bootstrap_modal_url($("#modal"), "index.php?action=create_courseAndAlbum&label="+label+"&albumtype="+
                    selectType+"&course_code="+course_code);
                $("#modal").modal("show"); 
            }, 500);
        }
    }); 
    
    
    
</script>
