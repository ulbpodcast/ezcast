

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

<table class="table table-striped table-hover table-condensed table-responsive events">
    <tr>
        <th>Asset</th>
        <th>®origin®</th>
        <th>®monit_classroom®</th>
        <th>®author®</th>
        <th>®monit_record_type®</th>
        <th data-col="event_date" <?php echo $input['col'] == 'event_date' ? 'data-order="' . $input["order"] . '"' : '' ?> 
            style="cursor:pointer;">®monit_event_date®
                <?php echo ($input['col'] == 'event_date') ? 
                    ($input['order'] == 'ASC' ? 
                        ' <span class="glyphicon glyphicon-chevron-down"></span>' : 
                        ' <span class="glyphicon glyphicon-chevron-up"></span>') 
                    : ' <span class="glyphicon glyphicon-chevron-up" style="visibility: hidden;"></span>' ?>
        </th>
        <th>®monit_context®</th>
        <th>®monit_log_level®</th>
        <th>®monit_message®</th>
    </tr>
        
    <?php foreach($events as $event) { ?>
        <tr>
            <td><?php echo $event['asset']; ?></td>
            <td><?php echo $event['origin']; ?></td>
            <td><?php echo $event['asset_classroom_id']; ?></td>
            <td><?php echo $event['asset_author']; ?></td>
            <td><?php echo $event['asset_cam_slide']; ?></td>
            <td><?php echo $event['event_time']; ?></td>
            <td><?php echo $event['context']; ?></td>
            <td><?php echo $event['loglevel']; ?></td>
            <td><?php echo $event['message']; ?></td>
        </tr>
    <?php } ?>
</table>



<script>
    
$(function() {
    
}
    $(".pagination li").click(function() {
        if($(this).hasClass('active')) return;
        page($(this).find("a").data("page"));
    });

    $("table.events th").click(function() {
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
        var $form = $("form.search_event");
        $form.find("input[name='page']").first().val(n);
        $form.submit();
    }

    function sort(col, order) {
        var $form = $("form.search_event");
        $form.find("input[name='col']").first().val(col);
        $form.find("input[name='order']").first().val(order);
        $form.submit();
    }
    
</script>