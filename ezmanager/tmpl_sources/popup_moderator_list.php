<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Moderator_List®</h4>
</div>
<div class="modal-body">
    <div class="row">
        <br />
        <div class="col-md-12">
            <table class="table table-hover text-left" >
                <?php for($i=0; $i < count($tbusercourse); $i++) {
                    echo '<tr>';
                        echo '<td>';
                            echo $tbusercourse[$i]['user_ID'];
                        echo '</td>';
                        if (TRUE || count($tbusercourse) != 1) { // avoid suppression of the last admin
                            echo '<td>';?>
                                <a class="btn-xs btn btn-danger delete_user_course pointer" id="delete_user_course"
                                    onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#delete_user_course'));
                                        $('#modal').modal('show'); }, 500);"
                                    href="index.php?action=show_popup&amp;popup=moderator_delete&amp;album=<?php echo $album; 
                                        ?>&amp;id_user=<?php echo $tbusercourse[$i]['user_ID']; ?>" 
                                    data-remote="false" data-toggle="modal" data-target="#modal" >
                                <?php 
                                    echo '<span>®Delete®</span>';
                                echo '</a>';
                            echo '</td>';
                        }
                    echo '</tr>';
                } ?>
            </table>
        </div>
    </div>
</div>