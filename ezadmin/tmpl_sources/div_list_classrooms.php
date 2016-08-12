<?php 
if(isset($pagination)) {
    $pagination->insert();
} 
?>

<form class="classroom_update" style="display:hidden" method="POST">
    <input type="hidden" name="update" />
    <input type="hidden" name="a_room_ID" value=""/>
    <input type="hidden" name="u_room_ID" value=""/>
    <input type="hidden" name="u_name" value=""/>
    <input type="hidden" name="u_ip" value=""/>
    <input type="hidden" name="u_ip_remote" value=""/>
</form>

<table class="table table-striped table-bordered table-hover table-responsive table-condensed classrooms table-left">
    <tr>
        <th></th>
        <?php echo $colOrder->insertThSort("room_ID", "®room_ID®"); ?>
        <?php echo $colOrder->insertThSort("name", "®room_name®"); ?>
        <?php echo $colOrder->insertThSort("IP", "®room_IP®"); ?>
        <?php echo $colOrder->insertThSort("IP_remote", "®room_remote_IP®"); ?>
        <th>®room_enabled®</th>
        <th></th>
        <th></th>
    </tr>
    
    <?php 
        foreach($listClassrooms as $currClass) {
        ?>
        <tr>
            <td style="text-align: center;">
                <?php 
                if((!array_key_exists('online', $currClass) || !$currClass['online']) && 
                        $currClass['enabled']) {
                    echo '<span title="®no_ping®"><span class="glyphicon glyphicon-warning-sign"></span></span>';
                }
                //TODO: move this in javascript, this heavily slow down page loading if some recorders are offline
                //exec('ping -c 1 -W 1 '.$currClass['IP'], $output, $return_val); if($return_val != 0) 
                //echo '<span title="®no_ping®"><span class="glyphicon glyphicon-warning-sign"></span></span>'; ?>
            </td>
            <td class="room_id">
                <a class="view" href="index.php?action=view_classroom_calendar&post=&classroom=<?php echo $currClass['room_ID']; ?>&nweek=4">
                    <?php echo $currClass['room_ID']; ?>
                </a>
                <div class="edit" style="display:none;">
                    <input class="form-control input-xsm" type="text" name="new_room_ID" 
                           value="<?php echo htmlspecialchars($currClass['room_ID']) ?>"/>
                </div>
            </td>
            <td class="name">
                <div class="view">
                    <?php echo $currClass['name'] ?>
                </div>
                <div class="edit" style="display:none;">
                    <input class="form-control input-xsm" type="text" name="name" 
                           value="<?php echo htmlspecialchars($currClass['name']) ?>"/>
                </div>
            </td>
            <td class="ip">
                <div class="view">
                    <a target="_blank" href="http://<?php echo $currClass['IP']; ?>/ezrecorder/">
                        <?php echo $currClass['IP'] ?>
                    </a> 
                    <a target="_blank" href="vnc://<?php echo $currClass['IP']; ?>/">
                        (VNC)
                    </a>
                </div>
                <div class="edit" style="display:none;">
                    <input class="form-control input-xsm" type="text" name="ip" 
                           value="<?php echo htmlspecialchars($currClass['IP']) ?>"/>
                </div>
            </td>
            <td class="ip_remote">
                <?php if(array_key_exists('IP_remote', $currClass)) { ?>
                    <div class="view">
                        <a target="_blank" href="http://<?php echo $currClass['IP_remote']; ?>/ezrecorder/">
                            <?php echo $currClass['IP_remote'] ?>
                        </a> 
                        <?php if(isset($currClass['IP_remote']) && $currClass['IP_remote'] != "") { ?>
                            <a target="_blank" href="vnc://<?php echo $currClass['IP_remote']; ?>/">
                                (VNC)
                            </a>
                        <?php } ?>
                    </div>
                    <div class="edit" style="display:none;">
                        <input class="form-control input-xsm" type="text" name="ip_remote" 
                               value="<?php echo htmlspecialchars($currClass['IP_remote']) ?>" />
                    </div>
                <?php } ?>
            </td>
            <td style="text-align: center;">
                <span class="glyphicon glyphicon-<?php echo $currClass['enabled'] ? 'ok' : 'remove'; ?>"></span>
            </td>
            <td style="text-align: center">
                <a href="index.php?action=view_classroom_calendar&post=&classroom=<?php echo $currClass['room_ID']; ?>&nweek=4"
                   class="btn btn-default btn-xs" role="button">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                </a>
            </td>
            <td style="text-align: right;">
                <button class="btn btn-xs enabled_button <?php echo !$currClass['enabled'] ? 'btn-success' : '' ?>">
                    <?php echo !$currClass['enabled'] ? '®enable®' : '®disable®' ?>
                </button>
                <button class="btn btn-xs edit_button"><span class="glyphicon glyphicon-edit"></span></button>
                <button class="btn btn-xs btn-danger cancel_button"><span class="glyphicon glyphicon-remove"></span></button>
                <button class="btn btn-xs btn-danger delete_button"><span class="glyphicon glyphicon-trash"></span></button>
            </td>
        </tr>
        <?php if(array_key_exists('recording', $currClass) && $currClass['recording']) { ?>
        <tr class="<?php echo $currClass['loglevel']; ?>">
            <td></td>
            <td colspan="7" style="padding-bottom: 12px;">
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-record" aria-hidden="true"></span>
                    ®status_record_general®: 
                    <?php echo $currClass['status_general']; ?><br />
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    ®monit_author®: 
                    <a href="./index.php?action=view_user_details&user_ID=<?php echo $currClass['author']; ?>">
                        <?php echo $currClass['author']; ?>
                    </a>
                </div>
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-facetime-video" aria-hidden="true"></span>
                    ®classroom_record_cam®: 
                    <?php echo $currClass['status_cam']; ?><br />
                    <span class="glyphicon glyphicon-education" aria-hidden="true"></span>
                    ®monit_courses®: 
                    <a href="./index.php?action=view_course_details&course_code=<?php echo $currClass['course']; ?>">
                        <?php echo $currClass['course']; ?>
                    </a>
                </div>
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                    ®classroom_record_slide®: 
                    <?php echo $currClass['status_slides']; ?><br />
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
                    Asset: 
                    <a href="./index.php?action=view_events&post=&startDate=0&asset=<?php echo $currClass['asset']; ?>">
                        <?php echo $currClass['asset']; ?>
                        <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>
                    </a>
                </div>
                <div class="col-md-3">
                    <br />
                    <span class="label label-<?php echo $currClass['loglevel']; ?>">
                    <?php echo ucfirst($currClass['loglevel']); ?>
                    </span>
                </div>
            </td>
        </tr>
        <?php }
    }
    ?>
