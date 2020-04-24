<?php
require_once 'config.inc';
?>

<?php 
	
	if(empty($error))
	{
		echo "<br>";
		echo '<div class="page_title"><h3>'.$room_name.'</h3></div>';
		$url = $path_url_proxy.'?room='.$room.'&action_cam=displayImgOptions&room_ip='.$classroom[0].'';

	    	echo "<div>";
	        echo '<img class="center-block" id="img" src= "'.$url.'" width="640" height="360" alt="error: no preview avaible" />';
	        echo'
	        <br> <br>
	        <div class="row"> 
	        	<div class="col-sm-6">
     				<form method="POST" action="cli_get_camera.php" target="_blank"> 
			        	<input type="hidden" name="room" value="'.$room.'"/>
			        	<input type="hidden" name="action_cam" value="displayImgOptions"/> 
			        	<input type="hidden" name="room_ip" value="'.$classroom[0].'"/>
			        	<input type="hidden" name="param" value="default"/> 
			        	<button type="submit" class="btn btn-primary" style="float: right;">
			        		Contrôle avancé <span class="glyphicon glyphicon-wrench"></span>
			        	</button>  
			        </form>
    			</div>
    			<div class="col-sm-6">
     				<a href="http://'.$classroom[0].'/ezrecorder" target="_blank" class="btn btn-primary">
     					EZrecorder <span class="glyphicon glyphicon-facetime-video"></span>
     				</a>
    			</div>
    		</div>
		    <br><br>';
	        echo'            
	        <table style="margin: 0 auto;">
	            <tr>
	                <td></td>
	                <td>
	                    <a href="#" id="'.$url.'&param=top" class="command"> <span class="glyphicon glyphicon-triangle-top controlButton"></span></a>
	                </td>
	            </tr>
	            <tr>
	                <td>
	                    <a href="#" id="'.$url.'&param=left" class="command"> <span class="glyphicon glyphicon-triangle-left controlButton"></span></a>
	                </td>
	                <td></td>
	                <td>
	                    <a href="#" id="'.$url.'&param=right" class="command"> <span class="glyphicon glyphicon-triangle-right controlButton"></span></a>
	                </td>                    
	                <td class="col-md-2"></td>
	                 <td >
	                    <a href="#" id="'.$url.'&param=minus" class="command"> <span class="glyphicon glyphicon-minus controlButton"></span> </a>
	                </td>
	                <td style="padding-left: 60px;">
	                    <a href="#" id="'.$url.'&param=plus" class="command"> <span class="glyphicon glyphicon-plus controlButton"></span></a>
	                   <!-- <a href="#" id="'.$ipcommand.':8080/Set?Func=Zoom&Kind=0&ZoomMode=4" class="command">   <span class="glyphicon glyphicon-plus controlButton"></span></a> -->
	                </td>
	            </tr><tr>
	                <td></td>
	                <td>
	                    <a href="#" id="'.$url.'&param=bottom" class="command"> <span class="glyphicon glyphicon-triangle-bottom controlButton"></span></a>
	                </td>
	            </tr>
	        </table>
	        <br>            
	        <table style="margin: 0 auto;">
	            <tr>
	                <td style="padding-right:15px;"> 
	                    <a href="#" id="'.$url.'&param=preset1" class="command btn btn-primary"> preset 1 </a>
	                </td>
	                <td>
	                    <a href="#" id="'.$url.'&param=preset2" class="command btn btn-primary"> preset 2 </a><br>
	                </td>
	            </tr>
	        </table>            
	        <br> <br>            
            <div class="well">
                Status: '.getRecordingStatus($classroom[0]).'
                <br>';
        		$poucentage = str_replace("%", "", getSpaceUsed($classroom[0]));
	                if($poucentage >= 0 && $poucentage <= 50)
	                	$color_pourcentage = "label-success";
	                elseif ($poucentage > 50 && $poucentage <= 75)
	                		$color_pourcentage = "label-warning";
	                elseif ($poucentage > 75 && $poucentage <= 100)
	                		$color_pourcentage = "label-danger";

             echo '<span class="label '.$color_pourcentage.'" style="font-size:100%;">Espace disque utilisé : '.$poucentage.'%</span>
            </div>' ; 
	        /*<div class="">
	            <h4>Flux RTSP</h4> <div class="well">rtsp://admincam:b7N_rYg6@'.$ip.':554/Src/MediaInput/stream_2</div>
	        </div>*/        
	   echo '</div>

	    <script type="text/javascript">
	    	$(".controlButton").css("font-size", "30px");

	        $(".command").on("click", function(e)
	        {
	            makeaction($(this));
	        });            
	        function makeaction($this)
	        {
				$.ajax({
	                url : "cli_get_camera.php", 
	                type : "POST",
	                data : $this.attr("id").split("?")[1],
	                success :  function(data)
		            {
		            	console.log("ok");
		            },
		            error : function(resultat, statut, erreur)
		            {
		            	console.log("erreur");
					}		
	            });   
	        }

	        $(window).load(function(){
	            var url;
	            var urlFinal;
	            setInterval(function()
	            {
	                $("#img").each(function()
	                {
	                    url = $(this).attr("src").split("?");
	                    params = url[1].split("&");
	                    urlFinal = url[0]+"?";
	                    for (var i = 0; i < params.length ; i++)
	                    {
	                        if(params[i].indexOf("=") != -1)
	                        {
	                            urlFinal += params[i]+"&";
	                        }
	                    }
	                    
	                    $(this).attr("src", urlFinal+""+new Date().getTime());
	                });
	            },1000);
	        });
	    </script>
		';
	}
	else
	{
		echo '<div class="alert alert-danger alert-dismissible fade in" role="alert"> 
		        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		            <span aria-hidden="true">×</span></button>'; 
		                echo $error;
	    echo '</div>';
	}
?>