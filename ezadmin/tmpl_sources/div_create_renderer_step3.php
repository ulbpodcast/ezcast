    
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
    
<div class="page_title">®create_renderer_step_3®</div>
<div id="main_step_3">
    
    <form method="POST" class="form-horizontal">
        
    <?php if (isset($error)) {
    ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert"> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button> 
            <?php echo $error; ?>
        </div>
    <?php
} else {
        ?>
        <div class="alert alert-success">®ssh_connection_success®</div>
    <?php
    } ?>
        
        <input type="hidden" name="renderer_step" value="3"/>
            
        <div class="form-group">
            <label for="renderer_root_path" class="col-md-2 control-label">®renderer_root_path®</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" name="renderer_root_path" value="<?php echo $input['renderer_root_path']?>"/>
            </div>
        </div>
            
        <div class="form-group">
            <label for="renderer_php" class="col-md-2 control-label">®renderer_php®</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" name="renderer_php" value="<?php echo $input['renderer_php']?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="renderer_rsync" class="col-md-2 control-label">rsync</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" name="renderer_rsync" value="<?php echo $input['renderer_rsync']?>"/>
            </div>
        </div>  
        <div class="form-group">
            <label for="renderer_options" class="col-md-2 control-label">®renderer_pgm®</label>
            <div class="col-sm-5">
                <select class="selectpicker form-control" id="options" name="renderer_options" onchange="select_option();">
                <?php 
                foreach ($renderers_options as $option_name => $option) {
                    
                        echo '<option value="' . $option_name . '"';
                        if (isset($input) && array_key_exists('renderer_options', $input) &&
                                $option_name == $input['renderer_options']) {
                            echo ' selected ';
                        }
                        echo '>'.$option['description'] . '</option>';
                    }
                
                ?>
                </select>
            </div>
        </div>
            
        <div class="renderer_option ffmpeg ffmpeg_built_in_aac">
            <div class="form-group">
                <label for="renderer_ffmpeg" class="col-md-2 control-label">®renderer_ffmpeg®</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="renderer_ffmpeg" value="<?php echo $input['renderer_ffmpeg']?>"/>
                </div>
            </div>
                
            <div class="form-group">
                <label for="renderer_ffprobe" class="col-md-2 control-label">®renderer_ffprobe®</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="renderer_ffprobe" value="<?php echo $input['renderer_ffprobe']?>"/>
                </div>
            </div>
        </div>
            
        <div class="form-group">
            <div class="renderer_option avconv" style="display:none">
                <label for="renderer_avconv" class="col-md-2 control-label">AVCONV path</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="renderer_avconv" value="<?php echo $input['renderer_avconv']?>"/>
                </div>
            </div>
        </div>
            
        <div class="form-group">
            <label for="renderer_num_jobs" class="col-md-2 control-label">®renderer_jobs®</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" name="renderer_num_jobs" value="<?php echo $input['renderer_num_jobs']?>"/>
            </div>
        </div>
            
        <div class="form-group">
            <label for="renderer_num_threads" class="col-md-2 control-label">®renderer_threads®</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" name="renderer_num_threads" value="<?php echo $input['renderer_num_threads']?>"/>
            </div>
        </div>
        <br />
        <div class="form-group text-center">
            <input type="submit" class="btn btn-primary" name="submit_step_3_prev" value="®previous®"/>
            <input type="submit" class="btn btn-primary" name="submit_step_3_next" onclick="swap('load_step_3','main_step_3');" value="®continue®"/>
            <input type="submit" class="btn btn-warning" name="submit_step_3_skip" value="®skip®"/>
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