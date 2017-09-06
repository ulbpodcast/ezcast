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
?>
<?php
include_once 'lib_print.php';

foreach ($chat_messages as $message) {
    $creationDate = (get_lang() == 'fr') ? new DateTimeFrench($message['creationDate'], $DTZ) : new DateTime($message['creationDate'], $DTZ);
    $creationDateVerbose = (get_lang() == 'fr') ? $creationDate->format('j F Y à H\hi') : $creationDate->format("F j, Y, g:i a"); ?> 
    <?php
    // checks if the hashtag #question is present
    if (($hashtag_pos = stripos($message['message'], '#question')) !== false) {
        // the question is added to the #questions tab
        $message['message'] = replace_hashtag($message['message'], '#question', 'javascript:chat_scroll_to(\'#chat_questions\');', $hashtag_pos); ?>
        <script>
            chat_question_append('<?php echo $message['authorFullName']; ?>', '<?php echo print_info(str_replace("'", "\'", htmlspecialchars_decode($message['message'], ENT_QUOTES)), '', false); ?>', '<?php echo $creationDateVerbose; ?>');
        </script>
        <?php
    } ?>

    <div class="chat_msg_wrapper  <?php
    if (acl_user_is_logged() && $message['authorId'] == $_SESSION['user_login']) {
        echo 'author ';
    } ?>">
        <div class="chat_msg_info">
            <span>
                <b><?php echo $message['authorFullName']; ?></b> 
            </span>
            <div class='chat_msg_date'>
                <i class="slash item-thread-slash">//</i>
                <span style="font-style: italic; font-size: 11px;"><?php echo $creationDateVerbose; ?></span>
            </div>
        </div>
        <div class="chat_msg" onclick="chat_date_toggle($(this))">
            <div class="chat_msg_txt">
                <?php echo print_info(htmlspecialchars_decode($message['message'], ENT_QUOTES), '', false); ?>
            </div>
        </div>
    </div>
    <?php
}
?>