<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Stats_Reset®</h4>
</div>
<div class="modal-body">
    ®Stats_successfully_reset_message®
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >
        ®Close_and_return_to_index®
    </button>
</div>
<script>
$('#modal').on('hide.bs.modal', function (e) {
    refresh_album_view();
});
</script>