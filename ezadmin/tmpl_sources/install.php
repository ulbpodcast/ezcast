<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>®install_page_title®</title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/style.css" rel="stylesheet"/>
        <script type="text/javascript" src="./jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="./modernizr.custom.23345.js"></script>

        <script>
            /**
             * HTML5 Placeholder Text, jQuery Fallback with Modernizr
             *
             * @url        http://uniquemethod.com/
             * @author    Unique Method
             */
            $(function()
            {
                // check placeholder browser support
                if (!Modernizr.input.placeholder)
                {

                    // set placeholder values
                    $(this).find('[placeholder]').each(function()
                    {
                        if ($(this).val() == '') // if field is empty
                        {
                            $(this).val($(this).attr('placeholder'));
                        }
                    });

                    // focus and blur of placeholders
                    $('[placeholder]').focus(function()
                    {
                        if ($(this).val() == $(this).attr('placeholder'))
                        {
                            $(this).val('');
                            $(this).removeClass('placeholder');
                        }
                    }).blur(function()
                    {
                        if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))
                        {
                            $(this).val($(this).attr('placeholder'));
                            $(this).addClass('placeholder');
                        }
                    });

                    // remove placeholders on submit
                    $('[placeholder]').closest('form').submit(function()
                    {
                        $(this).find('[placeholder]').each(function()
                        {
                            if ($(this).val() == $(this).attr('placeholder'))
                            {
                                $(this).val('');
                            }
                        })
                    });

                }
            });
        </script>
        <script src="bootstrap/js/bootstrap-dropdown.js"></script>
    </head>

    <body link="#000088" vlink="#000044" alink="#0000ff" <?php if (isset($GLOBALS['debugmode']) && $GLOBALS['debugmode'] == "devl") {
    echo 'background="#99ff99"';
} ?>>
        <div class="container_ezplayer">
            <?php include_once template_getpath('div_header.php'); ?>
            <div id="global">
            <h1>®install_title®</h1>
            <?php foreach ($errors as $e) {
    ?>
                <div class="alert alert-error">
                    <?php echo $e; ?>
                </div>
                <?php
}
            ?>
                <form method="POST" class="form-horizontal" enctype="multipart/form-data" >
                <div class="wizard">
                    <div class="panel">
                        <div class="panel-heading btn input-block-level">
                            <h4>®settings_panel_general®</h4>
                        </div>

                        <div class="panel-body well" style="margin-bottom:0;">
                            <div class="row">
                                    <div class="col-md-6" style='width: 75%;'>

                                    <div class="control-group">
                                        <label class="control-label">®settings_application_url®</label>
                                        <div class="controls">
                                            <input type="url" required name="application_url" value="<?php echo htmlspecialchars($input['application_url']) ?>" />
                                            <span class="help-block">Root URL of EZcast applications</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="checkbox">
                                                    <input type="checkbox" name="https_ready" <?php echo (isset($input['https_ready']) && !empty($input['https_ready'])) ? 'checked' : '' ?> />
                                                ®https_ready®
                                            </label>
                                            <span class="help-block">Check this box if your website is ready for https connections.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_organization_name®</label>
                                        <div class="controls">
                                            <input type="text" required name="organization_name" value="<?php echo htmlspecialchars($input['organization_name']) ?>" />
                                            <span class="help-block">Name of the application owner, i.e. your organization or university.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                            <label class="control-label">®settings_organization_url®</label>
                                            <div class="controls">
                                                <input type="text" required name="organization_url" value="<?php echo htmlspecialchars($input['organization_url']) ?>" />
                                                <span class="help-block">URL to the application owner's website, i.e. your organization or university. This URL will be a link in the header of the application</span>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">®settings_organization_logo®
                                                <div style='cursor: help; color: #0081c2;' onmouseover="document.getElementById('preview').style.display='block';" onmouseout="document.getElementById('preview').style.display='none';">preview
                                                    <div id='preview' style='display: none; position: absolute; left: 180px; border: 2px solid #B2B2B2;'><img src='./img/preview.png'/></div></div>
                                            </label>
                                            <div class="controls">
                                                <input type="file" name="organization_logo" accept="image/png"/>
                                                <span class="help-block"><b>The logo MUST be a png file smaller than 1Mo. It is best displayed when height is 42px.</b><br/>Leave empty if you don't want any logo to be displayed in the header of the application.</span>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                        <label class="control-label">®settings_copyright®</label>
                                        <div class="controls">
                                            <input type="text" name="copyright" value="<?php echo htmlspecialchars($input['copyright']) ?>" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_email®</label>
                                        <div class="controls">
                                            <input type="text" name="mailto_alert" value="<?php echo htmlspecialchars($input['mailto_alert']) ?>" />
                                            <span class="help-block">The email contacted if an error occurs</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_repository_basedir®</label>
                                        <div class="controls">
                                            <input type="text" required name="repository_basedir" value="<?php echo htmlspecialchars($input['repository_basedir']) ?>" />
                                            <span class="help-block">Path to the repository (from the root)</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_ezcast_basedir®</label>
                                        <div class="controls">
                                            <input type="text" required name="ezcast_basedir" value="<?php echo htmlspecialchars($input['ezcast_basedir']) ?>" />
                                            <span class="help-block">Path to the EZcast application (from the root)</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_php_path®</label>
                                        <div class="controls">
                                            <input type="text" required name="php_cli_cmd" value="<?php echo htmlspecialchars($input['php_cli_cmd']) ?>" />
                                            <span class="help-block">Path to the PHP binary (for command line scripts)</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_rsync_path®</label>
                                        <div class="controls">
                                            <input type="text" required name="rsync_pgm" value="<?php echo htmlspecialchars($input['rsync_pgm']) ?>" />
                                            <span class="help-block">Path to the rsync binary</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="help-body">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading btn input-block-level">
                            <h4>®settings_panel_db®</h4>
                        </div>    

                        <div class="panel-body well" style="margin-bottom:0;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <label class="control-label">®settings_db_type®</label>
                                        <div class="controls">
                                            <select name="db_type">
                                                <option value="mysql" <?php echo $input['db_type'] ? 'selected' : '' ?>>MySQL</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_db_host®</label>
                                        <div class="controls">
                                            <input type="text" required name="db_host" value="<?php echo htmlspecialchars($input['db_host']) ?>" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_db_login®</label>
                                        <div class="controls">
                                            <input type="text" required name="db_login" value="<?php echo htmlspecialchars($input['db_login']) ?>" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_db_passwd®</label>
                                        <div class="controls">
                                            <input type="password" required name="db_passwd" value="<?php echo htmlspecialchars($input['db_passwd']) ?>" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_db_name®</label>
                                        <div class="controls">
                                            <input type="text" required name="db_name" value="<?php echo htmlspecialchars($input['db_name']) ?>" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_db_prefix®</label>
                                        <div class="controls">
                                            <input type="text" required name="db_prefix" value="<?php echo htmlspecialchars($input['db_prefix']) ?>" />
                                            <span class="help-block">All the tables within the database will be named using this prefix.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="help-body">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading btn input-block-level">
                            <h4>®settings_panel_recorder®</h4>
                        </div>    

                        <div class="panel-body well" style="margin-bottom:0;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <label class="control-label">®settings_recorder_user®</label>
                                        <div class="controls">
                                            <input type="text" required name="recorder_user" value="<?php echo htmlspecialchars($input['recorder_user']) ?>" />
                                            <span class="help-block">Name of the Unix account used on the recorders to SSH into.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_recorder_basedir®</label>
                                        <div class="controls">
                                            <input type="text" required name="recorder_basedir" value="<?php echo htmlspecialchars($input['recorder_basedir']) ?>" />
                                            <span class="help-block">Path to the source code on the recorders (from the root)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="help-body">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading btn input-block-level">
                            <h4>®settings_panel_ezmanager®</h4>
                        </div>

                        <div class="panel-body well" style="margin-bottom:0;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <label class="control-label">®settings_ezmanager_host®</label>
                                        <div class="controls">
                                            <input type="text" name="ezmanager_host" value="<?php echo htmlspecialchars($input['ezmanager_host']) ?>" />
                                            <span class="help-block">This field can be left blank if EZmanager is on the same machine as EZadmin. Otherwise, write here the hostname of the machine that hosts EZmanager.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">®settings_ezmanager_user®</label>
                                        <div class="controls">
                                            <input type="text" name="ezmanager_user" value="<?php echo htmlspecialchars($input['ezmanager_user']) ?>" />
                                            <span class="help-block">This field can be left blank if EZmanager is on the same machine as EZadmin. Otherwise, write here the username to use when SSH'ing into EZmanager.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="help-body">
                                    </div>
                                </div>
                            </div>         
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading btn input-block-level">
                            <h4>®settings_panel_user®</h4>
                        </div>

                        <div class="panel-body well" style="margin-bottom:0;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="checkbox">
                                                    <input type="checkbox" name="classrooms_category_enabled" <?php echo (isset($input['classrooms_category_enabled']) && !empty($input['classrooms_category_enabled'])) ? 'checked' : '' ?> />
                                                ®classrooms_recording_enabled®
                                            </label>
                                            <span class="help-block">If checked, EZadmin will include a category to manage the recorders in the classrooms. This setting can be changed later on.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="checkbox">
                                                    <input type="checkbox" name="add_users_enabled" <?php echo (isset($input['add_users_enabled']) && !empty($input['add_users_enabled'])) ? 'checked' : '' ?> />
                                                ®add_users_enabled®
                                            </label>
                                            <span class="help-block">If unchecked, only the users coming from an external source (e.g. university's registry) will have access to the recorders. If checked, admins can manually add users along with the "official" ones. This setting can be changed later on.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="checkbox">
                                                    <input type="checkbox" name="recorder_password_storage_enabled" <?php echo (isset($input['recorder_password_storage_enabled']) && !empty($input['recorder_password_storage_enabled'])) ? 'checked' : '' ?> />
                                                ®password_storage_enabled®
                                            </label>
                                            <span class="help-block">If checked, the same password will be used both for EZmanager and EZrecorder. If unchecked, EZmanager will use an "external" password (e.g. using the university's identification system), if available. This setting can be changed later on.</span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="checkbox">
                                                    <input type="checkbox" name="use_user_name" <?php echo (isset($input['use_user_name']) && !empty($input['use_user_name'])) ? 'checked' : '' ?> />
                                                ®settings_use_user_name®
                                            </label>
                                            <span class="help-block">If checked, users will be referenced by their full name. If unchecked, users will be referred to by their ID. This setting only influences the way EZadmin displays data, it does not influence any other product. This setting can be changed later on.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="help-body">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" name="install" value="®install®" class="btn btn-primary"/>
                    <input type="reset" name="install" value="®reset®" class="btn"/>
                </div>
            </form>
        </div>
            <?php include_once template_getpath('div_footer.php'); ?>
        </div>

        <script>

            $(function() {
                $(".help-block").hide();

                $(".control-group").hover(function() {
                    $(this).closest('.panel-body').find('.help-body').first().text($(this).find('.help-block').text());
                }, function() {
                    $(this).closest('.panel-body').find('.help-body').first().text($(this).closest('.panel-body').find('input:focus, select:focus').first().closest('.controls').find('.help-block').text());
                }).find('input, select').focus(function() {
                    $(this).closest('.panel-body').find('.help-body').first().text($(this).closest('.controls').find('.help-block').text());
                }).blur(function() {
                    $(this).closest('.panel-body').find('.help-body').first().text('');
                });

                $(".wizard .panel").each(function() {
                    var $this = $(this);

                    $this.find(".panel-heading").click(function() {
                        var $next = $(this).next(".panel-body");

                        if ($next.is(':visible'))
                            return;

                        $('.panel-body').slideUp();
                        $next.slideDown();
                    });
                });

                $(".panel-body").hide();
                $(".panel-heading").first().click();
            });

        </script>
    </body>
</html>
