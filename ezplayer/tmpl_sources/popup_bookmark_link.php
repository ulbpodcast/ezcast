<?php
$bookmark_link = $ezplayer_url . '/index.php?action=view_asset_bookmark'
        . '&album=' . $album 
        . '&asset=' . $bookmark['asset'] 
        . '&t=' . $bookmark['timecode'];
?>

<div id="popup_bookmark_<?php echo $index ?>" class="reveal-modal left">
    <h2><?php print_bookmark_title($bookmark['title']); ?></h2>
    <br/><p>®Bookmark_link_message®</p>
    <br/><p style="text-align:center;"><a href="<?php echo $bookmark_link ?>"><?php echo $bookmark_link ?></a></p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    
    <!--[if !IE]><!-->
    <div class="wrapper_clip" style="position:relative; text-align: center;">
        <span id="copy_button_<?php echo $index ?>" class="copy-to-clipboard-button">®Copy_to_clipboard®</span>
        <div id="zero_clipboard_<?php echo $index ?>" 
             onmouseout="getElementById('copy_button_<?php echo $index ?>').style.background='#333333'" 
             onmouseover="getElementById('copy_button_<?php echo $index ?>').style.background='#11acea'" 
             style="position:absolute; left:160px; top:0px; width:200px; height:30px; z-index:105; cursor: pointer;">
        </div>
    </div>
    <!--<![endif]-->  
    
    <!--[if IE]>
    <span id="copy_button_<?php echo $index ?>" class="Bouton"><a href="#" onclick="window.clipboardData.setData('Text','<?php echo $bookmark_link ?>');"><span>®Copy_to_clipboard®</span></a></span>
    <![endif]-->
</div>

<script>
    copyToClipboard('#zero_clipboard_<?php echo $index ?>', '<?php echo $bookmark_link ?>');
</script>