<?php 
/*
* EZCAST EZmanager
*
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
* UI Design by Julien Di Pietrantonio
*
* This software is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

<!--
This template is not supposed to be used by itself.
It is part of div_asset_details and has been split apart for readability.
-->
<h1>®Slides®</h1>
<div class="Video-slide-Bouton" id="Player_<?php echo $asset; ?>_slide"> 
    <a href="javascript:show_embed_player('<?php echo $album; ?>', '<?php echo $asset; ?>', 'low', 'slide', '<?php 
        echo $asset_token; ?>');" 
       onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image11','','images/page4/BRegarderSlide.png',1)">
        <img src="images/page4/ARegarderSlide.png" name="Image11" width="187" title="®Watch_slides®" height="109" 
             border="0" id="Image11" />
    </a> 
</div>
<span class="ButtonPlus"> 
    <a href="javascript:visibilite('InfoAvance_<?php echo $asset; ?>_2');">®Advanced_info®</a> 
</span>
<div id="InfoAvance_<?php echo $asset; ?>_2" style="display:none">
    <p><span class="infosAvance">®Filesize_HD®&nbsp;:</span> <?php print_info($filesize_slides['HD'], ' ®Megabyte_unit®'); ?></p>
    <p><span class="infosAvance">®Filesize_SD®&nbsp;:</span> <?php print_info($filesize_slides['SD'], ' ®Megabyte_unit®'); ?></p>
    <p><span class="infosAvance">®Dimensions_HD®&nbsp;:</span> <?php print_info($dimensions_slides['HD']); ?></p>
    <p><span class="infosAvance">®Dimensions_SD®&nbsp;:</span> <?php print_info($dimensions_slides['SD']); ?></p>
    <!--<p><span class="infosAvance">®View_count®&nbsp;:</span> <?php // echo $view_count_slides;?></p> -->
</div>
        
<span class="ButtonPublication"> 
    <a href="javascript:visibilite('Publication_<?php echo $asset; ?>_2');">®Publication®</a>
</span>

<!--div class="Publication" id="Publication_<?php echo $asset; ?>_2" style="display:none">
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php //echo $asset;?>&amp;media=high_slide');">+ ®high_res®</a>
    </p>
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=low_slide');">+ ®low_res®</a>
    </p>
</div>

<span class="ButtonEmbed"> <a href="javascript:visibilite('Embed_<?php // echo $asset;?>_2');">®Embed®</a> </span>

<div class="Embed" id="Embed_<?php echo $asset; ?>_2" style="display:none">
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=high_slide');">+ ®high_res®</a></p>
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=low_slide');">+ ®low_res®</a></p>
</div -->
        

<div class="Publication" id="Publication_<?php echo $asset; ?>_2" style="display:none;">
    <span class="ButtonDownload"> <a href="javascript:visibilite('Download_<?php echo $asset; ?>_2');">®Download®</a> </span>
    <div class="Download" id="Download_<?php echo $asset; ?>_2" style="display:none;">
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_slide"
            data-remote="false" data-toggle="modal" data-target="#modal" >
                ®high_res®
            </a>
        </p>
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
                echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_slide" data-remote="false" 
                data-toggle="modal" data-target="#modal" >
                ®low_res®
            </a>
        </p>
    </div>
    
    <!-- Links to embed player -->
    <span class="ButtonEmbed"> <a href="javascript:visibilite('Embed_<?php echo $asset; ?>_2');">®Embed®</a> </span>

    <div class="Embed" id="Embed_<?php echo $asset; ?>_2" style="display:none;">
        <p><a class="greyLink" href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_slide');">®high_res®</a></p>
        <p><a class="greyLink" href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_slide');">®low_res®</a></p>
    </div>

    <span class="ButtonEZplayer"> 
        <a href="index.php?action=show_popup&amp;popup=ezplayer_link&amp;album=<?php echo $album; ?>&amp;asset=<?php echo $asset; ?>" 
           data-remote="false" data-toggle="modal" data-target="#modal">
            EZplayer
        </a>
    </span>

    <span class="ButtonULBCode"> <a href="javascript:visibilite('ULBcode_<?php echo $asset; ?>_2');">®ULBcode®</a> </span>
    <div class="ULBcode" id="ULBcode_<?php echo $asset; ?>_2" style="display:none;">
        <p><a class="greyLink" href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=ulb_code&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_slide');">®high_res®</a>
        </p>
        <p><a class="greyLink" href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=ulb_code&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_slide');">®low_res®</a>
        </p>
    </div>
</div>
