<?php

function index($param = array())
{
    $renderers_file = __DIR__ . '/../../commons/renderers.inc';
    if (!file_exists($renderers_file)) {
        $renderers = array();
    } else {
        $renderers = require_once $renderers_file;
    }

    require_once template_getpath('div_main_header.php');
    require_once template_getpath('div_list_renderers.php');
    require_once template_getpath('div_main_footer.php');
}
