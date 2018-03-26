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

<div class="header">
    <div class="header_content">
        <div class="logo"> 
            <?php if (file_exists('./htdocs/images/Header/organization-logo.png')) { ?>    
                <a href="<?php global $organization_url;
                echo $organization_url; ?>"><img id="organisation_logo" src="./images/Header/organization-logo.png"/></a>
            <?php } ?>

            <?php
                global $ezplayer_custom_logo;
                $ezplayer_logo = !empty($ezplayer_custom_logo) ? "images/custom/$ezplayer_custom_logo" : "images/Header/LogoEZplayer.png"; //default value
            ?>
            <a href="index.php" title="®Back_to_home®"><img src="<?php echo $ezplayer_logo; ?>" /></a>
        </div>      
    </div>
</div>
