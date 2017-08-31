<!-- 
DEPRECATED

Pops up for resetting RSS feed
You should not have to include this file yourself.
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Reset_broadcast_link®</h4>
</div>
<div class="modal-body">
    <div class="alert alert-warning text-center" role="alert">®Non_reversible_operation®</div>
    <p>®Reset_broadcast_message®</p><br />
    <center>
        <a class="btn btn-info" target="_blank" href="?action=view_help" role="button">®Help®</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#rss_confirm'));$('#modal').modal('show'); }, 500);"
            href="index.php?action=reset_rss&album=<?php echo $album; ?>" 
            data-dismiss="modal" id="rss_confirm">
            ®OK®
        </a>
    </center>
</div>
