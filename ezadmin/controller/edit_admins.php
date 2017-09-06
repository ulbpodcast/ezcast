<?php

function index($param = array())
{
    global $input;

    $admins = parse_admin_file();
    //update_config_file(false, true, true);

    include template_getpath('div_main_header.php');
    include template_getpath('div_list_admins.php');
    include template_getpath('div_main_footer.php');
}
