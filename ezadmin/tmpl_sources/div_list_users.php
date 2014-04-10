
<!--
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
-->

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

<table class="table table-striped table-hover table-condensed users">
    <tr>
        <th data-col="user_ID" <?php echo $input['col'] == 'user_ID' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®user_ID®<?php echo ($input['col'] == 'user_ID') ? ($input['order'] == 'ASC' ? ' <i class="icon-chevron-down"></i>' : ' <i class="icon-chevron-up"></i>') : ' <i class="icon-chevron-up" style="visibility: hidden;"></i>' ?></th>
        <th>®fullname®</th>
        <th>®origin®</th>
        <th>®is_admin_title®</th>
    </tr>
    
    <?php foreach($users as $user) {
     
        ?>
        <tr>
            <td><a href="index.php?action=view_user_details&amp;user_ID=<?php echo $user['user_ID']; ?>"><?php echo $user['user_ID']; ?></a></td>
            <td><a href="index.php?action=view_user_details&amp;user_ID=<?php echo $user['user_ID']; ?>"><?php echo $user['forename'] . ' ' . $user['surname']; ?></a></td>
            <td><span class="label <?php if($user['origin'] == 'internal') echo 'label-info'; ?>"><?php if($user['origin'] == 'internal') echo '®intern®'; else echo '®extern®'; ?></span></td>
            <td><?php echo ($user['permissions'] != 0) ? '<i class="icon-ok"></i>' : ''; ?></td>
        </tr>
        <?php
    }
    ?>
        
</table>

<script>
    
$(function(){
   
   $(".pagination li").click(function() {
       if($(this).hasClass('active')) return;
       page($(this).find("a").data("page"));
   });
   
   $("table.users th").click(function() {
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
       var $form = $("form.search_user");
       $form.find("input[name='page']").first().val(n);
       $form.submit();
   }
   
   function sort(col, order) {
       var $form = $("form.search_user");
       $form.find("input[name='col']").first().val(col);
       $form.find("input[name='order']").first().val(order);
       $form.submit();
   }
});  
    
</script>