
<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
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
<script>
function swap(div_a, div_b) {
    document.getElementById(div_a).style.display = 'block';
    document.getElementById(div_b).style.display = 'none';
}
swap('main_step_2', 'load_step_2');

function select_option(){
   var options_list = document.getElementById('options'); 
   var SelIndex = options_list.selectedIndex; 
   var SelValue = options_list.options[SelIndex].value; 
   
   $('.renderer_option').hide();
   $('.' + SelValue).show();
}

</script>

<h4>®create_renderer_step_3®</h4>
<div id="main_step_3">

<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    <div class="alert alert-success">®ssh_connection_success®</div>
    <input type="hidden" name="renderer_step" value="3"/>
    
    <div class="control-group">
        <label for="renderer_root_path" class="control-label">®renderer_root_path®</label>
        <div class="controls">
            <input type="text" name="renderer_root_path" value="<?php echo $input['renderer_root_path']?>"/>
        </div>
    </div>

    <div class="control-group">
        <label for="renderer_php" class="control-label">®renderer_php®</label>
        <div class="controls">
            <input type="text" name="renderer_php" value="<?php echo $input['renderer_php']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_options" class="control-label">®renderer_pgm®</label>
        <div class="controls">
            <select class="selectpicker" id="options" name="renderer_options" onchange="select_option();">
            <?php foreach ($renderers_options as $option_name => $option){
                if ($option_name != 'ffmpeg_exp' || $display_ffmpeg_exp)
                    if ($option_name == $input['renderer_options']){
                echo '<option value="' . $option_name . '" selected>' . $option['description'] . '</option>';                        
                    } else {
                echo '<option value="' . $option_name . '">' . $option['description'] . '</option>';
                    }
            }
        ?>
        </select>
        </div>
    </div>
    
    <div class="renderer_option ffmpeg ffmpeg_exp">
    <div class="control-group">
        <label for="renderer_ffmpeg" class="control-label">®renderer_ffmpeg®</label>
        <div class="controls">
            <input type="text" name="renderer_ffmpeg" value="<?php echo $input['renderer_ffmpeg']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_ffprobe" class="control-label">®renderer_ffprobe®</label>
        <div class="controls">
            <input type="text" name="renderer_ffprobe" value="<?php echo $input['renderer_ffprobe']?>"/>
        </div>
    </div>
    </div>
    
    <div class="renderer_option avconv" style="display:none">
    <div class="control-group">
        <label for="renderer_avconv" class="control-label">AVCONV path</label>
        <div class="controls">
            <input type="text" name="renderer_avconv" value="<?php echo $input['renderer_avconv']?>"/>
        </div>
    </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_num_jobs" class="control-label">®renderer_jobs®</label>
        <div class="controls">
            <input type="text" name="renderer_num_jobs" value="<?php echo $input['renderer_num_jobs']?>"/>
        </div>
    </div>
    
    <div class="control-group">
        <label for="renderer_num_threads" class="control-label">®renderer_threads®</label>
        <div class="controls">
            <input type="text" name="renderer_num_threads" value="<?php echo $input['renderer_num_threads']?>"/>
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="submit_step_3_prev" value="®previous®"/>
        <input type="submit" class="btn btn-primary" name="submit_step_3_next" onclick="swap('load_step_3','main_step_3');" value="®continue®"/>
        <input type="submit" class="btn btn-primary" name="submit_step_3_skip" value="®skip®"/>
    </div>
</form>
</div>
<div id="load_step_3" style="text-align:center; display: none">
<br/><br/>
®create_renderer_step_3_loading® (<?php echo $_SESSION['renderer_address'];?>).
<br/>®create_renderer_step_3_wait®
<br/><br/><br/>
<img src="img/loading_white.gif"/>
</div>
<script>select_option();</script>