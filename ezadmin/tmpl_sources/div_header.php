<div class="header">
    <div class="header_content">
        <div class="logo"> 
            <?php if (file_exists("./htdocs/img/organization-logo.png")) { ?>
                <a href="<?php
                global $organization_url;
                echo $organization_url;
                ?>"><img src="./img/organization-logo.png" height="42px;"/></a>
               <?php } ?>
            <a href="index.php"><img src="./img/ezadmin.png" alt="" height="42px;"/></a>
            <?php if ($_SESSION['changes_to_push']) echo '<small class="badge badge-important" title="®unsaved_changes®">!</small>'; ?>
        </div>
    </div>
</div>