<?php if (isset($pagination)) {
    $pagination->insert();
} ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered table-condensed events sort_col">
        <tr>
            <?php echo $colOrder->insertThSort("events.asset", "Asset"); ?>
            <?php echo $colOrder->insertThSort("origin", "®origin®"); ?>
            <?php echo $colOrder->insertThSort("classroom_id", "®monit_classroom®"); ?>
            <?php echo $colOrder->insertThSort("course", "®monit_courses®"); ?>
            <?php echo $colOrder->insertThSort("author", "®author®"); ?>
            <?php echo $colOrder->insertThSort("cam_slide", "®monit_record_type®"); ?>
            <?php echo $colOrder->insertThSort("event_time", "®monit_event_date®"); ?>
            <?php echo $colOrder->insertThSort("type_id", "®monit_type_id®"); ?>
            <?php echo $colOrder->insertThSort("context", "®monit_context®"); ?>
            <?php echo $colOrder->insertThSort("loglevel", "®monit_log_level®"); ?>
            <?php echo $colOrder->insertThSort("message", "®monit_message®"); ?>
        </tr>

        <?php 
        foreach ($events as &$event) {
            ?>
            <tr class="<?php echo $event['loglevel_name']; ?>">
                <td>
                    <a style="" href="<?php echo url_post_replace_multiple(
                            array('asset' => $event['asset'], 'page' => 1)
        ); ?>">
                        <?php echo $event['asset']; ?>
                    </a>
                </td>
                <td><?php echo $event['origin']; ?></td>
                <td><?php echo $event['classroom_id']; ?></td>
                <td><?php echo $event['course']; ?></td>
                <td><?php echo $event['author']; ?></td>
                <td><?php echo $event['cam_slide']; ?></td>
                <td><?php echo $event['event_time']; ?></td>
                <td><?php echo str_replace("_", "-", $logger->get_type_name($event['type_id'])); ?></td>
                <td><?php echo $event['context']; ?></td>
                <td><span class="label label-<?php echo $event['loglevel_name']; ?>">
                    <?php echo $event['loglevel']. " - " .
                            ucfirst($event['loglevel_name']); ?>
                    </span>
                </td>
                <td
                <?php if (array_key_exists('min_message', $event)) {
                                ?>
                    tabindex="0" data-container="body" data-toggle="popover"
                    data-placement="left" data-content="<?php echo nl2br(htmlspecialchars($event['message'])); ?>">
                    <?php 
                    echo $event['min_message'];
                            } else {
                                echo '>'.$event['message'];
                            } ?>    
                </td>
            </tr>
        <?php
        } ?>
    </table>
</div>

<?php if (isset($pagination)) {
            $pagination->insert();
        } ?>



<script> 
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
    
    $('[data-toggle="popover"]').hover(function(e) {
        closeAll(e.target);
        $(this).popover('show');
    });
    
    function closeAll(element) {
        $('[data-toggle="popover"]').each(function () {
            if(this !== element) {
                $(this).popover('hide');
            } 
        });
    }
    
}); 
</script>