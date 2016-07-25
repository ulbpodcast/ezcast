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

<div id="popup_login" class="reveal-modal down">
    <h2>®connect®</h2>
    <br/><p>®connect_message®</p>
    <a class="close-reveal-modal">&#215;</a>
    <br/>
    <div class="error"><?php echo $login_error; ?></div><br/> 
    <form name="login_form" action="<?php global $ezplayer_safe_url; echo $ezplayer_safe_url; ?>/index.php" method="post">
        <input type="hidden" name="action" value="anonymous_login" />
        <label>Netid :&nbsp;</label><input type="text" name="login"  autocapitalize="off" autocorrect="off" />
        <br/><br/>
        <label>Password :&nbsp;</label><input type="password" name="passwd" autocorrect="off" autocapitalize="off" />
        <br />
        <a href="#" onclick="document.login_form.submit(); return false;" class="simple-button" title="®Login_title®">®Login®</a>
        <a class="close-reveal-modal-button">®Cancel®</a>
        <br />
    </form>
</div>
 