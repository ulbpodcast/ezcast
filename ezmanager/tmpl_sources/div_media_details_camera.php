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
<h1>®Video®</h1>
<!-- Player and video thumbnail -->
<div class="Video-slide-Button" id="Player_<?php echo $asset; ?>_cam"> 
    <a href="javascript:show_embed_player('<?php echo $album . "', '" . $asset; ?>', 'low', 'cam', '<?php echo $asset_token; ?>');" 
       onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image10','','images/page4/BRegarderVideo.png',1)">
        <img src="images/page4/ARegarderVideo.png" name="Image10" width="187" height="109" title="®Watch_video®" border="0" id="Image10" />
    </a>
</div>
<!-- Advanced info -->
<span class="ButtonPlus"> <a href="javascript:visibilite('InfoAvance_<?php echo $asset; ?>_1');">®Advanced_info®</a> </span>
<div id="InfoAvance_<?php echo $asset; ?>_1" style="display:none">
    <p><span class="infosAvance">®Filesize_HD®&nbsp;:</span> <?php print_info($filesize_cam['HD'], ' ®Megabyte_unit®'); ?></p>
    <p><span class="infosAvance">®Filesize_SD®&nbsp;:</span> <?php print_info($filesize_cam['SD'], ' ®Megabyte_unit®'); ?></p>
    <p><span class="infosAvance">®Dimensions_HD®&nbsp;:</span> <?php print_info($dimensions_cam['HD']); ?></p>
    <p><span class="infosAvance">®Dimensions_SD®&nbsp;:</span> <?php print_info($dimensions_cam['SD']); ?></p>
    <!--  <p><span class="infosAvance">®View_count®&nbsp;:</span> <?php //echo $view_count_cam;?></p> -->
</div>
<!-- Links to media -->
<span class="ButtonPublication"> <a href="javascript:visibilite('Publication_<?php echo $asset; ?>_1');">®Publication®</a></span>


<!--div class="Publication" id="Publication_<?php // echo $asset;?>_1" style="display:none">
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=high_cam');">+ ®high_res®</a></p>
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=low_cam');">+ ®low_res®</a></p>
</div-->

<!-- Links to embed player -->
<!--span class="ButtonEmbed"> <a href="javascript:visibilite('Embed_<?php // echo $asset;?>_1');">®Embed®</a> </span>

<div class="Embed" id="Embed_<?php // echo $asset;?>_1" style="display:none">
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=high_cam');">+ ®high_res®</a>
    </p>
    <p><a href="javascript:show_popup_from_outer_div('index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
        // echo $album;?>&amp;asset=<?php // echo $asset;?>&amp;media=low_cam');">+ ®low_res®</a>
    </p>

</div-->

<div class="Publication" id="Publication_<?php echo $asset; ?>_1" style="display:none;">
    <span class="ButtonDownload"> <a href="javascript:visibilite('Download_<?php echo $asset; ?>_1');">®Download®</a> </span>
    <div class="Download" id="Download_<?php echo $asset; ?>_1" style="display:none;">
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_cam"
            data-remote="false" data-toggle="modal" data-target="#modal" >
                ®high_res®
            </a>
        </p>
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=media_url&amp;album=<?php 
                echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_cam" data-remote="false" 
                data-toggle="modal" data-target="#modal" >
                ®low_res®
            </a>
        </p>
    </div>
    <!-- Links to embed player -->
    <span class="ButtonEmbed"> <a href="javascript:visibilite('Embed_<?php echo $asset; ?>_1');">®Embed®</a> </span>

    <div class="Embed" id="Embed_<?php echo $asset; ?>_1" style="display:none;">
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
                echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_cam"
                data-remote="false" data-toggle="modal" data-target="#modal" >
                ®high_res®
            </a>
        </p>
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=embed_code&amp;album=<?php 
                echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_cam"
                data-remote="false" data-toggle="modal" data-target="#modal" >
                ®low_res®
            </a>
        </p>
    </div>
    
    <span class="ButtonEZplayer"> 
        <a href="index.php?action=show_popup&amp;popup=ezplayer_link&amp;album=<?php echo $album; ?>&amp;asset=<?php echo $asset; ?>" 
           data-remote="false" data-toggle="modal" data-target="#modal">
            EZplayer
        </a>
    </span>
    
    <span class="ButtonULBCode"> <a href="javascript:visibilite('ULBcode_<?php echo $asset; ?>_1');">®ULBcode®</a> </span>
    <div class="ULBcode" id="ULBcode_<?php echo $asset; ?>_1" style="display:none;">
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=ulb_code&amp;album=<?php 
            echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=high_cam"
            data-remote="false" data-toggle="modal" data-target="#modal" >
                ®high_res®
            </a>
        </p>
        <p>
            <a class="greyLink" href="index.php?action=show_popup&amp;popup=ulb_code&amp;album=<?php 
                echo $album; ?>&amp;asset=<?php echo $asset; ?>&amp;media=low_cam"
                data-remote="false" data-toggle="modal" data-target="#modal" >
                ®low_res®
            </a>
        </p>
    </div>
</div>

