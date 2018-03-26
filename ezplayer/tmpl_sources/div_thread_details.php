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
<script>
<?php if ($_SESSION['ezplayer_mode'] == 'view_asset_bookmark') {
    ?>
        threads_array = new Array();

    <?php
    if (isset($threads) && is_array($threads) && count($threads) > 0) {
        foreach ($threads as $thread_meta) {
            if (($thread_meta['studentOnly'] == '0') || ($thread_meta['studentOnly'] == '1' && !acl_has_moderated_album()) || acl_is_admin()) {
                ?>
                    if (typeof threads_array[<?php echo json_encode($thread_meta['timecode']); ?>] == "undefined")
                        threads_array[<?php echo json_encode($thread_meta['timecode']); ?>] = new Array();
                    threads_array[<?php echo $thread_meta['timecode']; ?>][<?php echo $thread_meta['id']; ?>] = "<?php echo $thread_meta['title']; ?>";
                <?php
            }
        }
    }
}

$DTZ = new DateTimeZone('Europe/Paris');
?>
</script>
<div class="thread_header">
    <span class="visibility-logo <?php echo ($thread['studentOnly']) ? 'students' : 'all' ?> details" title="<?php 
        echo ($thread['studentOnly']) ? '®Visibility_students®' : '®Visibility_all®' ?>"></span>
    <a class="thread_timecode" href="javascript:player_video_seek(<?php echo $thread['timecode'] ?>, '');">  
        <span class="timecode white inline-block">(<?php print_time($thread['timecode']); ?>) </span>
    </a>
    <span class="thread-title inline-block"><?php echo $thread['title']; ?></span> 
    <a class="refresh-button pull-right" title="®Refresh_discussion_details®" href="javascript:thread_details_update(<?php echo $thread['id']; ?>, false)"></a>
    <a class="back-button pull-right" title="®Display_discussions®" href="javascript:threads_list_update(false);"></a>
    <br/>
    <?php
    $creationDate = (get_lang() == 'fr') ? new DateTimeFrench($thread['creationDate'], $DTZ) : new DateTime($thread['creationDate'], $DTZ);
    $creationDateVerbose = (get_lang() == 'fr') ? $creationDate->format('j F Y à H\hi') : $creationDate->format("F j, Y, g:i a");
    ?>
</div>
<span id="thread_author" class="darkgray-label">
    <span style="font-style: italic; font-size: 11px;"><?php echo $thread['authorFullName']; ?></span> 
    <i class="slash item-thread-slash">//</i>
    ®On_date® <b><?php echo $creationDateVerbose; ?></b>
</span>
<div id="message-thread">
    <?php echo print_info(htmlspecialchars_decode($thread['message'], ENT_QUOTES), '', false); ?>
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
            <div class="message-input edit_msg">
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
                <a class="button" tabindex='16' href="javascript: thread_edit_form_cancel(<?php echo $thread['id']; ?>);">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a class="button green2" tabindex='17' href="javascript: if(thread_edit_form_check(<?php echo $thread['id']; ?>)) thread_edit_form_submit(<?php echo $thread['id']; ?>,'<?php echo $thread['albumName']; ?>','<?php echo $thread['assetName']; ?>');">
                    ®Update®
                </a>
            </div>
            <br />
        </form>
    </div>
</div>
<div id="thread-options" class="right-options">
    <?php if (acl_user_is_logged()) {
                        ?>
    <a class="button-empty green2 pull-right inline-block" href="javascript:thread_comment_form_toggle();" >
        ®Reply_discussion®
    </a>
    <?php
                    } ?>
    <?php if (($_SESSION['user_login'] == $thread['authorId']) || acl_is_admin()) {
                        ?>
        <a class="edit-button green2 pull-right inline-block" title="®Edit_discussion®" onclick="thread_edit_form_prepare(<?php echo $thread['id'] ?>)"></a>
        <?php if (acl_is_admin()) {
                            ?>     
            <a class="delete-button green2 pull-right inline-block" title="®Delete_discussion®" 
               href="javascript:popup_thread('<?php echo $thread['id']; ?>', 'delete');" ></a>
<?php
                        }
                    }
?>
</div>

<br/><br/>

<?php if (count($thread['comments']) != 0) {
    ?>
    <?php
    if ($thread['best_comment'] != null) {
        $best_comment = $thread['best_comment']; ?>
        <div class="cat">
            <label class="thread-cat-title"><?php echo mb_strtoupper('®Best_answer®', 'UTF-8'); ?></label>
        </div>
        <div class="best_reply">
            <div id="best-comment-message" onclick="javascript:scrollTo('comment_<?php echo $best_comment['id']; ?>');" style="cursor: pointer;">
                <?php echo print_info(htmlspecialchars_decode($best_comment['message'], ENT_QUOTES), '', false); ?>
            </div>
            <label class="pull-left badge-score"><?php echo sprintf("%02s", $best_comment['score']); ?></label>
            <div class="trophy-best-comment pull-left"></div>
        </div>
        <br />
    <?php
    } ?>
    <div class="cat">
        <label class="thread-cat-title"><?php echo mb_strtoupper('®Other_answers®', 'UTF-8'); ?></label>
    </div>
    <div id="comments_list">
        <?php
        if (is_array($thread['comments'])) {
            $thread_main_comments = get_main_comments($thread['comments']);
            $i = 0;
            foreach ($thread_main_comments as $comment) {
                if ($i != 0) {
                    echo '<div class="eom"></div>';
                }
                require template_getpath('div_comment.php');
                $i++;
                $childs = get_comment_childs($thread['comments'], $comment);
                foreach ($childs as $comment) {
                    ?>
                    <div  class="<?php echo $comment['level'] ?>">
                        <div class="eom"></div>
                        <?php
                        require template_getpath('div_comment.php'); ?>
                    </div>
                    <?php
                }
            }
        } ?>

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
                <a class="button" tabindex='16' href="javascript:thread_comment_form_hide();">®Cancel®</a>
            </div>
            <div class="submitButton">
                <a class="button green2" tabindex='17' href="javascript: if(thread_comment_form_check()) thread_comment_form_submit();">®Reply®</a>
            </div>
            <br />
        </form>
    </div>
</div>
