
<table class="table table-vertical-bordered table-center"> 
    <thead> 
        <tr> 
            <th></th> 
            <th>Lundi</th> 
            <th>Mardi</th> 
            <th>Mercredi</th>
            <th>Jeudi</th>
            <th>Vendredi</th>
            <th>Samedi</th>
            <th>Dimanche</th>
        </tr> 
    </thead> 
    <tbody> 
        <?php for($h = 12; $h <= 44; $h += 1) { ?>
        <tr> 
            <th style="width: 7.6%;" scope="row"><?php echo ((int)($h/2)) . ($h % 2 ? ':30' : ':00'); ?></th> 
            <?php for($day = 1; $day <= 7; ++$day) {
                echo '<td style="width: 13.2%"';
                if(!empty($resultRecord[$day]) && !empty($resultRecord[$day][$h])) { 
                    echo 'class="info"';
                    
                    echo 'data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
                            sagittis lacus vel augue laoreet rutrum faucibus."';
                    
                    echo '>';
                    echo count($resultRecord[$day][$h]);
                } else {
                    echo '>';
                }
                echo '</td>';
            } ?>
        </tr>
        <?php } ?>
    </tbody> 
</table>


