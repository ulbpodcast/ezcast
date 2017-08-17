<div id="div_ezplayer_url">
    <div class="BlocPodcastMenu">
        <?php 
        if(!$public_album) {
            echo '<br />';
            echo "<div class=\"alert alert-danger text-center\" role=\"alert\">";
                echo "®Manager_url_private_alert®";
            echo "</div>";
        }
        ?>
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