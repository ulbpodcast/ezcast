
<?php
require_once 'config.inc';
?>
    <?php
        if(!empty($error))
        {
            echo '<div class="alert alert-danger alert-dismissible fade in" role="alert"> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span></button>'; 
                        echo $error;
            echo '</div>';
        }
        else
        {
    ?>
        <div class="page_title">®list_cam®</div>
    	<div class="container chargement" id="div_progress_bar" style="width: 50%">
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
        <div class="chargement" id="images" style="display: none;">
        <?php

            echo '<div class="row">';

            $i = 0;
            $id_form = 0;
            foreach ($listClassrooms as $currClass)
            {
                echo' 
                 <div class="col-sm-4">';
                    $src = "";
                    $alt = "";
                    $value_connexion = test_connexion_url($currClass['IP']);
                    if($value_connexion)
                    {
                        $src = $path_url_proxy.'?room='.$currClass['room_ID'].'&action_cam=displayImg&room_ip='.$currClass['IP'].'';
                        $alt = $currClass['name'];
                        if($enable_control_panel_options)
                        {
                            echo '<form action="index.php?action=view_camera" method="POST" id="form_camera_'.$id_form.'">
                            <input type="hidden" name="room" value="'.$currClass['room_ID'].'">
                            <input type="hidden" name="room_name" value="'.$currClass['name'].'">';
                            ?>
                                <a href="javascript:document.getElementById('form_camera_<?php echo $id_form; ?>').submit();" >
                            <?php
                        }
                    }
                    else
                    {
                        $src = 'img/Image_n_a.png';
                        $alt = "error";
                    }
                    echo '<img class="img-thumbnail" id="img'.$currClass['room_ID'].'" src="'.$src.'" alt="'.$alt.'">';
                    if((getRecordingStatus($currClass['IP']))=="recording")
                        echo'<img alt="'.$currClass['name'].'" style="position: absolute; left: 15px; top: 0px; z-index: 999; width: 353px;" src="rec.png"/>';
                            
                    echo'</img></a>
                    <div class="well">
                        <b>';
                            $text = $currClass['name'].' ('.getRecordingStatus($currClass['IP']).')';
                            if(strlen($text) > 49)
                            {
                                $text = substr($text, 0, 45)." ...";
                            }

                        echo $text.'</b><br>'; 
                            $poucentage = str_replace("%", "", getSpaceUsed($currClass['IP']));
                            if($poucentage >= 0 && $poucentage <= 50)
                                $color_pourcentage = "label-success";
                            elseif ($poucentage > 50 && $poucentage <= 75)
                                    $color_pourcentage = "label-warning";
                            elseif ($poucentage > 75 && $poucentage <= 100)
                                    $color_pourcentage = "label-danger";
                    echo ' <span class="label '.$color_pourcentage.'" style="font-size:100%;">Espace disque utilisé : '.$poucentage.'%</span>
                    </div>
                </div>
                ';
                $i++;

                if($i % 3 == 0)
                {
                    $i = 0;
                    echo '</div><div class="row">';
                }
                if($value_connexion)
                {
                    echo "</form>";
                    $id_form++;
                }
            }
        ?>
        </div>
        </div>
        <script type="text/javascript">
            document.onreadystatechange = function ()
            {
                if (document.readyState == "interactive")
                {
                    progressbar("page", ".chargement", "Chargement de la page");
                }
                else if(document.readyState == "complete")
                {
                    progressbar("image", ".img-thumbnail", "Chargement des images");
                }
            }

            function progressbar(type, classe, text)
            {
                timer(0, 100 / $(classe).length, type);
                $(".progress-bar").text(text);
            }

            function timer(n, value, type)
            {
                $(".progress-bar").css("width", n + "%");

                if(n < 100)
                {
                    setTimeout(function() {
                        timer(n + value, value, type);
                    }, 200);
                }
                
                if(n >= 100 && type == "image")
                {
                    $("#div_progress_bar").delay(1000).fadeOut(300);
                    $("#images").delay(1000).fadeIn(300);
                }
            }

            $(window).load(function(){
                var url;
                var params;
                var urlFinal;

                setInterval(function()
                {
                    $(".img-thumbnail").each(function()
                    {
                        if($(this).attr("alt") != "error")
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
                        }
                    });

                }, 2000);
            });
        </script>
    <?php
        }
    ?>