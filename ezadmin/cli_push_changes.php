<?php

require_once(__DIR__.'/lib_push_changes.php');
echo "Pushing..." . PHP_EOL;
$failed_cmd = push_changes();
var_dump($failed_cmd);
echo "Finished" . PHP_EOL;

