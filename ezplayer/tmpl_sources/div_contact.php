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
<h2>®Contact_h2®</h2>
<br/>
<p>®Contact_message®</p>
<br/>

<form name="submit_contact_form" action="<?php
global $ezplayer_safe_url;
echo $ezplayer_safe_url;
?>/index.php" method="post">
    <input type="hidden" name="action" value="contact_send" />

    <!-- Title field -->  
    <div style='clear: both'>
        <label>®Email_address®&nbsp;:
            <span class="small">®optional®</span>
        </label>
        <input name="email" tabindex='11' id="contact_email" type="text" maxlength="70"/>
    </div>


    <!-- keywords field -->
    <div style='clear: both'>
        <label>®Subject®&nbsp;:
        </label>
        <select name='subject'>
            <option value="®Missing_album®">®Missing_album®</option>
            <option value="®Missing_recording®">®Missing_recording®</option>
            <option value="®No_sound®">®No_sound®</option>
            <option value="®Video_problem®">®Video_problem®</option>
            <option value="®Thread_problem®">®Thread_problem®</option>
            <option value="®Bookmark_problem®">®Bookmark_problem®</option>
            <option value="®Bug®">®Bug®</option>
            <option value="®Help_contact®">®Help_contact®</option>
            <option value="®Feature®">®Feature®</option>
            <option value="®Other®">®Other®</option>
        </select>
    </div>

    <!-- Description field -->
    <div style='clear: both'>
        <label>®Message®&nbsp;:
            <span class="small">®Required®</span>
        </label>
        <textarea name="message" tabindex='12' id="contact_message" rows="4" ></textarea>
    </div>

    <div style='clear: both; margin-left: 130px;'>
        <a class="button-empty green" href="javascript: header_form_hide('contact');">®Cancel®</a>
        <a class="button green" href="#" onclick="document.submit_contact_form.submit();">®Send®</a>
    </div>

</form>
