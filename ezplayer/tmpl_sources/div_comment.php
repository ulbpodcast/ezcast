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


$creationDate = (get_lang() == 'fr') ? new DateTimeFrench($comment['creationDate'], $DTZ) : new DateTime($comment['creationDate'], $DTZ);
$creationDateVerbose = (get_lang() == 'fr') ? $creationDate->format('j F Y à H\hi') : $creationDate->format("F j, Y, g:i a");
require template_getpath('popup_delete_comment.php');
?>
<span id="comment_<?php echo $comment['id']; ?>">
    <form action="index.php" method="POST">
        <?php if ($comment['approval'] == '1') { ?>
            <div class="ribbon-img inline-block pull-right"></div>
            <div class="orange-title sm"><?php echo mb_strtoupper("®Professor_approved®", "UTF-8") ?><i class="slash-sm orange"> // </i></div>
        <?php } ?>
        <span class="comment_author" class="darkgray-label">
            <span style="font-style: italic; font-size: 11px;"><?php echo $comment['authorFullName']; ?></span> 
            <i class="slash item-thread-slash">//</i>
            ®On_date® <b><?php echo $creationDateVerbose; ?></b>
        </span>
        <br/>
        <br/>

        <div class="comment-message" id="comment_message_id_<?php echo $comment['id']; ?>"><?php echo nl2br(html_entity_decode($comment['message'])); ?></div>

        <span id="edit_comment_<?php echo $comment['id']; ?>" hidden>
            <textarea name="message" id="edit_comment_message_<?php echo $comment['id']; ?>_tinyeditor"><?php echo $comment['message']; ?></textarea>
            <input type="hidden" name="album" id="edit_comment_album" value="<?php echo $thread['albumName']; ?>"/>
            <input type="hidden" name="asset" id="edit_comment_asset" value="<?php echo $thread['assetName']; ?>"/>
            <input type="hidden" name="thread" id="edit_comment_thread" value="<?php echo $thread['id']; ?>"/>
        </span>

        <br/>
        <!-- Submit button -->
        <div id="edit-options-<?php echo $comment['id']; ?>" hidden>
            <a class="button" href="javascript: cancel_edit_comment(<?php echo $comment['id'] ?>, <?php echo '\'' . str_replace("'", "\'", $comment['message']) . '\''; ?>);">®Cancel®</a>
            <a class="button green2" href="javascript: submit_edit_comment_form(<?php echo $comment['id']; ?>);">®Submit®</a>
        </div>
        <br />

        <!-- ----- BEGIN - ANSWER FORM --------------------- -->
        <div class="form" id="answer_comment_form_<?php echo $comment['id']; ?>" hidden>
            <div id='answer_comment_form_wrapper'>
                <form action="index.php" method="post" id="submit_answer_comment_form_<?php echo $comment['id']; ?>">
                    <input type="hidden" name="parent" id="answer_parent_<?php echo $comment['id']; ?>" value="<?php echo $comment['id']; ?>"/>
                    <input type="hidden" name="thread" id="answer_thread_<?php echo $comment['id']; ?>" value="<?php echo $comment['thread']; ?>"/>
                    <input type="hidden" name="nbChilds" id="answer_nbChilds_<?php echo $comment['id']; ?>" value="<?php echo $comment['nbChilds']; ?>"/>
                    <input type="hidden" name="album" id="answer_album" value="<?php echo $thread['albumName']; ?>"/>
                    <input type="hidden" name="asset" id="answer_asset" value="<?php echo $thread['assetName']; ?>"/>
                    <br/>
                    <textarea name="answer_message" class="answer_textarea" id="answer_comment_message_<?php echo $comment['id']; ?>_tinyeditor" rows="4" required style="margin-left: 36px;"></textarea>
                    <br/>

                    <!-- Submit button -->
                    <div class="cancelButton" style="margin-left: 300px;">
                        <a class="button" href="javascript: hide_answer_comment_form(<?php echo $comment['id']; ?>);">®Cancel®</a>
                    </div>
                    <div class="submitButton">
                        <a class="button green2" href="javascript: if(check_answer_comment_form(<?php echo $comment['id']; ?>)) submit_answer_comment_form(<?php echo $comment['id']; ?>);">®Reply®</a>
                    </div>
                    <br />
                </form>
            </div>
        </div>
        <!-- --- END - ANSWER FORM ------------------------- -->

        <span class="comment-options">
            <!-- ----- VOTE ------------------------------------ -->
            <div class="inline-block">
                <div class="upvote-button pull-left inline-block" onclick="javascript:vote('<?php echo $_SESSION['user_login'] ?>', <?php echo $comment['id'] ?>, <?php echo '0' ?>);" ></div>
                <label class="pull-left badge-score"><?php echo sprintf("%02s", $comment['score']); ?></label>
                <div class="downvote-button pull-left inline-block" onclick="javascript:vote('<?php echo $_SESSION['user_login'] ?>', <?php echo $comment['id'] ?>, <?php echo '1' ?>);"></div>
                <?php if (acl_has_album_moderation($album) || acl_is_admin()) { ?>
                    <div style="padding-top: 5px;" class="copy-button <?php echo ($comment['approval'] == '0') ? '' : 'active' ?> inline-block" title="<?php echo ($comment['approval'] == '0') ? '®Answer_approval®' : '®Withdraw_approval®' ?>" onclick="javascript:approve(<?php echo $comment['id']; ?>)"></div>
                <?php } ?>
            </div>
            <!-- --- END - VOTE -------------------------------- -->

            <div class="right-options">
                <a class="button-empty green2 pull-right inline-block" onclick="javascript:show_answer_comment_form('<?php echo $comment['id']; ?>');">®Reply®</a>
                <?php if ($_SESSION['user_login'] == $comment['authorId'] || acl_is_admin()) { ?>
                    <div class="inline-block">
                        <a class="edit-button green2 pull-right inline-block" title="®Edit_comment®" onclick="javascript:edit_thread_comment('<?php echo $comment['id']; ?>');"></a>
                        <?php if (acl_is_admin()) { ?>
                            <a class="delete-button green2 pull-right inline-block" title="®Delete_comment®" data-reveal-id="popup_delete_comment_<?php echo $comment['id']; ?>"></a> 
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>      
            </div>
        </span>
        <br/>
        <br/>
    </form>
</span>

