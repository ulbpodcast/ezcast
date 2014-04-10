<!--
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
-->

<h4>®list_courses_title®</h4>

<!-- Search form -->
<form method="POST" action="index.php?action=view_courses" class="form-inline search_course">  
    <input class="input-large auto-clear placeholder" type="text" placeholder="®course_code®" title="®course_code®" name="course_code" value="<?php echo $input['course_code']; ?>" />
    <input class="input-large auto-clear placeholder" type="text" placeholder="®teacher®" title="®teacher®" name="teacher" value="<?php echo $input['teacher']; ?>" />
    
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php echo $input['col'] ?>" />
    <input type="hidden" name="order" value="<?php echo $input['order'] ?>" />

    <input type="submit" name="search" value="®search®" class="btn btn-primary">
    <input type="reset" name="reset" value="®reset®" class="btn"> 
    <div><a href="#" onclick="javascript:toggleVisibility('options_block');"><i class="icon-plus"></i> ®options®</a></div>
    <div class="row" style="display: none;" id="options_block">
        <div class="span1"></div>
        <div class="span2">
            <label class="control-label" style="display: inline-block;">
                ®origin®: 
            </label>
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" title="®intern®" name="intern" <?php echo isset($input['intern']) ? 'checked' : ''; ?> /> ®intern®
                </label>
            </div>
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" title="®extern®" name="extern" <?php echo isset($input['extern']) ? 'checked' : ''; ?> /> ®extern®
                </label>
            </div>
        </div>
    <div class="span2">
        ®has_albums_title®: 
        <div class="controls">
            <label class="checkbox">
            <input type="checkbox" title="®has_albums®" name="has_albums" <?php echo isset($input['has_albums']) ? 'checked' : ''; ?> />
            ®has_albums®
            </label>
        </div>
        
        <div class="controls">
            <label class="checkbox">
            <input type="checkbox" title="®no_albums®" name="no_albums" <?php echo isset($input['no_albums']) ? 'checked' : ''; ?> />
            ®no_albums®
            </label>
        </div>
    </div>
        
    <div class="span2">
        ®recorders®: 
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®in_recorders®" name="in_recorders" <?php echo isset($input['in_recorders']) ? 'checked' : ''; ?> />
            ®in_recorders®
        </label>
        </div>
        
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®out_recorders®" name="out_recorders" <?php echo isset($input['out_recorders']) ? 'checked' : ''; ?> />
            ®out_recorders®
        </label>
        </div>
    </div>
    
    <div class="span2">
        ®teacher_assign®: 
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®with_teacher®" name="with_teacher" <?php echo isset($input['with_teacher']) ? 'checked' : ''; ?> />
            ®in_recorders®
        </label>
        </div>
        
        <div class="controls">
        <label class="checkbox">
            <input type="checkbox" title="®without_teacher®" name="without_teacher" <?php echo isset($input['without_teacher']) ? 'checked' : ''; ?> />
            ®out_recorders®
        </label>
        </div>
    </div>
    </div>
</form>

<hr>