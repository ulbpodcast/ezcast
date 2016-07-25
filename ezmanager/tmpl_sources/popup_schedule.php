<?php
/*
 * EZCAST EZmanager 
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 		    Arnaud Wijns <awijns@ulb.ac.be>
 *                   Antoine Dewilde
 * UI Design by Julien Di Pietrantonio
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


<!--
This popup appears when the user clicks on "move this record".
It presents the user with a list of albums he can move the asset to.

You should not have to use this template on its own. however, if you do, please
make sure $created_albums_with_descriptions is initialized and is an array containing the album names (without any suffix) as keys, and albums descriptions as values
for every album the user can create.
-->

<div class="popup" id="popup_schedule_<?php echo $asset_name; ?>">
    <h2>®Program®</h2>
    <strong>®Title®&nbsp;: </strong><?php echo htmlspecialchars($title); ?><br/><br/>
    <?php if ($asset_scheduled) { ?>
        ®Asset_sched_move® 
        <?php
        $date = (get_lang() == 'fr') ? new DateTimeFrench($asset_sched_date, $DTZ) : new DateTime($asset_sched_date, $DTZ);
        $dateVerbose = (get_lang() == 'fr') ? $date->format('j F Y à H\hi') : $date->format("F j, Y, g:i a");
        echo $dateVerbose;
        ?>
        <br/><br/>
        ®Asset_sched_remove® 
        <br/><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="Bouton"> <a href="?action=cancel_schedule_asset&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>"><span>®Delete_sched®</span></a></span>

    <?php } else { ?>
        <br/>®schedule_at®<br/><br/>
        <form  action="index.php" onsubmit="return false;" method="post" id="schedule_form">
            <input type="hidden" name="action" value="schedule_asset"/>
            <input type="hidden" id="album" name="album" value="<?php echo $album; ?>"/>
            <input type="hidden" id="asset" name="asset" value="<?php echo $asset_name; ?>"/>
            <input id="datepicker_<?php echo $asset_name; ?>" class="datepicker" type="text" name="date" value="">
            <br/><br/>
            <div id="submitButton">
                <!-- <span class="Bouton"><span><input type="submit" value="®Submit®" /></span></span> -->
                <!--span class="Bouton"><a href="javascript:document.forms['edit_form'].submit();"><span>®Update®</span></a></span-->
                <span class="Bouton"><a href="javascript:submit_schedule_form();"><span>®Program®</span></a></span>
            </div>
            <script >

                $(function () {
                    d = new Date();
                    $('.datepicker').appendDtpicker({
                        "futureOnly": true,
                        "inline": true
                    });
                });
                function submit_schedule_form() {
                    var date = encodeURIComponent(document.getElementById('datepicker_<?php echo $asset_name; ?>').value);
                    show_popup_from_outer_div('index.php?action=schedule_asset&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>&date=' + date, true);
                }
            </script>
        </form>
    <?php } ?>
</div>