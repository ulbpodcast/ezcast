
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Stats_Reset®</h4>
</div>
<div class="modal-body">
    <div class="alert alert-warning text-center" role="alert">®Non_reversible_operation®</div>
    <p>®Stats_Reset_message®</p><br />
    <center>
        <a class="btn btn-info" target="_blank" href="?action=view_help" role="button">®Help®</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), 
                    $('#reset_stats_confirm'));$('#modal').modal('show'); }, 500);"
            href="index.php?action=album_stats_reset&album=<?php echo $album; ?>" 
            data-dismiss="modal" id="reset_stats_confirm">
            ®OK®
        </a>
    </center>
</div>