
<script>
function swap(div_a, div_b) {
    document.getElementById(div_a).style.display = 'block';
    document.getElementById(div_b).style.display = 'none';
}
swap('main_step_2', 'load_step_2')
</script>

<h4>®create_renderer_step_2®</h4>
<div id="main_step_2">
<form method="POST" class="form-horizontal">
    
    <?php if($error) { ?>
        <div class="alert alert-error"><?php echo $error ?></div>
    <?php } ?>
    <input type="hidden" name="renderer_step" value="2"/>
    ®create_renderer_step_2_message® 
    <br/>(<?php echo $_SESSION['renderer_address'];?>)
    <br/><br/>
    <textarea readonly="true" style="width: 780px; height: 180px;" rows="14" cols="170"><?php echo $ssh_public_key; ?></textarea>
  
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" name="submit_step_2_prev" value="®previous®"/>
        <input type="submit" class="btn btn-primary" name="submit_step_2_next" onclick="swap('load_step_2','main_step_2');" value="®continue®"/>
    </div>
</form>
</div>
<div id="load_step_2" style="text-align:center; display: none">
<br/><br/>
®create_renderer_step_2_loading® (<?php echo $_SESSION['renderer_address'];?>).
<br/>®create_renderer_step_2_wait®
<br/><br/><br/>
<img src="img/loading_white.gif"/>
</div>