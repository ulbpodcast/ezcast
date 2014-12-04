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

include_once 'lib_print.php';

$DTZ = new DateTimeZone('Europe/Paris');
require template_getpath('popup_delete_thread.php');
?>
<a class="item_link right back-to-threads" href="javascript:threads_list_update()">®Display_discussions®</a>
<div class="thread_header">
    <span class="thread-logo details"></span>
    <span class="thread-title inline-block"><?php echo $thread['title']; ?></span> 
    <a class="refresh-button pull-right" title="®Refresh_discussion_details®" href="javascript:thread_details_update(<?php echo $thread['id']; ?>)"></a>
    <br/>
    <?php
    $creationDateFr_msg = new DateTimeFrench($thread['creationDate'], $DTZ);
    $creationDateFrVerbose_msg = $creationDateFr_msg->format('j F Y');
    ?>
    <span id="thread_author" class="darkgray-label">
        <b><label><?php echo $thread['authorFullName']; ?></label></b> 
        <i class="white"> // </i>
        <label> <?php echo $creationDateFrVerbose_msg; ?></label>
        <i class="white"> // </i>
        <label><?php echo substr($thread['creationDate'], 11, 8); ?></label>
    </span>
</div>
<div id="message-thread">
    <?php echo nl2br(html_entity_decode($thread['message'])); ?>
</div>

<div class="form" id="edit_thread_form_<?php echo $thread['id']; ?>" hidden>
    <div id='thread_form_wrapper'>
        <form action="index.php" method="post" id="submit_thread_form" onsubmit="return false">
            <input type="hidden" name="album" id="thread_album" value="<?php echo $thread['albumName']; ?>"/>
            <input type="hidden" name="asset" id="thread_asset" value="<?php echo $thread['assetName']; ?>"/>

            <br/>
            <div class="full-width">
                <label>®Title®&nbsp;:
                    <span class="small">®Title_info®</span>
                </label>
                <input name="title" id="edit_thread_title_<?php echo $thread['id']; ?>" 
                       type="text" placeholder="®Discussion_title_placeholder®" style="width: 75%" 
                       maxlength="70" value="<?php echo $thread['title']; ?>"/>
            </div>
            <div class="message-input">
                <label>®Message®&nbsp;:
                    <span class="small">®Required®</span>
                </label>
                <textarea name="message" id="edit_thread_message_<?php echo $thread['id']; ?>_tinyeditor"><?php echo $thread['message'] ?></textarea>
            </div>
            <div class="message-input">
                <label>®Timecode®&nbsp;:
                    <span class="small">®Timecode_info®</span>
                </label>
                <input name="timecode" id="edit_thread_timecode_<?php echo $thread['id']; ?>" style="width: 75%" 
                       value="<?php echo $thread['timecode']; ?>" type="text" value="0"/>
            </div>
            <br/>

            <!-- Submit button -->
            <div class="cancelButton" style="margin-left: 428px;">
                <a class="button" tabindex='16' href="javascript: cancel_edit_thread(<?php echo $thread['id']; ?>,<?php echo '\'' . str_replace("'", "\'", $thread['title']) . '\''; ?>,<?php echo '\'' . str_replace("'", "\'", $thread['message']) . '\''; ?>, <?php echo $thread['timecode']; ?>);">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a class="button green2" tabindex='17' href="javascript: if(check_edit_thread_form(<?php echo $thread['id']; ?>)) submit_edit_thread_form(<?php echo $thread['id']; ?>,'<?php echo $thread['albumName']; ?>','<?php echo $thread['assetName']; ?>');">®Update®</a>
            </div>
            <br />
        </form>
    </div>
</div>
<div id="thread-options" class="right-options">
    <a class="button-empty green2 pull-right inline-block" href="javascript:toggle_comment_form();" >
        ®Reply_discussion®
    </a>
    <?php if (($_SESSION['user_login'] == $thread['authorId']) || acl_is_admin()) { ?>
        <a class="edit-button green2 pull-right inline-block" title="®Edit_discussion®" onclick="edit_asset_thread(<?php echo $thread['id'] ?>)"></a>
        <?php if (acl_is_admin()) {
            ?>     
            <a class="delete-button green2 pull-right inline-block" title="®Delete_discussion®" data-reveal-id="popup_delete_thread_<?php echo $thread['id']; ?>" ></a>
        <?php }
    }
    ?>
</div>

<br/><br/>

<?php if (count($thread['comments']) != 0) { ?>
    <?php if ($thread['best_comment'] != NULL) {
        $best_comment = $thread['best_comment'];
        ?>
        <div class="cat">
            <label class="thread-cat-title"><?php echo mb_strtoupper('®Best_answer®', 'UTF-8'); ?></label>
        </div>
        <div class="best_reply">
            <?php
            $creationDateFr_msg = new DateTimeFrench($best_comment['creationDate'], $DTZ);
            $creationDateFrVerbose_msg = $creationDateFr_msg->format('j F Y');
            ?>
            <span class="comment_author" class="darkgray-label">
                <b><label><?php echo $best_comment['authorFullName']; ?></label></b> 
                <i class="slash-sm"> // </i>
                <span class="date-sm">
                    <label> <?php echo $creationDateFrVerbose_msg; ?></label>
                    <i class="slash-sm"> // </i>
                    <label><?php echo substr($best_comment['creationDate'], 11, 8); ?></label>
                </span>
            </span>
            <div id="best-comment-message" onclick="javascript:scrollTo('comment_<?php echo $best_comment['id']; ?>');" style="cursor: pointer;">
        <?php echo nl2br(html_entity_decode($best_comment['message'])); ?>
            </div>
            <label class="pull-left badge-score"><?php echo sprintf("%02s", $best_comment['score']); ?></label>
            <div class="trophy-best-comment pull-left"></div>
        </div>
        <br />
    <?php } ?>
    <div class="cat">
        <label class="thread-cat-title"><?php echo mb_strtoupper('®Other_answers®', 'UTF-8'); ?></label>
    </div>
    <div id="comments_list">
        <?php
        if (is_array($thread['comments'])) {
            $thread_main_comments = get_main_comments($thread['comments']);
            $i = 0;
            foreach ($thread_main_comments as $comment) {
                if ($i != 0)
                    echo '<div class="eom"></div>';
                require template_getpath('div_comment.php');
                $i++;
                $childs = get_comment_childs($thread['comments'], $comment);
                foreach ($childs as $comment) {
                    ?>
                    <div  class="<?php echo $comment['level'] ?>">
                        <div class="eom"></div>
                        <?php
                        require template_getpath('div_comment.php');
                        ?>
                    </div>
                    <?php
                }
            }
        }
        ?>

    </div>
    <?php
}
?>
<div class="form" id="comment_form">
    <div id='comment_form_wrapper'>
        <form action="index.php" method="post" id="submit_comment_form" onsubmit="return false">
            <input type="hidden" name="thread_id" id="thread_id" value="<?php echo $thread['id']; ?>"/>
            <input type="hidden" name="album" id="thread_album" value="<?php echo $thread['albumName']; ?>"/>
            <input type="hidden" name="asset" id="thread_asset" value="<?php echo $thread['assetName']; ?>"/>
            <textarea name="message" tabindex='12' id="comment_message_tinyeditor" required></textarea>

            <br/>
            <!-- Submit button -->
            <div class="cancelButton" style="margin-left: 464px;">
                <a class="button" tabindex='16' href="javascript: hide_comment_form();">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a class="button green2" tabindex='17' href="javascript: if(check_comment_form()) submit_comment_form();">®Reply®</a>
            </div>
            <br />
        </form>
    </div>
</div>
