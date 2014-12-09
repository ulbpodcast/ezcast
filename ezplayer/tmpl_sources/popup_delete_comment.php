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
?>
<div id="popup_delete_comment_<?php echo $comment['id']; ?>" class="reveal-modal">
    <h3>®Delete_comment_title®</h3>
    <br/><p>®Delete_comment_message®</p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    <a href="javascript:delete_thread_comment(<?php echo $comment['thread'] ?>, <?php echo $comment['id'] ?>);" class="delete-button-confirm">®Delete®</a>
    <a class="close-reveal-modal-button">®Cancel®</a>
</div>
