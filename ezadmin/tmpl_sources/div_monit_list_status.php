<?php if(isset($pagination)) {
    $pagination->insert();
} ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered table-condensed events sort_col">
        <tr>
            <th></th>
            <?php echo $colOrder->insertThSort("asset", "Asset"); ?>
            <?php echo $colOrder->insertThSort("status_time", "®monit_status_date®"); ?>
            <th>®author®</th>
            <th>®monit_status®</th>
            <th>®monit_message®</th>
        </tr>

        <?php 
        foreach($resStatus as &$status) { ?>
            <tr class="<?php echo EventStatus::getColorStatus($status['status']); ?>">
                <td></td>
                <td style="text-align: left;">
                    <a href="./index.php?action=view_events&post=&startDate=0&asset=<?php echo $status['asset']; ?>">
                        <?php echo $status['asset']; ?>
                    </a>
                    <?php if(array_key_exists('children_asset', $status)) {
                        foreach($status['children_asset'] as $children) {
                            echo '<br />';
                            echo '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ';
                            echo '<a href="./index.php?action=view_events&post=&startDate=0&asset='.
                                    $children.'">';
                            echo $children;
                            echo '</a>';
                        }
                    } ?>
                </td>
                <td><?php echo $status['status_time']; ?></td>
                <td><?php echo $status['author']; ?></td>
                <td><?php echo $status['status']; ?></td>
                <td
                <?php if(array_key_exists('min_description', $status)) { ?>
                    data-container="body" data-toggle="popover" data-trigger="hover" 
                        data-placement="right" data-content="<?php echo $status['description']; ?>"><?php 
                    echo $status['min_description'];
                } else { 
                    echo '>'.$status['description'];
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