

<?php if(isset($pagination)) {
    $pagination->insert();
} ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered table-condensed events sort_col">
        <tr>
            <?php echo $colOrder->insertThSort("asset", "Asset"); ?>
            <?php echo $colOrder->insertThSort("origin", "®origin®"); ?>
            <?php echo $colOrder->insertThSort("asset_classroom_id", "®monit_classroom®"); ?>
            <?php echo $colOrder->insertThSort("asset_course", "®monit_courses®"); ?>
            <?php echo $colOrder->insertThSort("asset_author", "®author®"); ?>
            <?php echo $colOrder->insertThSort("asset_cam_slide", "®monit_record_type®"); ?>
            <?php echo $colOrder->insertThSort("event_time", "®monit_event_date®"); ?>
            <?php echo $colOrder->insertThSort("type_id", "®monit_type_id®"); ?>
            <?php echo $colOrder->insertThSort("context", "®monit_context®"); ?>
            <?php echo $colOrder->insertThSort("loglevel", "®monit_log_level®"); ?>
            <?php echo $colOrder->insertThSort("message", "®monit_message®"); ?>
        </tr>

        <?php 
        foreach($events as &$event) { ?>
            <tr class="<?php echo $event['loglevel_name']; ?>">
                <td>
                    <a style="" href="<?php echo url_post_replace('asset', $event['asset']); ?>">
                        <?php echo $event['asset']; ?>
                    </a>
                </td>
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
                            ucfirst($event['loglevel_name']); ?>
                    </span>
                </td>
                <td
                <?php if(array_key_exists('min_message', $event)) { ?>
                    data-container="body" data-toggle="popover" data-trigger="hover" 
                        data-placement="right" data-content="<?php echo $event['message']; ?>">
                    <?php 
                    echo $event['min_message'];
                } else { 
                    echo '>'.$event['message'];
                } ?>    
                </td>
            </tr>
        <?php } ?>
    </table>
</div>


<script> 
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
}); 
</script>