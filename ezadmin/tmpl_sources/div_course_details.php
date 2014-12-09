
<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
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

<div class="row">
    <div class="span8">
        <form class="form-horizontal" method="POST">
            <?php if($error) { ?>
                <div class="alert alert-error"><?php echo $error ?></div>
            <?php } ?>
        
            <p>®course_details_title®: <?php echo $course_code; ?></p>
            
            <input type="hidden" name="post"/>

            <!-- Course name -->
            <div class="control-group">
                <label class="control-label">®course_name®</label>
                <?php if($origin == 'external') { ?>
                <div class="controls"><?php echo $course_name; ?>
                <input type="hidden" name="course_name" value="<?php echo $course_name ?>" /></div>
                <?php } else { ?>
                <div class="controls">
                    <div class="view"><?php echo $course_name; ?></div>
                    <div class="edit"><input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name) ?>" /></div>
                </div>
                <?php } ?>
            </div>

            <!-- Shortname -->
            <div class="control-group <?php echo empty($shortname) ? 'edit' : '' ?>">
                <label class="control-label">®course_short_name®</label>
                <div class="controls">
                    <div class="view"><?php echo $shortname; ?></div>
                    <div class="edit"><input type="text" name="shortname" value="<?php echo htmlspecialchars($shortname) ?>" /></div>
                </div>
            </div>

            <!-- Origin -->
            <div class="control-group">
                <label class="control-label">®origin®</label>
                <div class="controls"><span class="label <?php if($origin == 'internal') echo 'label-info'; ?>"><?php if($origin == 'internal') echo '®intern®'; else echo '®extern®'; ?></span></div>
            </div>

            <!-- Has albums -->
            <div class="control-group">
                <label class="control-label">®has_albums_title®</label>
                <?php if($has_albums) { ?>
                <div class="controls"><i class="icon-ok"></i> ®yes®</div>
                <?php } else { ?>
                <div class="controls"><i class="icon-remove"></i> ®no®</div>
                <?php } ?>
            </div>

            <!-- In recorders -->            
            <div class="control-group">
                <label class="control-label">®recorders®</label>
                <div class="view">
                    <?php if($in_classroom) { ?>
                        <div class="controls"><i class="icon-ok"></i> ®yes®</div>
                    <?php } else { ?>
                        <div class="controls"><i class="icon-remove"></i> ®no®</div>
                    <?php } ?>
                </div>
                <div class="edit">
                    <label style="margin-left:160px"><input type="checkbox" name="in_recorders" <?php echo $in_classroom ? 'checked' :''?> /></label>
                </div>
            </div>
        </form>
    </div>
    
    <div class="span4">
        <table>
            <tr>
                <td><button class="btn edit_cancel">®cancel®</button></td>
                <td><button class="btn edit_mode">®edit_button®</button></td>
                <?php if($origin == 'internal') { ?>
                    <td>
                        <form action="index.php?action=remove_course" method="POST" style="margin:0px;">
                            <input type="hidden" name="course_code" value="<?php echo $course_code; ?>" />
                            <input type="submit" name="delete" value="®delete®" onClick="confirm('®delete_confirm®')" class="btn btn-danger delete_button" />
                        </form>
                    </td>
                <?php } ?>
            </tr>
        </table>
    </div>
</div>


<table class="table table-striped table-bordered table-hover users_table">
    <thead>
        <tr>
            <th>®user_ID®</th>
            <th>®username®</th>
            <th>®link_origin®</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($users as $u) { ?>
        <tr data-id="<?php echo $u['ID'] ?>" data-origin="<?php echo $u['origin'] ?>">
            <td><?php echo $u['user_ID']; ?></td>
            <td><?php echo $u['forename'] . ' ' . $u['surname']; ?></td>
            <td><span class="label <?php if($u['origin'] == 'internal') echo 'label-info'; ?>"><?php if($u['origin'] == 'internal') echo '®intern®'; else echo '®extern®'; ?></span></td>
            <td class="unlink" style="cursor: pointer;"><?php if($u['origin'] == 'internal') echo '<i class="icon-remove"></i> ®remove_link®'; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<div class="create_link form-inline">
    <input type="text" name="link_to" value="" class="input-medium" placeholder="®user_ID®" data-provide="typeahead" autocomplete="off" />
    <button name="link" class="btn btn-primary">®add_user_course®</button>
</div>

<script>
    
$(function() {
    $('.edit_cancel').hide();
    $('.edit').hide();
    $("button.edit_cancel").click(function() {
        $this = $(this);
        $this.hide();
        $('.edit_mode').removeClass('btn-primary');
        $('.delete_button').show();
        $(".edit").hide();
        $('.view').show();
        
    });
    
    $("button.edit_mode").click(function() {
        $this = $(this);
        $('.edit_cancel').show();
        $('.delete_button').hide();
        
        if($this.hasClass("btn-primary")) {
            $("form").first().submit();
        } else {
            $this.addClass('btn-primary');
            $(".edit").show();
            $('.view').hide();
        }
    });
   
    $(".users_table .unlink").live("click", function() {
        $this = $(this);

        if($this.parent().data('origin') == 'external') alert("®cannot_delete_external®");
        if(!confirm("®unlink_confirm®")) return false;

        var link = $this.parent().data("id");



        $.ajax("index.php?action=link_unlink_user_course&course_code=<?php echo $input['course_code'] ?>", {
            type: "post",
            data: {
                query: "unlink",
                id: link         
            },
            success: function(jqXHR, textStatus) {
                var data = JSON.parse(jqXHR);

                if(data.error) {
                    if(data.error == 1) alert("®cannot_delete_external®");
                   return;
                }

                $this.parent().hide(400, function() { $(this).remove(); });
            }
        });
    });

    $(".create_link button[name='link']").click(function() {
        $this = $(this);

        var user = $this.prev().val();
        $this.prev().val('');

        $.ajax("index.php?action=link_unlink_user_course&course_code=<?php echo $input['course_code'] ?>", {
           type: "post",
           data: {
               query: "link",
               id: user
           },
           success: function(jqXHR, textStatus) {

               var data = JSON.parse(jqXHR);

               if(data.error) {
                   if(data.error == '1') alert("®link_error®");
                   return;
               }

               var $netid = $('<td></td>').text(data.netid);
               var $username = $('<td></td>').text(data.name);
               var $delete = $('<td class="unlink" style="cursor:pointer;"><i class="icon-remove"></i>®remove_link®</td>');

               var $tr = $('<tr data-id="' + data.id + '"></tr>');
               $tr.append($netid);
               $tr.append($username);
               $tr.append($delete);

               $tr.hide();

               $('.users_table tbody').append($tr);

               $tr.show(400).css('display', 'table-row');
            }
        });
     });
 });
    
</script>