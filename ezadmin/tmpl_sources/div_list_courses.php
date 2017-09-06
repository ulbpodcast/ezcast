

<?php if ($max > 0) {
    ?>

<div class="text-center">
    <ul class="pagination">
        <li <?php if ($input['page'] == 1) {
        echo 'class="disabled"';
    } ?>>
            <a href="#" data-page="<?php echo $input['page']-1 ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li <?php echo $input['page'] == 1 ? 'class="active"' : ''?>><a href="#" data-page="1">1</a></li>
        
        <?php if ($input['page'] > 5) {
        ?>
           <li><a href="#" data-page="0">...</a></li>
        <?php
    } ?>
           
         <?php $start = $input['page'] > 4 ? $input['page']-3 : 2 ?>
           
        <?php for ($i = $start; $i < $max && $i < $start+7; ++$i) {
        ?>
           <li <?php echo $input['page'] == $i ? 'class="active"' : ''?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
        <?php
    } ?>
        
        <?php if ($input['page']+7 < $max) {
        ?>
           <li><a href="#" data-page="0">...</a></li>
        <?php
    } ?> 
           
        <?php if ($max != 1) {
        ?>
        <li <?php echo $input['page'] == $max? 'class="active"' : ''?>><a href="#" data-page="<?php echo $max ?>"><?php echo $max ?></a></li>
        <?php
    } ?>
        <li>
            <a href="#" data-page="<?php echo $input['page']+1 ?>"><span aria-hidden="true">&raquo;</span></a>
        </li>
    </ul>
</div>

<?php
} ?>

<table class="table table-striped table-hover table-condensed table-responsive courses">
    <tr>
        <th>®ID®</th>        
        <th data-col="course_code" <?php echo $input['col'] == 'course_code' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®course_name®<?php echo ($input['col'] == 'course_code') ? ($input['order'] == 'ASC' ? ' <span class="glyphicon glyphicon-chevron-down"></span>' : ' <span class="glyphicon glyphicon-chevron-up"></span>') : ' <span class="glyphicon glyphicon-chevron-up" style="visibility: hidden;"></span>' ?></th>
        <th data-col="course_code" <?php echo $input['col'] == 'course_code' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®course_code®<?php echo ($input['col'] == 'course_code') ? ($input['order'] == 'ASC' ? ' <span class="glyphicon glyphicon-chevron-down"></span>' : ' <span class="glyphicon glyphicon-chevron-up"></span>') : ' <span class="glyphicon glyphicon-chevron-up" style="visibility: hidden;"></span>' ?></th>
        <th data-col="user_ID" <?php echo $input['col'] == 'user_ID' ? 'data-order="' . $input["order"] . '"' : '' ?> style="cursor:pointer;">®teacher®<?php echo ($input['col'] == 'user_ID') ? ($input['order'] == 'ASC' ? ' <span class="glyphicon glyphicon-chevron-down"></span>' : ' <span class="glyphicon glyphicon-chevron-up"></span>') : ' <span class="glyphicon glyphicon-chevron-up" style="visibility: hidden;"></span>' ?></th>
        <th>®origin®</th>
        <th>®albums®</th>
        <th>®recorders®</th>
    </tr>
        
    <?php foreach ($courses as $course) {
        ?>
        <tr>               
            <td><span title="<?php echo $course['course_code']; ?>"><a href="index.php?action=view_course_details&amp;course_code=<?php echo urlencode($course['course_code']); ?>"><?php echo  $course['course_code']; ?></a></span></td>
            <td><span title="<?php echo $course['course_code']; ?>"><a href="index.php?action=view_course_details&amp;course_code=<?php echo urlencode($course['course_code']); ?>"><?php echo $course['course_name']; ?></a></span></td>
            <td><span title="<?php echo $course['course_name']; ?>"><a href="index.php?action=view_course_details&amp;course_code=<?php echo urlencode($course['course_code']); ?>"><?php if (isset($course['course_code_public']) && $course['course_code_public']!="") {
            echo $course['course_code_public'];
        } else {
            echo $course['course_code'];
        } ?></a></span></td>

            <?php global $use_user_name;
        if ($use_user_name) {
            ?>
                <td><span title="<?php echo $course['user_ID']; ?>"><a href="index.php?action=view_user_details&amp;user_ID=<?php echo urlencode($course['user_ID']); ?>"><?php echo $course['forename'].' '.$course['surname']; ?></a></span></td>
            <?php
        } else {
            ?>
                <td><span title="<?php echo $course['forename'].' '.$course['surname']; ?>"><a href="index.php?action=view_user_details&amp;user_ID=<?php echo urlencode($course['user_ID']); ?>"><?php echo $course['user_ID']; ?></a></span></td>
            <?php
        } ?>
            <td>
                <span class="label 
                    <?php if ($course['origin'] == 'internal') {
            echo 'label-info';
        } elseif ($course['origin'] == 'external') {
            echo 'label-primary';
        } else {
            echo 'label-danger';
        } ?>
                    ">
                    <?php 
                    if ($course['origin'] == 'internal') {
                        echo '®intern®';
                    } elseif ($course['origin'] == 'external') {
                        echo '®extern®';
                    } else {
                        echo '®error®';
                    } ?>
                </span>
            </td>
            <td><?php echo $course['has_albums'] ? '<span class="glyphicon glyphicon-ok"></span>' : ''; ?></td>
            <td><?php echo $course['in_recorders'] ? '<span class="glyphicon glyphicon-ok"></span>' : ''; ?></td>
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
   
   $("table.courses th").click(function() {
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
       var $form = $("form.search_course");
       $form.find("input[name='page']").first().val(n);
       $form.submit();
   }
   
   function sort(col, order) {
       var $form = $("form.search_course");
       $form.find("input[name='col']").first().val(col);
       $form.find("input[name='order']").first().val(order);
       $form.submit();
   }
});  
    
</script>
