<?php
/**
 * @package ezcast.ezadmin.test
 */

include '../commons/lib_database.php';

db_prepare();
var_dump(db_log('test', 'Test logging', 'test user'));
var_dump($statements['log_action']->errorInfo());
?>
