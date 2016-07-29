<?php if(isset($pagination)) {
    $pagination->insert();
} ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered events sort_col">
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
                <td style="text-align: center;">
                    <button type="button" class="btn btn-default btn-sm" id="check" 
                            data-toggle="modal" data-target="#modal_check" data-asset="<?php echo $status['asset']; ?>">
                        <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
                    </button>
                    <?php if(!array_key_exists($status['asset'], $listChildren)) { ?>
                    <button type="button" class="btn btn-default btn-sm" id="link"
                            data-toggle="modal" data-target="#modal_link" data-asset="<?php echo $status['asset']; ?>">
                        <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    </button>
                    <?php } ?>
                </td>
                <td style="text-align: left;">
                    <a href="./index.php?action=view_track_asset&post=&startDate=0&view_all=on&asset=<?php echo $status['asset']; ?>">
                        <?php echo $status['asset']; ?>
                    </a>
                    <a style="float: right;" href="./index.php?action=view_events&post=&startDate=0&asset=<?php echo $status['asset']; ?>">
                        <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>
                    </a>
                    <?php if(array_key_exists($status['asset'], $listChildren)) {
                        foreach($listChildren[$status['asset']] as $children) {
                            echo '<br />';
                            echo '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ';
                            echo '<a href="./index.php?action=view_track_asset&post=&startDate=0&view_all=on&asset='.
                                    $children.'">';
                            echo $children . '  ';
                            echo '<a href="./index.php?action=view_track_asset&post=&startDate=0&view_all=on&asset='.
                                    $children.'">';
                            echo '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
                            echo '</a>';
                        }
                    } ?>
                </td>
                <td><?php echo $status['status_time']; ?></td>
                <td><?php echo $status['author']; ?></td>
                <td><span class="label label-<?php echo EventStatus::getColorStatus($status['status']); ?>">
                        <?php echo $status['status']; ?>
                    </span>
                </td>
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
    
    <!-- Link an asset to an other -->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLink" id="modal_link">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="®monit_help_status_close®">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">®monit_parent_define®</h4>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="new_parent" value="true">
                        <input id="asset_name" type="hidden" name="current_asset" value="">

                        <div class="form-group">
                            <label for="parent_asset" class="control-label">®monit_parent_asset®:</label>
                            <input type="text" class="form-control" id="parent_asset" placeholder="Asset" name="parent_asset">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">®monit_help_status_close®</button>
                        <button type="submit" class="btn btn-success">®edit®</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <!-- Check an asset -->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCheck" id="modal_check">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="®monit_help_status_close®">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">®monit_status_process®</h4>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="new_parent" value="true">
                        <input id="asset_name" type="hidden" name="current_asset" value="">

                        <div class="form-group">
                            <label for="parent_asset" class="control-label">®monit_parent_asset®:</label>
                            <input type="text" class="form-control" id="parent_asset" placeholder="Asset" name="parent_asset">
                        </div>
                        
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder="®monit_message®"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">®monit_help_status_close®</button>
                        <button type="submit" class="btn btn-success">®edit®</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    
    
</div>


<script> 
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
    
    $("button[data-toggle='modal']#link").on('click', function(e) {
        var button = $(this); //clicked object
        var assetName = button.attr('data-asset'); //modal element id 
        var modal = $('#modal_link');
        modal.find('.modal-body input#asset_name').val(assetName);
    });
    
    $("button[data-toggle='modal']#check").on('click', function(e) {
        var button = $(this); //clicked object
        var assetName = button.attr('data-asset'); //modal element id 
        var modal = $('#modal_check');
        modal.find('.modal-body input#asset_name').val(assetName);
    });
    

}); 

</script>