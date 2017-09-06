

<!--
This popup appears when the user clicks on "copy this record".
It presents the user with a list of albums he can copy the asset to.

You should not have to use this template on its own. however, if you do, please
make sure $created_albums_with_descriptions is initialized and is an array containing the album names (without any suffix) as keys, and albums descriptions as values
for every album the user can create.
-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Copy®</h4>
</div>
<div class="modal-body">
    <?php if (empty($created_albums_list_with_descriptions)) {
    ?>
        ®No_albums_to_copy_asset_to®
    <?php
} else {
        ?>
        <p>®Copy_asset_message®</p>
        
        <div class="row">
            <br />
            <div class="col-md-12">
                <table class="table table-hover text-left" >
                    <?php foreach ($created_albums_list_with_descriptions as $destination_name => $destination_description) {
            // get course name from private or public album, should be the same
            $course_code_public = ezmam_album_course_public_name_get($destination_name."-pub");
                        
            if ($album != $destination_name.'-priv') {
                echo '<tr>';
                echo '<td class="album_name col-md-4" style="font-weight: bold;">';
                echo '<a href="index.php?action=copy_asset&from='.$album.'&to='.
                                            $destination_name.'-priv'.'&asset='.$asset_name.'" ' .
                                            'onClick=\'setTimeout(function(){ display_bootstrap_modal($("#modal"), '.
                                                '$("#copy_asset_'.$destination_name.'_priv"));$("#modal").modal("show"); }, 500);\' ' .
                                            'data-dismiss="modal" id="copy_asset_'.$destination_name.'_priv" >';
                echo $course_code_public . ' (®private®)';
                echo '</a>';
                echo '</td>';
                echo '<td class="album_description">';
                echo $destination_description . ' (®Private_album®)';
                echo '</td>';
                echo '</tr>';
            }
            if ($album != $destination_name.'-pub') {
                echo '<tr>';
                echo '<td class="album_name col-md-2" style="font-weight: bold;">';
                echo '<a href="index.php?action=copy_asset&from='.$album.'&to='.
                                            $destination_name.'-pub'.'&asset='.$asset_name.'" ' .
                                            'onClick=\'setTimeout(function(){ display_bootstrap_modal($("#modal"), '.
                                                '$("#copy_asset_'.$destination_name.'_pub"));$("#modal").modal("show"); }, 500);\' ' .
                                            'data-dismiss="modal" id="copy_asset_'.$destination_name.'_pub" >';
                echo $course_code_public . ' (®public®)';
                echo '</a>';
                echo '</td>';
                echo '<td class="album_description">';
                echo $destination_description . ' (®Public_album®)';
                echo '</td>';
                echo '</tr>';
            }
        } ?>
                </table>
            </div>
        </div>        
    <?php
    } ?>
</div>