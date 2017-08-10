<?php global $repository_basedir; ?>

    <div class="backgrey section">
      <div class="container_home">
        <div class="row">
          <div class="col-md-9">
            <ul class="backgrey nav nav-pills">
              <li class="active">
                <a href="index.php?action=home">Home</a>
              </li>
              <?php
              if (isset($_SESSION['ezplayer_logged'])){ ?>
              <li class="">
                <a href="index.php?action=album_view">Mes Albums </a>
              </li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-md-3">
            <form role="form" class="float" action="index.php?action=home">
              <div class="form-group">
                <div class="input-group">
                  <input name="search" type="text" class="form-control" placeholder="Recherche">
                  <span class="input-group-btn">
                    <input class="btn btn-primary" type="submit" value="Go">
                  </span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="backgrey section grey-ezplayer">
      <div class="container_home">
        <div class="row">
          <div class="col-md-12">
           <?php
              if ($search!="" && count($assets)>0 ){ ?>
                <h1 class="text-center">Résultats pour la recherche " <?php echo $search ?> "</h1>
             <?php } else if(count($assets)==0) { ?>
                <h1 class="text-center">Aucun résultat pour la recherche " <?php echo $search ?> "</h1>
             <?php } else { ?>				
                <h1 class="text-center">Dernières vidéos</h1>
            <?php
              } ?>
          </div>
        </div>
        <div class="row">        
            <?php $z=0; for($i=$first;$i<$first+$max_video_per_page;$i++){ ?>            
                <?php if($z==0 || $z%3==0){ ?>
        </div>
        <div class="row">
                <?php } if($i<count($assets)){

                if(file_exists ( $repository_basedir.'/repository/'.$assets[$i]['cours_id'].'/'.$assets[$i]['name'].'/thumbnails/thumbnail.png' )) $imgpath='data:image/png;base64,'.base64_encode(file_get_contents($repository_basedir.'/repository/'.$assets[$i]['cours_id'].'/'.$assets[$i]['name'].'/thumbnails/thumbnail.png'));
                else $imgpath='';
                
                
                
                //limit character number to 50 for the title and description
                $assets[$i]['title'] = strlen($assets[$i]['title']) > 50 ? substr($assets[$i]['title'],0,50)."..." : $assets[$i]['title'];
                $assets[$i]['description'] = strlen($assets[$i]['description']) > 50 ? substr($assets[$i]['description'],0,50)."..." : $assets[$i]['description'];
                ?>        
            <div class="col-md-4">
                <div class="embed-responsive embed-responsive-16by9">
                <div> <a href="index.php?action=view_asset_details&album=<?php echo ($assets[$i]['cours_id']) ?>&asset=<?php echo ($assets[$i]['name']) ?>&asset_token=<?php echo ($assets[$i]['token']) ?>"><img style="position:absolute; top:25px; left:80px" src="../ezplayer/images/play.png" width="100" /><?php if($imgpath!=''){ ?><img src='<?php echo($imgpath); ?>' name="Image10" width="265" height="150" title="Regarder la vidéo" border="2" id="Image10"><?php } ?></a></div>
                        <!-- iframe avec la video embed     <div><iframe style="padding: 0; z-index: 100;" frameborder="0" scrolling="no" src="../ezmanager/distribute.php?action=embed&amp;album=<?php echo ($assets[$i]['cours_id']) ?>&amp;asset=<?php echo ($assets[$i]['name']) ?>&amp;type=cam&amp;quality=low&amp;token=<?php echo ($assets[$i]['token']) ?>&amp;width=265&amp;height=139&amp;origin=embed" width="265" height="139"></iframe></div>  -->
                </div>
                <h3><a href="index.php?action=view_asset_details&album=<?php echo ($assets[$i]['cours_id']) ?>&asset=<?php echo ($assets[$i]['name']) ?>&asset_token=<?php echo ($assets[$i]['token']) ?>"> <?php echo ($assets[$i]['title']) ?> </a></h3>
                <p><?php echo ($assets[$i]['description']) ?> </p>
            </div>        
            <?php $z++;} } ?>   
        </div>
        
      </div>
    </div>
    <div class="section backgrey">
      <div class="container_home">
        <div class="row">
          <div class="col-md-12 text-center">
            <ul class="pagination">
              <li>
              <?php if($_GET['page']>1){ ?>
                 <a href="index.php?page=<?php echo ($_GET['page']-1) ?>&search=<?php echo $search ?>">Prev</a>
              <?php }else { ?>
                <a href="#">Prev</a>
             <?php } ?>
              </li>
              
            <?php for($i=1; $i<=$nbpage; $i++){ 
                if($_GET['page']==$i){  ?>  
                    <li class="active">
                <?php } else { ?>
                    <li>
                <?php } ?>
                        <a href="index.php?page=<?php echo $i ?>&search=<?php echo $search ?>"><?php echo $i ?></a>
                    </li>
                <?php } ?>              
 
              <li>
              <?php if($_GET['page']<$nbpage){ ?>
                <a href="index.php?page=<?php echo ($_GET['page']+1) ?>&search=<?php echo $search ?>">Next</a>
              <?php }else { ?>
                <a href="#">Next</a>
             <?php } ?>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>