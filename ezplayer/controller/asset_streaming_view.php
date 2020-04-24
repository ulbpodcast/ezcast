<?php

require_once(__DIR__."/../lib_streaming.php");
            
function index($param = array())
{
    $refresh_center = (count($param) == 0 || $param[0]);
    asset_streaming_view($refresh_center);
}
