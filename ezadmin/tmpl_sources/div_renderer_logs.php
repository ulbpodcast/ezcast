
<?php
require_once 'config.inc';
?>

<div class="page_title">®renderers_log®</div>


<table class="table table-striped table-hover table-condensed renderers">
    <?php
    foreach($tail_array as $line){ ?>
        <tr class="">
            <td><?php echo $line ?></td>
        </tr>
    <?php } ?>
</table>