
<?php if (!empty($error_asset['Error_date'])) {
    ?>
    <div class="alert alert-warning" role="alert">
        ®classroom_not_display_date®
        <?php echo implode(', ', $error_asset['Error_date']); ?>
    </div>
<?php
}
if (!empty($error_asset['Error_time'])) {
    ?>
    <div class="alert alert-warning" role="alert">
        ®classroom_not_display_short_record®
        <?php echo implode(', ', $error_asset['Error_time']); ?>
    </div>
<?php
} ?>


<table class="table table-vertical-bordered table-center"> 
    <thead> 
        <tr> 
            <th></th> 
            <th>®calendar_monday®</th> 
            <th>®calendar_tuesday®</th> 
            <th>®calendar_wednesday®</th>
            <th>®calendar_thursday®</th>
            <th>®calendar_friday®</th>
            <th>®calendar_saturday®</th>
            <th>®calendar_sunday®</th> 
        </tr> 
    </thead> 
    <tbody> 
        <?php for ($h = $START_HOUR; $h <= $END_HOUR; $h += 1) {
        ?>
        <tr style="height: 30px;"> 
            <th style="width: 6.2%;margin-top: -12px;position: absolute;" scope="row">
                <?php echo ($h != $START_HOUR) ? ((int)($h/2)) . ($h % 2 ? ':30' : ':00') : ''; ?>
            </th> 
            <?php for ($day = 1; $day <= 7; ++$day) {
            echo '<td style="width: 13.4%;';
            if (!empty($resultRecord[$day]) && !empty($resultRecord[$day][$h])) {
                echo ' background-color: '.number_to_color($resultRecord[$day][$h]['nbr_cours']).';"';
                    
                $strPopover = "";
                foreach ($resultRecord[$day][$h] as $cours_id => $listCours) {
                    if ($strPopover != "") {
                        $strPopover .= '<br />';
                    }
                        
                    $strPopover .= $listCours['str_infos'];
                }
                    
                echo ' data-container="body" data-toggle="popover" data-placement="left" ';
                echo 'data-content="'.htmlspecialchars($strPopover).'" data-html="true"';
                echo ' data-title="';
                echo htmlspecialchars('<span style="float: right;" class="badge">'
                            .$resultRecord[$day][$h]['nbr_cours'].
                            '</span><br />');
                echo '" data-trigger="hover"';
                    
                echo '>';
            } else {
                echo '">';
            }
            echo '</td>';
        } ?>
        </tr>
        <?php
    } ?>
    </tbody> 
</table>

<div class="col-md-10 col-md-offset-1 jumbotron">
    <p>®calendar_legend®</p>
    <?php for ($color = 1; $color <= $max_count; ++$color) {
        ?>
        <div class="col-md-2" style="margin-bottom: 8px;">
            <div style="background-color: <?php echo number_to_color($color); ?>;
                 width: 30px;height: 30px;display: inline-block;vertical-align: middle;"></div>
            <?php echo $color; ?>
        </div>
    <?php
    }?>
</div>

<script> 
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
}); 
</script>