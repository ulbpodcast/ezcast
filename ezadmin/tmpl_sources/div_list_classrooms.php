
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

<?php if($max > 0) { ?>

<div class="pagination">
    <ul>
        <li><a href="#" data-page="<?php echo $input['page']-1 ?>">Prev</a></li>
        <li <?php echo $input['page'] == 1 ? 'class="active"' : ''?>><a href="#" data-page="1">1</a></li>
        
        <?php if($input['page'] > 5) { ?>
           <li><a href="#" data-page="0">...</a></li>
        <?php } ?>
           
         <?php $start = $input['page'] > 4 ? $input['page']-3 : 2 ?>
           
        <?php for($i = $start; $i < $max && $i < $start+7; ++$i){ ?>
           <li <?php echo $input['page'] == $i ? 'class="active"' : ''?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
        <?php } ?>
        
        <?php if($input['page']+7 < $max) { ?>
           <li><a href="#" data-page="0">...</a></li>
        <?php } ?> 
           
        <?php if($max != 1) { ?>
        <li <?php echo $input['page'] == $max? 'class="active"' : ''?>><a href="#" data-page="<?php echo $max ?>"><?php echo $max ?></a></li>
        <?php } ?>
        <li><a href="#" data-page="<?php echo $input['page']+1 ?>">Next</a></li>
    </ul>
</div>

<?php } ?>

<form class="classroom_update" style="display:hidden" method="POST">
    <input type="hidden" name="update" />
    <input type="hidden" name="room_ID" value=""/>
    <input type="hidden" name="u_room_ID" value=""/>
    <input type="hidden" name="u_name" value=""/>
    <input type="hidden" name="u_ip" value=""/>
    <input type="hidden" name="u_ip_remote" value=""/>
</form>

<table class="table table-striped table-hover table-condensed classrooms">
    <tr>
        <th></th>
        <th data-col="room_ID" <?php echo $input['col'] == 'room_ID' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®room_ID®<?php echo ($input['col'] == 'room_ID') ? ($input['order'] == 'ASC' ? ' <i class="icon-chevron-down"></i>' : ' <i class="icon-chevron-up"></i>') : ' <i class="icon-chevron-up" style="visibility: hidden;"></i>' ?></th>
        <th data-col="name" <?php echo $input['col'] == 'name' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®room_name®<?php echo ($input['col'] == 'name') ? ($input['order'] == 'ASC' ? ' <i class="icon-chevron-down"></i>' : ' <i class="icon-chevron-up"></i>') : ' <i class="icon-chevron-up" style="visibility: hidden;"></i>' ?></th>
        <th>®room_IP®</th>
        <th>®room_remote_IP®</th>
        <th>®room_enabled®</th>
        <th>®enable_disable®</th>
        <th></th>
    </tr>
    
    <?php foreach($classrooms as $classroom) {
     
        ?>
        <tr>
            <td>
                <?php exec('ping '.$classroom['IP'] . ' 10', $output, $return_val); if($return_val != 0) echo '<span title="®no_ping®"><i class="icon-warning-sign"></i></span>'; ?>
            </td>
            <td class="room_id">
                <div class="view"><?php echo $classroom['room_ID'] ?></div>
                <div class="edit" style="display:none;"><input class="input-small" type="text" name="room_ID" value="<?php echo htmlspecialchars($classroom['room_ID']) ?>"/></div>
            </td>
            <td class="name">
                <div class="view"><?php echo $classroom['name'] ?></div>
                <div class="edit" style="display:none;"><input type="text" name="name" value="<?php echo htmlspecialchars($classroom['name']) ?>"/></div>
            </td>
            <td class="ip">
                <div class="view"><a target="_blank" href="http://<?php echo $classroom['IP']; ?>/ezrecorder/"><?php echo $classroom['IP'] ?></a> <a target="_blank" href="vnc://<?php echo $classroom['IP']; ?>/">(VNC)</a></div>
                <div class="edit" style="display:none;"><input type="text" name="ip" value="<?php echo htmlspecialchars($classroom['IP']) ?>"/></div>
            </td>
            <td class="ip_remote">
                <div class="view"><a target="_blank" href="http://<?php echo $classroom['IP_remote']; ?>/ezrecorder/"><?php echo $classroom['IP_remote'] ?></a> <?php if(isset($classroom['IP_remote']) && $classroom['IP_remote'] != "") { ?><a target="_blank" href="vnc://<?php echo $classroom['IP_remote']; ?>/">(VNC)</a><?php } ?></div>
                <div class="edit" style="display:none;"><input type="text" name="ip_remote" value="<?php echo htmlspecialchars($classroom['IP_remote']) ?>"/></div>
            </td>
            <td>
                <?php echo $classroom['enabled'] ? '<i class="icon-ok"></i>' : '<i></i>'; ?>
            </td>
            <td>
                <button class="btn btn-small enabled_button <?php echo !$classroom['enabled'] ? 'btn-success' : '' ?>"><?php echo !$classroom['enabled'] ? '®enable®' : '®disable®' ?></button>
            </td>
            <td>
                <button class="btn btn-small edit_button"><i class="icon-edit"></i></button>
                <button class="btn btn-small cancel_button"><i class="icon-remove"></i></button>
                <button class="btn btn-small delete_button"><i class="icon-trash"></i></button>
            </td>
        </tr>
        <?php
    }
    ?>
</table>

<script>
    
$(function() {
    $(".pagination li").click(function() {
        if($(this).hasClass('active')) return;
        page($(this).find("a").data("page"));
    });

    $("table.classrooms th").click(function() {
        var col = $(this).data('col');

        if(!col) return;

        var order = $(this).data('order');

        if(order == 'ASC') order = 'DESC';
        else order = 'ASC';

        // remove other col sort
        $(this).parent().find("th").each(function() {
            $(this).data('order', '');
        })

        // update col sort
        $(this).data('order', order);

        sort(col, order);
    });

    function page(n) {
        if(!n || n < 1 || n > <?php echo $max ?>) return;
        var $form = $("form.search_classroom");
        $form.find("input[name='page']").first().val(n);
        $form.submit();
    }

    function sort(col, order) {
        var $form = $("form.search_classroom");
        $form.find("input[name='col']").first().val(col);
        $form.find("input[name='order']").first().val(order);
        $form.submit();
    }
   
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
                $this.parent().prev().find("i").addClass('icon-ok');
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
                $this.parent().prev().find("i").removeClass('icon-ok');
            }
          });
       }
    });
    
    $("table.classrooms .edit_button").click(function() {
        var $this = $(this);
        
        if($this.hasClass('btn-primary')) {
            var $tr = $this.parent().parent();
            var $form = $("form.classroom_update");
            
            $form.find("input[name='room_ID']").val($tr.find('td.room_id .view').text());
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
