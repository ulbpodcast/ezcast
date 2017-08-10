
<div class="popup" id="popup_thumbnails_<?php echo $asset_name; ?>">
    <h2>Vignette</h2>
    <!--<h2>asset "<?php echo htmlspecialchars($asset_name); ?>"?</h2>-->
    
    
    <?php
     global $repository_basedir;
            $directory = $repository_basedir.'/repository/'.$album.'/'.$asset_name.'/thumbnails/';            
            // echo $directory;            
            $images = glob($directory ."*.{jpg,png,gif}", GLOB_BRACE);
            echo '<div class="thumbnail_list">';
            echo'<div id="image_preview" class="thumbnail thumbnail_'.$album.'_'.$asset_name.'"';
            echo'<h2>®chosenThumb®</h2><br><img  id="imgthumb" src="index.php?action=get_asset_thumbnails&album='.$album.'&asset='.$asset_name.'&image=thumbnail.png" name="Image10" width="187" height="109"  border="2" id="Image10"> </div>';
           
           
           
            echo'<br>';
            echo'<h2>
             <form id="my_form_'.$album.'_'.$asset_name.'" method="post" action="index.php?action=asset_thumbnail&album='.$album.'&asset='.$asset_name.'&type=cam" enctype="multipart/form-data">
                <input class="hiddenfile" id="hiddenfile_'.$album.'_'.$asset_name.'" type="file" name="image" accept="image/*" style="display:none" >                  
                <span> <input type="button" value="®importThumb®" onclick="getfile(\''.$album.'_'.$asset_name.'\')" /></span>®chooseThumb®</h2>
          
            </form>';
            foreach($images as $image)
            {
                $filename=basename($image);                
                // appeler l'image a partir du serveur grace au controler get asset thumbails                
                if($filename!='thumbnail.png'){
                    echo '<a class="thumbnail_image" id="thumbnail_'.$album.'_'.$asset_name.'_'.$filename.'"><img class="imgThumb" dataImgTitle="'.$filename.'" src="index.php?action=get_asset_thumbnails&album='.$album.'&asset='.$asset_name.'&image='.$filename.'" name="Image10" width="187" height="109"  border="2" id="Image10"></a>';
                }
            }
            echo '</div>';
    ?>       
    <br>
    <div class="choice_button">
       <!--<span class="Bouton"> <a href="javascript:close_popup();"><span>Annuler</span></a></span>-->
       <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
        <span class="Bouton"> <a href="javascript:close_popup();"><span>®finish®</span></a></span>
    
    </div>
    </div>

 <?php global $repository_basedir;
        if(!file_exists ( $repository_basedir.'/repository/'.$album.'/'.$asset_name.'/thumbnails/thumbnail.png' )){ ?>
       <script>
        $('.thumbnail_<?php echo $album.'_'.$asset_name; ?>').hide();
        $('#my_form_supp_<?php echo $album.'_'.$asset_name; ?>').hide();
            
       </script>
   <?php }  ?> 

<script>
    function getfile(asset){
       document.getElementById('hiddenfile_'+asset).click();
    }
    
    
    $('#my_form_<?php echo $album.'_'.$asset_name; ?>').find(' [name="image"]').on('change', function (e) {
            var files = $(this)[0].files;
     
            if (files.length > 0) {
                // On part du principe qu'il n'y qu'un seul fichier
                // étant donné que l'on a pas renseigné l'attribut "multiple"
                var file = files[0],
                    $image_preview = $('.thumbnail_<?php echo $album.'_'.$asset_name; ?>');
     
                // Ici on injecte les informations recoltées sur le fichier pour l'utilisateur
                $image_preview.show();
                //$('#my_form_supp_<?php echo $album.'_'.$asset_name; ?>').show();
                $image_preview.find('img').attr('src', window.URL.createObjectURL(file));
                $image_preview.find('h4').html(file.name);
                $image_preview.find('.caption p:first').html(file.size +' bytes');
            }
            
            
            var $form = $('#my_form_<?php echo $album.'_'.$asset_name; ?>');
            var formdata = (window.FormData) ? new FormData($form[0]) : null;
            var data = (formdata !== null) ? formdata : $form.serialize();
     
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                data: data,
                success: function (response) {
                   alert("®thumuploadedSuccesss®");
                }
            });
            $( ".hiddenfile" ).val("");

            
            
        });
    
    
    $(".thumbnail_image").click(function() {
      //alert( $(this).find('img').attr("dataImgTitle"));
      
      //dont work..
      
      

      $.ajax({
          url: 'index.php?action=asset_thumbnail&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>&fileToChange='+$(this).find('img').attr("dataImgTitle"),
          type: 'POST',
          dataType: 'json', 
          success: function (response) {
             alert("®thumuploadedSuccesss® "+response);
          }
      });
      
      
      
    $image_preview = $('.thumbnail_<?php echo $album.'_'.$asset_name; ?>');
     
    // Ici on injecte les informations recoltées sur le fichier pour l'utilisateur
    $image_preview.show();
    //$('#my_form_supp_<?php echo $album.'_'.$asset_name; ?>').show();
    $image_preview.find('img').attr('src', $(this).find('img').attr("src"));
  
  
      
      $("#imgthumb").attr( "src", $(this).find('img').attr("src") );
      //$("#image_preview").reload;
      
      
       
        //alert( "Handler for .click() called." );
    });
    
</script>
    