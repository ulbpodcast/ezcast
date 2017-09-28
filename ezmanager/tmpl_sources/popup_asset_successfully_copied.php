<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®asset_copy_success®</h4>
</div>
<div class="modal-footer copy-success">
    <a type="button" class="btn btn-default" href="index.php">®Close_and_return_to_index®</a>
</div>

<script>
$('#modal').on('hide.bs.modal', function () {
    if($('#modal .modal-footer.copy-success').length) {
        $(location).attr('href', 'index.php');
    }
})
</script>
