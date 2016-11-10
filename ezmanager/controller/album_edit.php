<?php


function index($param = array()) {
    global $input;
    global $repository_path;

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['moderation'])) {
        echo "Usage: index.php?action=edit_album&session=SESSION_ID&intro=INTRO&addTitle=ADD_TITLE&credits=CREDITS";
        die;
    }

    if ($input['moderation'] == true) {
        $album = $input['album'] . '-priv';
    } else {
        $album = $input['album'] . '-pub';
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($album)) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Then we update the metadata
    //
    $album_meta = ezmam_album_metadata_get($album);

    $album_meta['intro'] = $input['intro'];
    if(isset($input['credits']))
        $album_meta['credits'] = $input['credits'];
    $album_meta['add_title'] = $input['add_title'];
    $album_meta['downloadable'] = $input['downloadable'];

    $res = ezmam_album_metadata_set($album, $album_meta);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //  view_main();
    require_once template_getpath('popup_album_successfully_edited.php');
}