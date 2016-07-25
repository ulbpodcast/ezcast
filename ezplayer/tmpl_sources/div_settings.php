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

acl_update_settings();
?>
<h2>®Preferences®</h2>
<form name="submit_settings_form" action="<?php global $ezplayer_safe_url;
echo $ezplayer_safe_url; ?>/index.php" method="post">
    <input type="hidden" name="action" value="settings_update" />
    <table>
        <th><span class="title left">Notifications</span></th><br/>
        <tr>
            <td><span>®Notification_new_video®</span><td>
            <td><input id="settings_notif_new_asset" name="display_new_video_notification" 
                       type="checkbox" <?php echo acl_show_notifications() ? 'checked' : '' ?>/></td>
        </tr>
        <tr>
            <td><span>®Notification_during_video_playback®</span><td>
            <td><input id="settings_notif_threads" name="display_thread_notification" 
                       type="checkbox" <?php echo acl_display_thread_notification() ? 'checked' : '' ?>/></td>
        </tr>
        <th><span class="title left">®General®</span></th><br/>
        <tr>
            <td><span>®Show_discussions®</span><td>
            <td><input id="settings_display_threads" name="display_threads" 
                       type="checkbox" <?php echo acl_display_threads() ? 'checked' : '' ?>/></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a class="button-empty green" href="javascript: header_form_hide('settings');">®Cancel®</a>
            </td>
            <td>
                <a class="button green" href="#" onclick="document.submit_settings_form.submit();">®Update®</a>
            </td>
        </tr>

    </table>
</form>
