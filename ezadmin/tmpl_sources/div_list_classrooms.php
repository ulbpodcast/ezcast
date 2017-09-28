<?php 
if (isset($pagination)) {
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
        foreach ($listClassrooms as $currClass) {
            ?>
        <tr class="line_classroom <?php echo $currClass['enabled'] ? 'enable' : ''; ?>" 
            id="<?php echo preg_replace('/[\s.]+/', '', $currClass['room_ID']); ?>">
            <td style="text-align: center;" class="status"> <?php echo $currClass['enabled'] ? "<img style='height: 16px;' src='img/loading_transparent.gif'/>" : ''; ?></td>
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
                <?php if (array_key_exists('IP_remote', $currClass)) {
                ?>
                    <div class="view">
                        <a target="_blank" href="http://<?php echo $currClass['IP_remote']; ?>/ezrecorder/">
                            <?php echo $currClass['IP_remote'] ?>
                        </a> 
                        <?php if (isset($currClass['IP_remote']) && $currClass['IP_remote'] != "") {
                    ?>
                            <a target="_blank" href="vnc://<?php echo $currClass['IP_remote']; ?>/">
                                (VNC)
                            </a>
                        <?php
                } ?>
                    </div>
                    <div class="edit" style="display:none;">
                        <input class="form-control input-xsm" type="text" name="ip_remote" 
                               value="<?php echo htmlspecialchars($currClass['IP_remote']) ?>" />
                    </div>
                <?php
            } ?>
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
                <button class="btn btn-xs enabled_button <?php echo !$currClass['enabled'] ? 'btn-success' : ''; ?>">
                    <?php echo !$currClass['enabled'] ? '®enable®' : '®disable®'; ?>
                </button>
                <button class="btn btn-xs edit_button"><span class="glyphicon glyphicon-edit"></span></button>
                <button class="btn btn-xs btn-danger cancel_button"><span class="glyphicon glyphicon-remove"></span></button>
                <button class="btn btn-xs btn-danger delete_button"><span class="glyphicon glyphicon-trash"></span></button>
            </td>
        </tr>
        <tr class="recording" id="<?php echo preg_replace('/[\s.]+/', '', $currClass['room_ID']); ?>_recording" 
            style="display: none;">
            <td></td>
            <td colspan="7" style="padding-bottom: 12px;">
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-record" aria-hidden="true"></span>
                    ®status_record_general®: <span class="status_general" ></span><br />
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    ®monit_author®: 
                    <a class="author" href="#"></a>
                </div>
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-facetime-video" aria-hidden="true"></span>
                    ®classroom_record_cam®: <span class="status_cam" ></span><br />
                    <span class="glyphicon glyphicon-education" aria-hidden="true"></span>
                    ®monit_courses®: 
                    <a class="course" href="#"></a>
                </div>
                <div class="col-md-3">
                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                    ®classroom_record_slide®: <span class="status_slides" ></span><br /><br />
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
                    Asset: 
                    <a class="asset" href="#"></a>
                </div>
                <div class="col-md-3"><br />
                    <span class="label loglevel"></span>
                </div>
            </td>
        </tr>
        <?php
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

<script async>
$(function() {
    function classroom_online(classroom, data) {
        $('#' + classroom + ' .status').html('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
        
        if(data.status_general == 'recording' || data.status_general == 'paused' || data.status_general == 'stopped' || data.status_general == 'open') {
            $('#' + classroom + '_recording').show();
            $('#' + classroom + '_recording .status_general').text(data.status_general);
            $('#' + classroom + '_recording .status_cam').text(data.status_cam);
            $('#' + classroom + '_recording .status_slides').text(data.status_slides);
            $('#' + classroom + '_recording .author').text(data.author);
            $('#' + classroom + '_recording .author').attr("href", "./index.php?action=view_user_details&user_ID=" + data.author);
            $('#' + classroom + '_recording.recording').addClass(data.loglevel);
            
            $('#' + classroom + '_recording .loglevel').addClass('label-' + data.loglevel);
            $('#' + classroom + '_recording .loglevel').text((data.loglevel).charAt(0).toUpperCase() + (data.loglevel).slice(1));
            $('#' + classroom + '_recording .course').text(data.course);
            $('#' + classroom + '_recording .course').attr("href", './index.php?action=view_course_details&course_code=' + data.course);
            $('#' + classroom + '_recording .asset').html(data.asset + ' <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>');
            $('#' + classroom + '_recording .asset').attr("href", './index.php?action=view_events&post=&startDate=0&asset=' + data.asset);
        } else {
            <?php if ($onlyRecording) {
        ?>
                $('#' + classroom).hide();
            <?php
    } ?>
        }
    }
    
    function classroom_offline(classroom) {
        <?php if ($onlyOnline) {
        ?>
            $('#' + classroom).hide();
        <?php
    } else {
        ?>
            $('#' + classroom + ' .status').html("<span title=\"®no_ping®\"><span class=" + 
                        "\"glyphicon glyphicon-warning-sign\"></span></span>");
        <?php
    } ?>
    }
   
    function updateStatus(classroom) {
        $.ajax("index.php?action=get_classrooms_status", {
            type: "post",
            data: {
                classroomId: classroom
            },
            success: function(jqXHR, textStatus) {

                var data = JSON.parse(jqXHR);
                classroomWithoutSpace = classroom.replace(/[\s.]/g,'');
                
                if(data.status && data.status == '1') {
                    classroom_online(classroomWithoutSpace, data);
                } else {
                    classroom_offline(classroomWithoutSpace);
                }
            }
        });
    }
    
    var globalIndex = 0;
    var allClassroom = $('.line_classroom.enable');
    for(var i = 0; i < allClassroom.length; ++i) {
        setTimeout(function() {
            var classroom = $(allClassroom[globalIndex]);
            ++globalIndex;
            var classRoomId = classroom.find($(".room_id"));
            var strClassRoomId = classRoomId.text().trim();
            updateStatus(strClassRoomId);
        }, 0);
    }
    
});

</script>
