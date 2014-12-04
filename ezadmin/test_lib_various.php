<?php
/**
 * @package ezcast.ezadmin.test
 */

require_once 'lib_various.php';
$user_name = "awijns";

remove_admin_from_file($user_name);
add_admin_to_file($user_name);
?>
