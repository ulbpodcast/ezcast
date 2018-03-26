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

<?php if (!isset($_COOKIE['infos_cookie'])) {
    ?>
    <div class="cookie_header">
        ®cookie_infos®
        <button onclick="document.cookie='infos_cookie=true;expires=Fri, 31 Dec 9999 23:59:59 GMT';$('.cookie_header').hide()">OK</button>
    </div>
<?php
} ?>


<script>
    settings_form = false;
    contact_form = false;
</script>
<div class="header">
    <div class="header_content">
        <div class="logo"> 
            <?php  //if a custom organisation logo was defined, try to load it
                if (file_exists('./htdocs/images/Header/organization-logo.png')) {
                    ?>
                <a href="<?php
                global $organization_url;
                    echo $organization_url; ?>"><img id="organisation_logo" src="images/Header/organization-logo.png"/></a>
            <?php
                }
            
            global $ezplayer_custom_logo;
            $ezplayer_logo = !empty($ezplayer_custom_logo) ?
                    "images/custom/$ezplayer_custom_logo" :
                    "images/Header/LogoEZplayer.png"; //default value
            ?>
            <a href="index.php" title="®Back_to_home®"><img src="<?php echo $ezplayer_logo; ?>" /></a>
        </div>
        <!-- S E T T I N G S -->
        <div class="form" id="settings_form">
            <div id='settings_form_wrapper'>
<?php include_once template_getpath('div_settings.php'); ?>
            </div>
        </div>

        <!-- C O N T A C T -->
        <div class="form" id="contact_form">
            <div id='contact_form_wrapper'>
                <?php include_once template_getpath('div_contact.php'); ?>
            </div>
        </div>

        <?php if (acl_user_is_logged()) {
                ?>
            <a href="index.php?action=logout" title="®Logout_title®">
                <span class="logout">®Logout®</span>
            </a>
        <?php
        } else {
        ?>
            <a href="index.php?loging" title="®Login_title®">
                <span class="anonym_login">®Login®</span>
                
            </a>
        <?php
            } ?>       
        <span style="float: right; margin: 1px 3px; font-size: 15px;">|</span>
        <a href="index.php?action=view_help" target="_blank" title="®Help_title®">
            <span class="logout green">®Help®</span>
        </a>
        <?php if (acl_user_is_logged()) {
                ?>
            <span style="float: right; margin: 1px 3px; font-size: 15px;">|</span>
            <a id="contact" onclick="javascript:header_form_toggle('contact')" title="®Contact_title®"></a>  
            <a id="user-settings" class="pull-right" onclick="javascript:header_form_toggle('settings');" title="®Preferences_title®">
                <span>®Preferences®</span> 
            </a>      
        <?php
            }
        
        if (acl_admin_user()) {
            ?>
            <span style="float: right; margin: 1px 3px; font-size: 15px;">|</span>
            <a href="javascript:admin_mode_update()" title="®Admin_mode_update®">
                <span class="logout"><?php echo acl_is_admin() ? '®Admin_mode_enabled®' : '®Admin_mode_disabled®'; ?></span>
            </a>

        <?php
        }
        if (acl_runas()) {
            ?>
            <span style="float: right; margin: 1px 3px; font-size: 15px;">|</span>
            <span class="logout">®connected_as® <b><?php echo $_SESSION['user_full_name']; ?></b></span>
        <?php
        } ?>
    </div>
</div>
<?php if (!acl_user_is_logged()) {
            ?>
    <script>
        $('#popup_login').reveal($(this).data());
    </script>
<?php
        } ?>