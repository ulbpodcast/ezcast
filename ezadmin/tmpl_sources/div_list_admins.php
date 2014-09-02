
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
<h4>®admins_list_title®</h4>

<table class="table table-striped table-bordered table-hover users_table">
    <thead>
        <tr>
            <th>®user_ID®</th>
            <th>®username®</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($admins as $a) { ?>
        <tr data-id="<?php echo $a['user_ID'] ?>">
            <td><a href="index.php?action=view_user_details&amp;user_ID=<?php echo $a['user_ID']; ?>"><?php echo $a['user_ID']; ?></a></td>
            <td><?php echo $a['forename'] . ' ' . $a['surname']; ?></td>
            <td class="unlink" style="cursor: pointer;"><i class="icon-remove"></i>®remove_admin®</td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<div class="create_link form-inline">
    <input type="text" name="link_to" value="" class="input-medium" placeholder="®user_ID®" data-provide="typeahead" autocomplete="off" />
    <button name="link" class="btn btn-primary">®add_admin®</button>
</div>

<script>
   
$(".users_table .unlink").live("click", function() {
    $this = $(this);
    
    //if($this.parent().data('origin') == 'external') alert("®cannot_delete_external®");
    if(!confirm("®remove_admin_confirm®")) return false;
   
    var user_ID = $this.parent().data("id");
    
    $.ajax("index.php?action=remove_admin", {
        type: "post",
        data: {
            user_ID: user_ID
        },
        success: function(jqXHR, textStatus) {
            console.log(jqXHR);
            var data = JSON.parse(jqXHR);
            
            if(data.error) {
                if(data.error == 1) alert("®cannot_delete_admin®");
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
    
    $.ajax("index.php?action=add_admin", {
       type: "post",
       data: {
           user_ID: user
       },
       success: function(jqXHR, textStatus) {
           console.log(jqXHR);
           var data = JSON.parse(jqXHR);
           
           if(data.error) {
               if(data.error == '1') alert("®add_admin_error®");
               return;
           }
           
           var $netid = $('<td></td>');
           $netid.append($('<a href=""></a>').attr("href", 'index.php?action=view_user_details&amp;user_ID='+data.user_ID).text(data.user_ID));
           var $username = $('<td></td>').text(data.forename + ' ' + data.surname);
           var $delete = $("<td class=\"unlink\" style=\"cursor:pointer;\"><i class=\"icon-remove\"></i>®remove_admin®</td>");
           
           var $tr = $('<tr data-id="' + data.user_ID + '"></tr>');
           $tr.append($netid);
           $tr.append($username);
           $tr.append($delete);
           
           $tr.hide();
           
           $('.users_table tbody').append($tr);
           
           $tr.show(400).css('display', 'table-row');
        }
    });
 });
    
</script>