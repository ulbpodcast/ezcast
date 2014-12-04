
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
    swap('main_step_4', 'load_step_4');

    var save_step = 1;
    var numSteps = 3;
    function loadStepAjax(step) {
        save_step = step;
        $.ajax('index.php?action=create_renderer', {
            type: 'POST',
            dataType: 'json',
            data: "renderer_step=4&installation_step=" + step,
            success: function(response) {
                if (response.error === true){
                    $('#loading_img').hide();
                    $('#main_step_4_failed').show();
                    $('#load_step_4').append(response.msg);
                } else {
                    $('#main_step_4_failed').hide();
                    $('#load_step_4').append(response);
                    if (step < 3) {
                        loadStepAjax(step + 1);
                    } else {
                        $('#loading_img').hide();
                        $('#next_step').show();
                    }
                }
            }
        });
    }
</script>

<h4>®create_renderer_step_4®</h4>
<div id="main_step_4">

    <form method="POST" class="form-horizontal">

        <?php if ($error) { ?>
            <div class="alert alert-error"><?php echo $error ?></div>
        <?php } ?>
        <?php if ($tests_success) { ?>
            <div class="alert alert-success">®configuration_success®</div>
        <?php } ?>
        <input type="hidden" name="renderer_step" value="4"/>

        ®create_renderer_step_4_message®<br/><br/>
        <ul>
            <li>®create_renderer_step_4_copy®</li>
            <li>®create_renderer_step_4_install®</li>
            <li>®create_renderer_step_4_xml®</li>
        </ul>

        <div class="form-actions">
            <input type="submit" class="btn btn-primary" name="submit_step_4_prev" value="®previous®"/>
            <input type="button" class="btn btn-primary" name="submit_step_4_next" onclick="swap('load_step_4', 'main_step_4');
        loadStepAjax(1)" value="®continue®"/>
        </div>
    </form>
</div>
<div id="load_step_4" style="text-align:center; display: none">
    <br/>
    <img id="loading_img" src="img/loading_white.gif"/>
    <br/><br/>
    ®load_step_4_copy®
</div>
<div id="next_step" style="display: none">
    <br/><br/>    
    <form method="POST" class="form-horizontal">
        <input type="hidden" name="renderer_step" value="5"/>
        <div class="form-actions">
            <input type="submit" class="btn btn-primary" name="finish_install" value="®finish®"/>
        </div>
    </form>
</div>
<div id="main_step_4_failed" style="display: none">
    <br/><br/>    
    <form method="POST" class="form-horizontal">
        <input type="hidden" name="renderer_step" value="5"/>
        <div class="form-actions">
            <input type="button" class="btn btn-primary" onclick="swap('loading_img', 'main_step_4');loadStepAjax(save_step)" value="®retry®"/>
        </div>
    </form>
</div>