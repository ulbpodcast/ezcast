

<?php if(isset($pagination)) {
    $pagination->insert();
} ?>

<table class="table table-striped table-hover table-condensed table-responsive events sort_col">
    <tr>
        <th>Asset</th>
        <th>®origin®</th>
        <th>®monit_classroom®</th>
        <th>®monit_courses®</th>
        <th>®author®</th>
        <th>®monit_record_type®</th>
        <?php echo $colOrder->insertThSort("event_time", "®monit_event_date®"); ?>
        <th>®monit_type_id®</th>
        <th>®monit_context®</th>
        <th>®monit_log_level®</th>
        <th>®monit_message®</th>
    </tr>
        
    <?php 
    foreach($events as &$event) { ?>
        <tr>
            <td><?php echo $event['asset']; ?></td>
            <td><?php echo $event['origin']; ?></td>
            <td><?php echo $event['asset_classroom_id']; ?></td>
            <td><?php echo $event['asset_course']; ?></td>
            <td><?php echo $event['asset_author']; ?></td>
            <td><?php echo $event['asset_cam_slide']; ?></td>
            <td><?php echo $event['event_time']; ?></td>
            <td><?php echo $logger->get_type_name($event['type_id']); ?></td>
            <td><?php echo $event['context']; ?></td>
            <td><span class="label label-<?php echo $event['loglevel_name']; ?>">
                <?php echo $event['loglevel']. " - " .
                        $event['loglevel_name']; ?>
                </span>
            </td>
            <td><?php echo $event['message']; ?></td>
        </tr>
    <?php } ?>
</table>



<script>
    
$(function() {
    
    
    $("table.sort_col th").click(function() {
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

    function sort(col, order) {
        var $form = $("form.search_event");
        $form.find("input[name='col']").first().val(col);
        $form.find("input[name='order']").first().val(order);
        $form.submit();
    }
    
});
    
</script>