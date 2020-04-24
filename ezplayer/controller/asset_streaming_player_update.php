<?php

require_once(__DIR__."/../lib_streaming.php");

function index($param = array())
{
	global $input;

	if (!acl_session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    $display = (count($param) == 0 || $param[0]);
    asset_streaming_player_update($display);
}
