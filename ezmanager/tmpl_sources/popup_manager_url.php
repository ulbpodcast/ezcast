<div class="popup" id="manager_url_box" style="width:600px;">
    <h2>®Ezmanager_url®</h2>
    ®Manager_URL_message®<br/><br/>
    <strong><a href="<?php echo $manager_full_url; ?>" target="_blank"><?php echo $manager_full_url; ?></a><br/><br/></strong>
        
    <!-- Copy to clipboard button 
    All browsers use flash + javascript except Internet explorer which has an access to the clipboard -->
    
    <!--[if !IE]><!-->
    <div id="wrapper_clip" style="position:relative">
        <span id="copy_manager_url" class="Bouton"><a><span id="copy_button_text_manager_url">®Copy_to_clipboard®</span></a></span>
        <div id="zero_clipboard_manager_url" onmouseout="getElementById('copy_button_text_manager_url').style.color='#797676'" onmouseover="getElementById('copy_button_text_manager_url').style.color='#004B93'" style="position:absolute; left:200px; top:0px; width:200px; height:30px; z-index:99"></div>
    </div>
    <!--<![endif]-->  
    
    <!--[if IE]>
    <span id="copy_button_manager_url" class="Bouton"><a href="#" onclick="window.clipboardData.setData('Text','<?php echo $manager_full_url; ?>');"><span>Copier dans le presse-papier</span></a></span>
    <![endif]-->
</div>
