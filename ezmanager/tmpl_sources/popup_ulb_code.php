<!-- 
This is the popup displaying the ULB code for the video
You should not have to use this file on your own; if you do, make sure the variables $ulb_code are defined
-->
           

<div class="popup" id="popup_ulb_code" style="width: 400px;">
    <h2>®ULBcode®</h2>
    ®ULBcode_message® <br/><br/>
    <strong><?php echo $ulb_code; ?></strong><br/><br/>
    
    <div id="wrapper_clip" style="position:relative">
        <span id="copy_button" class="Bouton"><a><span id="copy_button_text">®Copy_to_clipboard®</span></a></span>
        <div class="clipboard" id="zero_clipboard" data-clipboard-text="<?php echo $ulb_code; ?>" onmouseout="getElementById('copy_button_text').style.color='#797676'" onmouseover="getElementById('copy_button_text').style.color='#004B93'" style="position:absolute; left:100px; top:0px; width:200px; height:30px; z-index:99"></div>
    </div>
</div>


        
