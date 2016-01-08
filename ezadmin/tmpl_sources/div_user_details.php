
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

<?php if ($userinfo) { ?>
    <div class="row">
        <div class="span8">
            <form class="form-horizontal" method="POST">

                <?php if ($error) { ?>
                    <div class="alert alert-error"><?php echo $error ?></div>
                <?php } ?>

                <input type="hidden" name="post"/>

                <h4>®user_details_title®: <?php echo $user_ID; ?></h4>

                <!-- User name -->
                <div class="control-group">
                    <label class="control-label">®username®</label>
                    <div class="controls">
                        <?php if ($origin == 'internal') { ?>
                            <div class="view"><?php echo $forename . ' ' . $surname; ?></div>
                            <div class="edit">
                                <input type="text" name="forename" value="<?php echo htmlspecialchars($forename) ?>" placeholder="®forename®"/>
                                <input type="text" name="surname" value="<?php echo htmlspecialchars($surname) ?>" placeholder="®surname®"/>
                            </div>
                        <?php } else { ?>
                            <?php echo $forename . ' ' . $surname; ?>
                            <input type="hidden" name="forename" value="<?php echo htmlspecialchars($forename) ?>"/>
                            <input type="hidden" name="surname" value="<?php echo htmlspecialchars($surname) ?>"/>
                        <?php } ?>
                    </div>
                </div>

                <!-- Origin -->
                <div class="control-group">
                    <label class="control-label">®origin®</label>
                    <div class="controls">
                        <span class="label <?php if ($origin == 'internal') echo 'label-info'; ?>">
                            <?php echo ($origin == 'internal') ? '®intern®' : '®extern®'; ?></span>
                    </div>
                </div>

                <!-- Is EZcast admin -->
                <div class="control-group">
                    <label class="control-label">®is_admin_title®</label>
                    <div class="view">
                        <?php if ($is_admin) { ?>
                            <div class="controls"><i class="icon-ok"></i> ®yes®</div>
                        <?php } else { ?>
                            <div class="controls"><i class="icon-remove"></i> ®no®</div>
                        <?php } ?>
                    </div>
                    <div class="edit">
                        <label style="margin-left:160px"><input type="checkbox" name="permissions" <?php echo $is_admin ? 'checked' : '' ?> /></label>
                    </div>
                </div>

                <!-- Is EZadmin admin -->
                <div class="control-group">
                    <label class="control-label">®ezadmin_access_title®</label>
                    <div class="view">
                        <?php if ($is_ezadmin) { ?>
                            <div class="controls"><i class="icon-ok"></i> ®yes®</div>
                        <?php } else { ?>
                            <div class="controls"><i class="icon-remove"></i> ®no®</div>
                        <?php } ?>
                    </div>
                    <div class="edit">
                        <label style="margin-left:160px"><input type="checkbox" name="is_ezadmin" <?php echo $is_ezadmin ? "checked" : '' ?> /></label>
                    </div>
                </div>

                <!-- In recorders -->
                <div class="control-group">
                    <label class="control-label">®recorders®</label>
                    <?php if ($in_classroom) { ?>
                        <div class="controls"><i class="icon-ok"></i> ®yes®</div>
                    <?php } else { ?>
                        <div class="controls"><i class="icon-remove"></i> ®no®</div>
                    <?php } ?>
                </div>

                <!-- recorder passwd -->
                <div class="edit">
                    <div class="control-group">
                        <label class="control-label">®recorder_passwd®</label>
                        <div class="controls">
                            <input type="password" name="recorder_passwd" placeholder="password"/><label>®leave_empty_field®</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="span4">
            <table>
                <tr>
                    <td><button class="btn edit_cancel">®cancel®</button></td>
                    <td><button class="btn edit_mode">®edit_button®</button></td>
                    <?php if ($origin == 'internal') { ?>
                        <td>
                            <form action="index.php?action=remove_user" method="POST" style="margin:0px;">
                                <input type="hidden" name="user_ID" value="<?php echo $user_ID; ?>" />
                                <input type="submit" name="delete" value="®delete®" onClick="confirm('®delete_confirm®')" class="btn btn-danger delete_button" />
                            </form>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover courses_table">
        <thead>
            <tr>
                <th>®course_code®</th>
                <th>®course_name®</th>
                <th>®link_origin®</th>
                <th>®recorders®</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $c) { ?>
                <tr data-id="<?php echo $c['ID'] ?>" data-origin="<?php echo $u['origin'] ?>">
                    <td><?php echo $c['course_code']; ?></td>
                    <td><?php echo $c['course_name']; ?></td>
                    <td><span class="label <?php if ($c['origin'] == 'internal') echo 'label-info'; ?>"><?php
                            if ($c['origin'] == 'internal')
                                echo '®intern®';
                            else
                                echo '®extern®';
                            ?></span></td>
                    <td><?php echo $c['in_recorders'] ? '<i class="icon-ok"></i> ®yes®' : '<i class="icon-remove"></i> ®no®'; ?></td>
                    <td class="unlink" style="cursor: pointer;"><?php if ($c['origin'] == 'internal') echo '<i class="icon-remove"></i> ®remove_link®'; ?></td>
                </tr>
    <?php } ?>
        </tbody>
    </table>

    <div class="create_link form-inline">
        <input type="text" name="link_to" value="" class="input-medium" placeholder="®course_code®" data-provide="typeahead" autocomplete="off" />
        <button name="link" class="btn btn-primary">®add_user_course®</button>
    </div>



    <script>

                                $(function() {
                                    $('.edit_cancel').hide();
                                    $('.edit').hide();
                                    $("button.edit_cancel").click(function() {
                                        $this = $(this);
                                        $this.hide();
                                        $('.edit_mode').removeClass('btn-primary');
                                        $('.delete_button').show();
                                        $(".edit").hide();
                                        $('.view').show();

                                    });

                                    $("button.edit_mode").click(function() {
                                        $this = $(this);
                                        $('.edit_cancel').show();
                                        $('.delete_button').hide();

                                        if ($this.hasClass("btn-primary")) {
                                            $("form").first().submit();
                                        } else {
                                            $this.addClass('btn-primary');
                                            $(".edit").show();
                                            $('.view').hide();
                                        }
                                    });


                                    $(".courses_table .unlink").live("click", function() {
                                        $this = $(this);

                                        if ($this.parent().data('origin') == 'external')
                                            alert("®cannot_delete_external®");
                                        if (!confirm("®unlink_confirm®"))
                                            return false;

                                        var link = $this.parent().data("id");

                                        $.ajax("index.php?action=link_unlink_course_user&user_ID=<?php echo $input['user_ID'] ?>", {
                                            type: "post",
                                            data: {
                                                query: "unlink",
                                                id: link
                                            },
                                            success: function(jqXHR, textStatus) {
                                                var data = JSON.parse(jqXHR);

                                                if (data.error) {
                                                    if (data.error == 1)
                                                        alert("®cannot_delete_external®");
                                                    return;
                                                }

                                                $this.parent().hide(400, function() {
                                                    $(this).remove();
                                                });
                                            }
                                        });
                                    });

                                    $(".create_link button[name='link']").click(function() {
                                        $this = $(this);

                                        var user = $this.prev().val();
                                        $this.prev().val('');

                                        $.ajax("index.php?action=link_unlink_course_user&user_ID=<?php echo $input['user_ID'] ?>", {
                                            type: "post",
                                            data: {
                                                query: "link",
                                                id: user
                                            },
                                            success: function(jqXHR, textStatus) {
                                                var data = JSON.parse(jqXHR);

                                                if (data.error) {
                                                    if (data.error == '1')
                                                        alert("®link_error®");
                                                    return;
                                                }

                                                var $course_code = $('<td></td>').text(data.course_code);
                                                var $course_name = $('<td></td>').text(data.course_name);
                                                var $delete = $('<td class="unlink" style="cursor:pointer;"><i class="icon-remove"></i>®remove_link®</td>');

                                                var $tr = $('<tr data-id="' + data.id + '"></tr>');
                                                $tr.append($course_code);
                                                $tr.append($course_name);
                                                $tr.append($delete);

                                                $tr.hide();

                                                $('.courses_table tbody').append($tr);

                                                $tr.show(400).css('display', 'table-row');
                                            }
                                        });
                                    });
                                });

    </script>

<?php } else { ?>

    <em><?php echo $input['user_ID'] ?></em> ®unknown_user®

<?php } ?>


