<?php
require_once __DIR__ .'/../../lib_ezmam.php';

/**
 * Displays the stast informations
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $distribute_url;
    global $ezplayer_url;
    global $enable_moderator;
    global $enable_anon_access_control;
    global $trace_on;

    if (!$trace_on || !$display_trace_stats) {
        die;
    }

    if (isset($input['album'])) {
        $album = $input['album'];
    } else {
        $album = $_SESSION['podman_album'];
    }
    $current_album = $album;

    ezmam_repository_path($repository_path);
    //
    // 0) Permissions checks
    //
    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', "view_album: tried to access album " . $album . ' without permission');
        die;
    }

    //
    // 1) We retrieve the metadata relating to the album
    //
    $metadata = ezmam_album_metadata_get($album);



  } ?>
