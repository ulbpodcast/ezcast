
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