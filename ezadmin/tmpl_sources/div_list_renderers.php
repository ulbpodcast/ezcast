
<?php
require_once 'config.inc';
?>

<table class="table table-striped table-hover table-condensed renderers">
    <tr>
        <th></th>
        <th>®renderer_performance_idx®</th>
        <th>®renderer_name®</th>
        <th>®renderer_hostname®</th>
        <th>®renderer_pgm®</th>
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
        exec('ping -c 1 '.$r['host'], $output, $return_val); 
        if($return_val != 0){
            $r['no_ping'] = true;
        } else {
        $r = lib_scheduling_renderer_metadata($r);
        }
        //var_dump($r2);
        ?>
        <tr class="<?php echo $class; ?>">
            <td><?php if($r['no_ping'] === true) echo '<span title="®no_ping®"><span class="glyphicon glyphicon-warning-sign"></span></span>';
                      if($r['ssh_error'] === true) echo '<span title="®ssh_error®"><span class="glyphicon glyphicon-warning-sign"></span></span>';?></td>
            <td><?php echo $r['performance_idx']; ?></td>
            <td class="renderer_name"><?php echo $r['name']; ?></td>
            <td><?php echo $r['host']; ?></td>
            <td><span title="<?php echo $r['encoding_desc'] ?>"><?php echo $r['encoding_pgm']; ?></span></td>
            <td><?php echo $r['num_jobs'] . '/' . $r['max_num_jobs']; ?></td>
            <td><?php echo $r['load']; ?></td>
            <td><?php echo $r['max_num_threads']; ?></td>
            <td><?php echo $r['status'] == 'enabled' ? '<span class="glyphicon glyphicon-ok"></span>' : '<i></i>'; ?></td>
            <td>
                <button class="btn btn-small enabled_button <?php echo $r['status'] != 'enabled' ? 'btn-success' : '' ?>"><?php echo $r['status'] != 'enabled' ? '®enable®' : '®disable®' ?></button>
            </td>
            <td>
                <button class="btn btn-small delete_button"><span class="glyphicon glyphicon-trash"></span></button>
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