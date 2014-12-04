
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

<?php
require_once 'config.inc';
?>

<table class="table table-striped table-hover table-condensed renderers">
    <tr>
        <th>®renderer_performance_idx®</th>
        <th>®renderer_name®</th>
        <th>®renderer_hostname®</th>
        <th>®renderer_jobs®</th>
        <th>®renderer_load®</th>
        <th>®renderer_threads®</th>
        <th>®room_enabled®</th>
        <th>®enable_disable®</th>
        <th></th>
    </tr>

    <?php
    include_once 'lib_scheduling.php';
    foreach ($renderers as $r) {
        $r = lib_scheduling_renderer_metadata($r);
        //var_dump($r2);
        ?>
        <tr class="<?php echo $class; ?>">
            <td><?php echo $r['performance_idx']; ?></td>
            <td class="renderer_name"><?php echo $r['name']; ?></td>
            <td><?php echo $r['host']; ?></td>
            <td><?php echo $r['num_jobs'] . '/' . $r['max_num_jobs']; ?></td>
            <td><?php echo $r['load']; ?></td>
            <td><?php echo $r['max_num_threads']; ?></td>
            <td><?php echo $r['status'] == 'enabled' ? '<i class="icon-ok"></i>' : '<i></i>'; ?></td>
            <td>
                <button class="btn btn-small enabled_button <?php echo $r['status'] != 'enabled' ? 'btn-success' : '' ?>"><?php echo $r['status'] != 'enabled' ? '®enable®' : '®disable®' ?></button>
            </td>
            <td>
                <button class="btn btn-small delete_button"><i class="icon-trash"></i></button>
            </td>
        </tr>
        <?php
    }
    ?>
</table>

<script>

    $(function() {

        $("table.renderers .enabled_button").click(function() {
            $this = $(this);

            var renderer = $this.parent().parent().find("td.renderer_name").text();

            if ($this.hasClass('btn-success')) {
                $.ajax("index.php?action=enable_renderer", {
                    type: "post",
                    data: {
                        name: renderer
                    },
                    success: function(jqXHR, textStatus) {

                        var data = JSON.parse(jqXHR);

                        if (data.error) {
                            if (data.error == '1')
                                alert("®room_enable_error®");
                            return;
                        }

                        $this.removeClass('btn-success');
                        $this.text('®disable®');
                        $this.parent().prev().find("i").addClass('icon-ok');
                    }
                });
            } else {
                $.ajax("index.php?action=disable_renderer", {
                    type: "post",
                    data: {
                        name: renderer
                    },
                    success: function(jqXHR, textStatus) {

                        var data = JSON.parse(jqXHR);

                        if (data.error) {
                            if (data.error == '1')
                                alert("®renderer_enable_error®");
                            return;
                        }

                        $this.addClass('btn-success');
                        $this.text('®enable®');
                        $this.parent().prev().find("i").removeClass('icon-ok');
                    }
                });
            }
        });



        $("table.renderers .delete_button").click(function() {
            if (!confirm('®delete_confirm®'))
                return;
            var $this = $(this);

            var renderer = $this.parent().parent().find("td.renderer_name").text();

            $.ajax("index.php?action=remove_renderer", {
                type: "post",
                data: {
                    name: renderer
                },
                success: function(jqXHR, textStatus) {
                    var data = JSON.parse(jqXHR);

                    if (data.error) {
                        if (data.error == '1')
                            alert("®renderer_delete_error®");
                        return;
                    } else {
                            alert("®renderer_delete®");
                    }

                    $this.parent().parent().hide(400, function() {
                        $(this).remove();
                    });
                }
            });
        });

    });

</script>