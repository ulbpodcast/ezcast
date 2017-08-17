
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Regen_Intro® "<?php echo htmlspecialchars($title); ?>" ?</h4>
</div>
<div class="modal-body">
    <center>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#rss_confirm'));
                $('#modal').modal('show'); }, 500);"
            href="index.php?action=reset_rss&album=<?php echo $album; ?>" 
            data-dismiss="modal" id="rss_confirm">
            ®OK®
        </a>
    </center>
</div>


<div class="popup" id="popup_regen_title">
    <h2>®Regen_Intro® "<?php echo htmlspecialchars($title); ?>"?</h2>
    <span class="Bouton"> <a href="javascript:close_popup();"><span>®Cancel®</span></a></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="Bouton"> <a href="javascript:popup_asset_regentitle_callback('<?php echo $album; ?>', '<?php 
    echo $asset_name; ?>');"><span>®OK®</span></a></span>
</div>
