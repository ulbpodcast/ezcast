<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Delete_Modo® <?php echo $id_user; ?> ?</h4>
</div>

<div class="modal-body">
    <div class="alert alert-warning text-center" role="alert">®Destructive_operation®</div>
    <p>®Delete_modo_message®</p><br />
    <center>
        <a class="btn btn-info" target="_blank" href="?action=view_help" role="button">®Help®</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
        <a class="btn btn-default" onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#delete_modo_course'));
                $('#modal').modal('show'); }, 500);"
            href="index.php?action=delete_user_course&album=<?php echo $album; ?>&iduser=<?php echo $id_user; ?>" 
            data-dismiss="modal" id="delete_modo_course">
            ®OK®
        </a>
    </center>
</div>