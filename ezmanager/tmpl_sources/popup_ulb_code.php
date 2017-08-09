<!-- 
This is the popup displaying the ULB code for the video
You should not have to use this file on your own; if you do, make sure the variables $ulb_code are defined
-->

<div class="modal-header">®ULBcode®</div>
<div class="modal-body">
    <p>®ULBcode_message®</p>

    <textarea readonly="" class="form-control" onclick="this.select()" id="share_time_link">
        <?php echo $ulb_code; ?>
    </textarea><br />
    <div class="wrapper_clip" style="position:relative; text-align: center;">
        <span id="share_time" onclick="copy_video_url();" class="copy-to-clipboard-button">
            <span id="share_valid" style="display: none">✔</span>
            ®Copy_to_clipboard®
        </span>
    </div>
</div>
