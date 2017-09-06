<?php

require_once(__DIR__."/../lib_streaming.php");

function index($param = array())
{
    $display = (count($param) == 0 || $param[0]);
    asset_streaming_player_update($display);
}
