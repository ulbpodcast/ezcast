<?php

$common_config_defined=false;
require_once __DIR__.'/../commons/common.inc';
global $ezrecorder_need_files_pushed_path;
if(file_exists($ezrecorder_need_files_pushed_path)){
    exec(" php /usr/local/ezcast/ezadmin/cli_push_changes.php > /dev/null 2>/dev/null &");
}