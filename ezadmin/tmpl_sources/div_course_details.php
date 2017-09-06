
<div class="page_title">®course_details_title®: <?php if (isset($course_code_public) && $course_code_public!="") {
    echo $course_code_public;
} else {
    echo $course_code;
} ?></div>

<div class="col-md-8">
    <form class="form-horizontal" method="POST">
        
        <?php if (isset($error)) {
    ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
            <?php echo $error; ?>
        </div>
        <?php
} ?>

        <input type="hidden" name="post"/>

        <!-- Course name -->
        <div class="form-group">
            <label class="col-md-3 control-label">®course_name®</label>
            <div class="col-sm-5">
            <?php if ($origin == 'external') {
        ?>
                <p class="view form-control-static">
                    <?php echo $course_name; ?>
                </p>
                <input type="hidden" class="form-control" name="course_name" 
                       value="<?php echo $course_name ?>" />
            <?php
    } else {
        ?>
                <p class="view form-control-static"><?php echo $course_name; ?></p>
                <div class="edit">
                    <input type="text" class="form-control" name="course_name" 
                           value="<?php echo htmlspecialchars($course_name) ?>" />
                </div>
            <?php
    } ?>
            </div>
        </div>

        <!-- Origin -->
        <div class="form-group">
            <label class="col-md-3 control-label">®origin®</label>
            <div class="col-sm-5">
                <span class="label 
                    <?php if ($origin == 'internal') {
        echo 'label-info';
    } elseif ($origin == 'external') {
        echo 'label-primary';
    } else {
        echo 'label-danger';
    } ?>
                    ">
                    <?php 
                    if ($origin == 'internal') {
                        echo '®intern®';
                    } elseif ($origin == 'external') {
                        echo '®extern®';
                    } else {
                        echo '®error®';
                    } ?>
                </span>
            </div>
        </div>

        <!-- Has albums -->
        <div class="form-group">
            <label class="col-md-3 control-label">®has_albums_title®</label>
            <p class="form-control-static">
                <?php if ($has_albums) {
                        ?>
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ®yes®
                <?php
                    } else {
                        ?>
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> ®no®
                <?php
                    } ?>
            </p>
        </div>

        <!-- In recorders -->            
        <div class="form-group">
            <label class="col-md-3 control-label">®recorders®</label>
            <p class="view form-control-static">
                <?php if ($in_classroom) {
                        ?>
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ®yes®
                <?php
                    } else {
                        ?>
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> ®no®
                <?php
                    } ?>
            </p>
            <div class="col-md-5">
                <label class="edit">
                    <input type="checkbox" name="in_recorders" <?php echo $in_classroom ? 'checked' :''?> />
                </label>
            </div>
        </div>
    </form>
</div>
    
<!-- Button to edit or remove course -->
<div class="col-md-2 col-md-offset-2">
    <form action="index.php?action=remove_course" method="POST" style="margin:0px;">
        <input type="hidden" name="course_code" value="<?php echo $course_code; ?>" />

        <button type="button" class="btn btn-block btn-primary edit_mode">®edit_button®</button>
        <button type="button" class="btn btn-block edit_cancel">®cancel®</button>
        <?php if ($origin == 'internal') {
                        ?>
            <button type="submit" name="delete" value="®delete®" 
                    onClick="confirm('®delete_confirm®')" 
                    class="btn btn-block btn-danger delete_button" />
            ®delete®
            </button>
        <?php
                    } ?>
    </form>
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
    <?php foreach ($users as $u) {
                        ?>
        <tr data-id="<?php echo $u['ID'] ?>" data-origin="<?php echo $u['origin'] ?>">
            <td><a href="index.php?action=view_user_details&amp;user_ID=<?php echo $u['user_ID']; ?>"><?php echo $u['user_ID']; ?></a></td>
            <td><?php echo $u['forename'] . ' ' . $u['surname']; ?></td>
            <td><span class="label <?php if ($u['origin'] == 'internal') {
                            echo 'label-info';
                        } ?>"><?php if ($u['origin'] == 'internal') {
                            echo '®intern®';
                        } else {
                            echo '®extern®';
                        } ?></span></td>
            <td class="unlink" style="cursor: pointer;"><?php if ($u['origin'] == 'internal') {
                            echo '<span class="glyphicon glyphicon-remove"></span> ®remove_link®';
                        } ?></td>
        </tr>
        <?php
                    } ?>
    </tbody>
</table>

<div class="create_link form-inline text-center">
        <input type="text" class="form-control" name="link_to" value="" class="input-medium" placeholder="®user_ID®" data-provide="typeahead" autocomplete="off" />
        <button type="button" name="link" class="btn btn-primary">®add_user_course®</button>
</div>

<script>
    
$(function() {
    // Default mask edit button
    $('.edit_cancel').hide();
    $('.edit').hide();
    
    // Exit edit mode
    $("button.edit_cancel").click(function() {
        $this = $(this);
        $this.hide();
        $('.edit_mode').addClass('btn-primary');
        $('.edit_mode').removeClass('btn-success');
        $('.edit_mode').removeClass('active_edit_mode'); // remove edit mode
        $('.edit_mode').text("®edit_button®");
        $('.delete_button').show();
        $(".edit").hide();
        $('.view').show();
        
    });
    
    // Active edit mode
    $("button.edit_mode").click(function() {
        $this = $(this);
        $('.edit_cancel').show();
        $('.delete_button').hide();
        
        if($this.hasClass("active_edit_mode")) {
            $("form").first().submit();
            
        } else {
            $this.addClass('active_edit_mode'); // Add edit mode
            $('.edit_mode').removeClass('btn-primary');
            $('.edit_mode').addClass('btn-success');
            $('.edit_mode').text("®submit_button®");
            $(".edit").show();
            $('.view').hide();
            
        }
    });
   
    $(".users_table .unlink").click(function() {
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
               var $delete = $('<td class="unlink" style="cursor:pointer;"><span class="glyphicon glyphicon-remove"></span>®remove_link®</td>');

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