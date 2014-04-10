<!-- 
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
-->

<div id="popup_copy_bookmark_<?php echo $index ?>" class="reveal-modal left">
    <h2><b style="text-transform:uppercase;"><?php echo print_bookmark_title($bookmark['title']); ?></b></h2>
    <h3><?php echo get_asset_title($bookmark['album'], $bookmark['asset']); ?></h3>
    <br/><p>®Copy_bookmark_message®</p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    <a href="javascript:copy_bookmark('<?php echo $bookmark['album'] ?>', '<?php echo $bookmark['asset'] ?>', '<?php echo $bookmark['timecode'] ?>', '<?php echo htmlspecialchars($bookmark['title']) ?>', '<?php echo htmlspecialchars($bookmark['description']) ?>', '<?php echo htmlspecialchars($bookmark['keywords']) ?>', '<?php echo $bookmark['level'] ?>', '<?php echo $call ?>', 'custom');" class="simple-button blue">®Copy®</a>
    <a class="close-reveal-modal-button">®Cancel®</a>
</div>
