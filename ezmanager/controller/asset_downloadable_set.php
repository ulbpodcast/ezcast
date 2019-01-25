<?php

function index($param = array())
{
    global $input;
    global $repository_path;

    if (!acl_session_key_check($input['sesskey'])) {
        echo "Error: Session key is not valid";
        die;
    }

    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['downloadable'])) {
        die;
    }

    ezmam_repository_path($repository_path);

    $metadata = ezmam_asset_metadata_get($input['album'], $input['asset']);

    $metadata['downloadable'] = $input['downloadable'];

    $res = ezmam_asset_metadata_set($input['album'], $input['asset'], $metadata);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
}
