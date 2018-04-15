<?php
//Get latest scheduler logs giving latest potential error with renderers
function index($param = array())
{
    global $config;
    $tail = shell_exec("bash -c 'tail -100 '".$config['paths']['logs']."'");
    $tail_array=explode("\n",$tail);
    require_once template_getpath('div_main_header.php');
    require_once template_getpath('div_renderer_logs.php');
    require_once template_getpath('div_main_footer.php');
}
