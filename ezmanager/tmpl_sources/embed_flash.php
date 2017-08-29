<!--
This template displays a nice Flash/HTML5 player playing the video passed in argument.
If you want to use this template, make sure $media_url, $width and $height are defined.
Also, make sure the file swf/bugatti.swf exists in your web root.
-->
        
        <div id="flashContent">
            <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="<?php echo $width; ?>" 
                    height="<?php echo $height; ?>" id="bugatti" align="middle">
                <param name="movie" value="<?php echo $player_url; ?>?url=<?php echo $media_url; ?>" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#000000" />
                <param name="play" value="true" />
                <param name="loop" value="true" />
                <param name="wmode" value="window" />

                <param name="scale" value="showall" />
                <param name="menu" value="true" />
                <param name="devicefont" value="false" />
                <param name="salign" value="" />
                <param name="allowScriptAccess" value="always" />
                <param name="allowFullScreen" value="true" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="<?php echo $player_url; ?>?url=<?php echo $media_url; ?>" 
                        width="<?php echo $width; ?>" height="<?php echo $height; ?>">
                <param name="movie" value="<?php echo $player_url; ?>?url=<?php echo $media_url; ?>" />

                <param name="quality" value="high" />
                <param name="bgcolor" value="#000000" />
                <param name="play" value="true" />
                <param name="loop" value="true" />
                <param name="wmode" value="window" />
                <param name="scale" value="showall" />
                <param name="menu" value="true" />
                <param name="devicefont" value="false" />
                <param name="salign" value="" />

                <param name="allowScriptAccess" value="always" />
                <param name="allowFullScreen" value="true" />
                <param name="movie" value="<?php echo $player_url; ?>?url=<?php echo $media_url; ?>" />

                <param name="quality" value="high" />
                <param name="bgcolor" value="#000000" />
                <param name="play" value="true" />
                <param name="loop" value="true" />
                <param name="wmode" value="window" />
                <param name="scale" value="showall" />
                <param name="menu" value="true" />
                <param name="devicefont" value="false" />
                <param name="salign" value="" />

                <param name="allowScriptAccess" value="always" />
                <param name="allowFullScreen" value="true" />
            <!--<![endif]-->
                    <a href="http://www.adobe.com/go/getflash">
                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" 
                             alt="Obtenir Adobe Flash Player" />
                    </a>
            <!--[if !IE]>-->
            </object>
            <!--<![endif]-->

            </object>
    </div>
</body>
</html>
