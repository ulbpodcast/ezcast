<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2014 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
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

if (acl_display_threads()) {
    include_once 'lib_print.php';

    $asset_threads = thread_get_all_by_asset();
    $threads = array();

    foreach ($asset_threads as $value) {
        if (($value['studentOnly'] == '0') || ($value['studentOnly'] == '1' && !acl_has_moderated_album()) || acl_is_admin()) {
            $threads[] = $value;
            ?>
            <script>
                timecode_array[<?php echo json_encode($value['id']); ?>] = <?php echo json_encode($value['timecode']); ?>;
                title_array["<?php echo $value['id']; ?>"] = "<?php echo $value['title']; ?>";
            </script>
            <?php
        }
    }
    ?>
    <div class="threads_header">
        <span class="thread-logo"></span>
        <span id="threads_header-label">®Discussions®</span>
        <a class="refresh-button pull-right" style="margin-top: 0px; margin-right: 48px;" title="®Refresh_discussions®" href="javascript:refresh_asset_threads()"></a>
    </div>
    <div class="threads_list">
        <?php
        if (is_array($threads) && count($threads) > 0) {
            $DTZ = new DateTimeZone('Europe/Paris');
            foreach ($threads as $thread) {
                $creationDate = $thread['creationDate'];
                $creationDateFr = new DateTimeFrench($creationDate, $DTZ);
                $creationDateFrVerbose = $creationDateFr->format('j F Y');
                ?>
                <div class="item-thread" onclick="show_thread_details(event,<?php echo $thread['id'] ?>)">
                    <div class="item-thread-content">
                        <a class="item_link" href="javascript:seek_video(<?php echo $thread['timecode'] ?>, '<?php echo (isset($thread['type'])) ? $thread['type'] : ''; ?>');">  
                            <span class="timecode green2 inline-block"><?php print_time($thread['timecode']); ?> </span>
                        </a>
                        <i class="slash item-thread-slash">//</i>
                        <label class="item-thread-title inline-block" ><?php echo $thread['title']; ?></label 

                        <br /><br />
                        <span class="item-thread-author">
                            <b><label><?php echo $thread['authorFullName']; ?></label></b> 
                            <i class="slash-sm">//</i>
                            <label> <?php echo $creationDateFrVerbose; ?></label>
                            <i class="slash-sm">//</i>
                            <label><?php echo substr($thread['creationDate'], 11, 8); ?></label>
                            <?php if (!(acl_has_moderated_album()) || acl_is_admin()) { ?>
                                <i class="slash-sm">//</i>
                                <label class="green-title"><?php echo ($thread['studentOnly'] == '1') ? '®Visibility_students®' : '®Visibility_all®'; ?></label>
                            <?php } ?>
                        </span>
                        <a class="more-button-small more-button-<?php echo $thread['id']; ?> item-thread-more" 
                           onclick="toggle_hidden_thread_part(<?php echo $thread['id'] ?>)">
                        </a>
                        <br/>
                        <div class="hidden-item-thread" id="hidden-item-thread-<?php echo $thread['id']; ?>">
                            <?php echo nl2br(html_entity_decode($thread['message'])); ?>     
                        </div>
                    </div>
                    <div class="eom"></div>
                </div>
                <?php
            }
        }
        ?>

    </div>
    <?php
}
?>