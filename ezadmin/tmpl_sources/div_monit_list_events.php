

<?php if($max > 0) { ?>

<div class="text-center">
    <ul class="pagination">
        <li <?php if($input['page'] == 1) { echo 'class="disabled"'; } ?>>
            <a href="#" data-page="<?php echo $input['page']-1 ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
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
        <li>
            <a href="#" data-page="<?php echo $input['page']+1 ?>"><span aria-hidden="true">&raquo;</span></a>
        </li>
    </ul>
</div>

<?php } ?>