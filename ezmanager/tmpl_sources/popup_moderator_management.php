<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Moderator_manage®</h4> 
</div>
<div class="modal-body">
    <div id="div_ezplayer_url">
        <h4 class="text-center">®sharemyalbum® </h4><br>
        <div class="BlocPodcastMenu">
            
            ®Manager_URL_message® <br/><br/>

            <textarea readonly="" class="form-control" onclick="this.select()"
                    id="share_time_link"><?php echo trim($manager_full_url); ?></textarea>
            <br />
            <div class="wrapper_clip" style="position:relative; text-align: center;">
                <span id="share_time" onclick="copy_video_url();" class="btn btn-default">
                    <span id="share_valid" style="display: none">✔</span>
                    ®Copy_to_clipboard®
                </span>
            </div>

        </div>
    </div>
    <div class="row">
        
        <br />
        <div class="col-md-12">
            <h4 class="text-center">®Moderator_List®</h4><br>
            <table class="table table-hover text-left" >
                <?php for ($i=0; $i < count($tbusercourse); $i++) {
    $userId = $tbusercourse[$i]['user_ID'];
    echo '<tr>';
    echo '<td>';
    echo $userId;
    echo '</td>';
    if (count($tbusercourse) != 1) { // avoid suppression of the last admin
        echo '<td>'; ?>
                                <a class="btn-xs btn btn-danger delete_user_course pointer" id="delete_user_course_<?php echo $userId; ?>"
                                    onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#delete_user_course_<?php echo $userId; ?>'));
                                        $('#modal').modal('show'); }, 500);"
                                    href="index.php?action=show_popup&amp;popup=moderator_delete&amp;album=<?php echo $album; ?>&amp;id_user=<?php echo $tbusercourse[$i]['user_ID']; ?>" 
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