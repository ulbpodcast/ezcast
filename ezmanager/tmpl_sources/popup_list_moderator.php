
<div class="popup" id="popup_list_moderator" style="width: 415px; height: 300px;">
    <h2>®Moderator_List®</h2>
	
	<div>
		<table class="table">
			<?php for($i=0;$i<count($tbusercourse);$i++){ ?>
			<tr>
				<td>
					<?php echo $tbusercourse[$i]['user_ID'] ?>
				</td>
				<?php if(count($tbusercourse)!=1) {   // avoid suppression of the last admin?>
				<td>
					<a class='button delete_user_course pointer' id='delete_<?php echo $album.'_'.$tbusercourse[$i]['user_ID']?>'> <span>®Delete®</span></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
    </div>
</div>


<div class="popup" id="popup_delete_moderator" style="display: none;height: 300px">
    <h2>®Delete_Modo®<span id="id_moderator"><span>?</h2>
    <span class="warning">®Destructive_operation®<br/><br/></span>
    <div>®Delete_modo_message®</div><br/>
    
    <span class="Bouton"> <a href="?action=view_help" target="_blank"><span>®Help®</span></a></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="Bouton"> <a href="javascript:close_popup();"><span>®Cancel®</span></a></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<span class="Bouton pointer" id="valid_button"> <span>®OK®</span></a></span>
</div>

<script >

$( ".delete_user_course" ).click(function(id) {
   $('#popup_list_moderator').hide();
   $('#popup_delete_moderator').css("display", "block");
   
   var result=(this.id).split('_');
   var album = result[1];
   var iduser = result[2];
   $('#id_moderator').text(iduser);
   
   $( "#valid_button" ).click(function() {
	   show_popup_from_outer_div('index.php?action=delete_user_course&album='+album+'&iduser=' + iduser, true);	   
   });
});

$('.pointer').hover(function() {
	$(this).css('cursor','pointer');
});

</script>
