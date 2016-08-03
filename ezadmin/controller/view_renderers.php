<?php

function index($param = array()) {
    if (!file_exists('renderers.inc')) {
        $renderers = array();
    } else {
        $renderers = require_once 'renderers.inc';
    }

    require_once template_getpath('div_main_header.php');
    require_once template_getpath('div_list_renderers.php');
    require_once template_getpath('div_main_footer.php');
}
