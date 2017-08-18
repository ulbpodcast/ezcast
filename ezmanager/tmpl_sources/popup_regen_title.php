
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Regen_Intro® "<?php echo htmlspecialchars($title); ?>" ?</h4>
</div>
<div class="modal-body">
    <center>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#regen_title'));}, 500);"
            href="index.php?action=regen_title&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>" 
            data-dismiss="modal" id="regen_title">
            ®OK®
        </a>
    </center>
</div>