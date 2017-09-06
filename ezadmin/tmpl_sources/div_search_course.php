
<div class="page_title">®list_courses_title®</div>

<!-- Search form -->
<form method="POST" action="index.php?action=view_courses" style="width: 100%" class="search_course">  
    <input type="hidden" name="post"/>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="col" value="<?php echo $input['col'] ?>" />
    <input type="hidden" name="order" value="<?php echo $input['order'] ?>" />
    
    <div class="form-group">
        <div class="row">
            <div class="col-md-4 col-md-offset-1">
                <label class="sr-only" for="course_code">®course_code®</label>
                <input class="form-control" type="text" placeholder="®course_code®" 
                   title="®course_code®" name="course_code" id="course_code"
                   value="<?php if (isset($input) && isset($input['course_code'])) {
    echo $input['course_code'];
} ?>" />
            </div>
            <div class="col-md-4">
                <label class="sr-only" for="teacher">®teacher®</label>
                <input class="form-control" type="text" placeholder="®teacher®" 
                   title="®teacher®" name="teacher"  id="teacher"
                   value="<?php if (isset($input) && isset($input['teacher'])) {
    echo $input['teacher'];
} ?>" />
            </div>
            <div class="col-md-2">
                <input type="submit" name="search" value="®search®" class="btn btn-block btn-primary">
            </div>
        </div>
    </div>
    
    <div class="col-md-offset-1">
        <div>
            <a href="#" onclick="javascript:toggleVisibility('options_block');">
                <span class="glyphicon glyphicon-plus"></span> 
                ®options®
            </a>
        </div>

        <div class="row" style="display: none;" id="options_block">
            <div class="col-md-2 col-md-offset-1">
                <label class="control-label" style="display: inline-block;">
                    ®origin®: 
                </label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®intern®" name="intern" 
                            <?php echo isset($input['intern']) ? 'checked' : ''; ?> /> 
                        ®intern®
                    </label>
                </div>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®extern®" name="extern" 
                            <?php echo isset($input['extern']) ? 'checked' : ''; ?> /> 
                        ®extern®
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                ®has_albums_title®: 
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®has_albums®" name="has_albums" 
                            <?php echo isset($input['has_albums']) ? 'checked' : ''; ?> />
                            ®has_albums®
                    </label>
                </div>

                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®no_albums®" name="no_albums" 
                            <?php echo isset($input['no_albums']) ? 'checked' : ''; ?> />
                        ®no_albums®
                    </label>
                </div>
            </div>

            <div class="col-md-2">
                ®recorders®: 
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®in_recorders®" name="in_recorders" 
                            <?php echo isset($input['in_recorders']) ? 'checked' : ''; ?> />
                        ®in_recorders®
                    </label>
                </div>

                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®out_recorders®" name="out_recorders" 
                            <?php echo isset($input['out_recorders']) ? 'checked' : ''; ?> />
                        ®out_recorders®
                    </label>
                </div>
            </div>

            <div class="col-md-2">
                ®teacher_assign®: 
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®with_teacher®" name="with_teacher" 
                            <?php echo isset($input['with_teacher']) ? 'checked' : ''; ?> />
                        ®in_recorders®
                    </label>
                </div>

                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" title="®without_teacher®" name="without_teacher" 
                            <?php echo isset($input['without_teacher']) ? 'checked' : ''; ?> />
                        ®out_recorders®
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
<br />