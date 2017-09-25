<div class="header">
    <div class="header_content">
        <div class="logo"> 
            <?php if (file_exists("./htdocs/img/organization-logo.png")) {
    ?>
                <a class="hidden-print-link" href="<?php
                global $organization_url;
    echo $organization_url; ?>"><img id="organisation_logo" src="./img/organization-logo.png"/></a>
               <?php
}
               
               
            global $ezadmin_custom_logo;
            $ezadmin_logo = !empty($ezadmin_custom_logo) ?
                    "img/custom/$ezadmin_custom_logo" :
                    "img/ezadmin.png"; //default value
            ?>
            <a href="index.php" title="®Back_to_home®"><img src="<?php echo $ezadmin_logo; ?>" /></a>   
               
            <?php if (isset($_SESSION['changes_to_push'])) {
                echo '<small class="badge badge-important" title="®unsaved_changes®">!</small>';
            } ?>
        </div>
    </div>
</div>