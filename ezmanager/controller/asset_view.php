<?php

require_once(__DIR__ . '/includes/asset_view.php');

/**
 * This function shows the asset details div for the asset passed by POST, GET or SESSION
 * @global type $input
 * @global type $repository_path
 */
function index($param = array())
{
    asset_view();
}