</table>

<script>
    
$(function() {
   
    $("table.classrooms .enabled_button").click(function() {
       $this = $(this);

        var room = $this.parent().parent().find("td.room_id .view").text();
       
        if($this.hasClass('btn-success')) {
            $.ajax("index.php?action=enable_classroom", {
                type: "post",
                data: {
                    id: room
                },
                success: function(jqXHR, textStatus) {

                    var data = JSON.parse(jqXHR);

                    if(data.error) {
                        if(data.error == '1') alert("®room_enable_error®");
                        return;
                    }

                    $this.removeClass('btn-success');
                    $this.text('®disable®');
                    var icon = $this.parent().prev().find(".glyphicon");
                    icon.removeClass('glyphicon-remove');
                    icon.addClass('glyphicon-ok');
                }
            });
        } else {
            $.ajax("index.php?action=disable_classroom", {
                type: "post",
                data: {
                    id: room
                },
                success: function(jqXHR, textStatus) {
                    
                    var data = JSON.parse(jqXHR);
                    
                    if(data.error) {
                        if(data.error == '1') alert("®room_enable_error®");
                        return;
                    }
           
                    $this.addClass('btn-success');
                    $this.text('®enable®');
                    var icon = $this.parent().prev().find(".glyphicon");
                    icon.removeClass('glyphicon-ok');
                    icon.addClass('glyphicon-remove');
                }
            });
        }
    });
    
    $("table.classrooms .edit_button").click(function() {
        var $this = $(this);
        
        if($this.hasClass('btn-primary')) {
            var $tr = $this.parent().parent();
            var $form = $("form.classroom_update");
            
            $form.find("input[name='a_room_ID']").val($tr.find('td.room_id .view').text());
            $form.find("input[name='u_room_ID']").val($tr.find('td.room_id input').val());
            $form.find("input[name='u_name']").val($tr.find('td.name input').val());
            $form.find("input[name='u_ip']").val($tr.find('td.ip input').val());
            $form.find("input[name='u_ip_remote']").val($tr.find('td.ip_remote input').val());
            
            $form.submit();
        } else {
            edit_reset();
            $this.addClass('btn-primary');
            $this.siblings('.delete_button').hide().siblings('.cancel_button').show();
            $this.parent().parent().find('.view').hide().siblings('.edit').show();
        }
    });
    
    $("table.classrooms .cancel_button").click(function() {
        edit_reset();
    }).hide();
    
    function edit_reset() {
        $("table.classrooms .view").show();
        $("table.classrooms .edit").hide();
        $("table.classrooms .edit_button").removeClass('btn-primary');
        $("table.classrooms .delete_button").show();
        $("table.classrooms .cancel_button").hide();
    }
    
    $("table.classrooms .delete_button").click(function() {
        if(!confirm('®delete_confirm®')) return;
        var $this = $(this);
        
        var room = $this.parent().parent().find("td.room_id .view").text();
        
        $.ajax("index.php?action=remove_classroom", {
             type: "post",
             data: {
                id: room
             },
             success: function(jqXHR, textStatus) {
                var data = JSON.parse(jqXHR);

                if(data.error) {
                    if(data.error == '1') alert("®room_delete_error®");
                    return;
                }
                
                $this.parent().parent().hide(400, function() { $(this).remove(); });
            }
       });
    });
    
});

</script>
