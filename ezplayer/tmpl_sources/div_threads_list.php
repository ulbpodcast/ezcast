<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
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

include_once 'lib_print.php';
?>
<div class="threads_header">
    <span class="thread-logo"></span>
    <span id="threads_header-label">®Discussions®</span>
    <a class="refresh-button pull-right" style="margin-top: 0px; margin-right: 48px;" title="®Refresh_discussions®" href="javascript:threads_list_update(true)"></a>
</div>
<div class="threads_list">
    <script>
    threads_array = new Array();
    </script>
    <?php
    if (is_array($threads) && count($threads) > 0) {
        $DTZ = new DateTimeZone('Europe/Paris');
        foreach ($threads as $thread) {
            if (($thread['studentOnly'] == '0') || ($thread['studentOnly'] == '1' && !acl_has_moderated_album()) || acl_is_admin()) {
                $editDate = (get_lang() == 'fr') ? new DateTimeFrench($thread['lastEditDate'], $DTZ) : new DateTime($thread['lastEditDate'], $DTZ);
                $editDateVerbose = (get_lang() == 'fr') ? $editDate->format('j F Y à H\hi') : $editDate->format("F j, Y, g:i a"); ?>
                <script>
                    if (typeof threads_array[<?php echo json_encode($thread['timecode']); ?>] == "undefined")
                        threads_array[<?php echo json_encode($thread['timecode']); ?>] = new Array();
                        
                    threads_array[<?php echo $thread['timecode']; ?>][<?php echo $thread['id']; ?>] = "<?php echo $thread['title']; ?>";
                </script>
                <div class="item-thread" onclick="show_thread_details(event,<?php echo $thread['id'] ?>)">
                    <div class="item-thread-content">
                        <?php if ($thread['studentOnly'] == '1') {
                    ?>
                            <img src="images/Generale/visibility-students.png" title="®Visibility_students®" class="visibility"/>
                        <?php
                } else {
                    ?>
                            <img src="images/Generale/visibility-all.png" title="®Visibility_all®" class="visibility"/>
                        <?php
                } ?>
                        <a class="item_link" href="javascript:player_video_seek(<?php echo $thread['timecode'] ?>, '<?php echo (isset($thread['type'])) ? $thread['type'] : ''; ?>');">  
                            <span class="timecode green2 inline-block"><?php print_time($thread['timecode']); ?> </span>
                        </a>
                        <i class="slash item-thread-slash">//</i>
                        <span style="font-style: italic; font-size: 11px;">®By® <?php echo $thread['authorFullName']; ?> </span>
                        <?php if ($thread['nbComments'] > 0) {
                    ?>
                            <div  class="item-thread-more item-thread-nbcomments" title="®Thread_nbComments®"><?php echo $thread['nbComments'] ?></div>
                        <?php
                } ?>
                        <label class="item-thread-title" ><?php echo $thread['title']; ?></label>
                        <span class="item-thread-author">
                            ®Edited_on® <b><?php echo $editDateVerbose; ?></b>
                            ®By® <?php echo (isset($thread['lastEditAuthor']) && trim($thread['lastEditAuthor']) != '') ? $thread['lastEditAuthor'] : $thread['authorFullName']; ?>
                        </span>
                        <a class="more-button-small more-button-<?php echo $thread['id']; ?> item-thread-more" 
                           onclick="thread_more_toggle(<?php echo $thread['id'] ?>)">
                        </a>
                        <br/>
                        <div class="hidden-item-thread" id="hidden-item-thread-<?php echo $thread['id']; ?>">
                            <?php echo print_info(htmlspecialchars_decode($thread['message'], ENT_QUOTES), '', false); ?>     
                        </div>
                    </div>
                    <div class="eom"></div>
                </div>
                <?php
            }
        }
    } else {
        ?>
        <span style='padding-left: 48px;'>®No_thread®</span>
        <?php
    }
    ?>

</div>
