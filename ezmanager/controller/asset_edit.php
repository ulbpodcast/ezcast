<?php

require_once(__DIR__ . '/includes/asset_view.php');

/**
 * Edits asset data and re-draws the asset details
 * @global type $input
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $title_max_length;
    $title = rawurldecode($input['title']);

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['title'])) {
        echo "Usage: index.php?action=edit_asset&amp;album=ALBUM&amp;asset=ASSET&amp;title=NEW_TITLE";
        die;
    }

    if (!acl_session_key_check($input['sesskey'])) {
        echo $input['sesskey'];
        echo $_SESSION['sesskey'];
        echo "Usage: Session key is not valid";
        die;
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

     if (mb_strlen($title) > $title_max_length) {
//        error_print_message(template_get_message('Title_too_long', get_lang()));
        if(get_lang()=="fr")
            error_print_message("Titre trop long (max.  $title_max_length  caracteres)");
        else
            error_print_message("Title too long (max.  $title_max_length  characters)");

        asset_view();

        die;
    }

    //
    // Then we update the metadata
    //
    $metadata = ezmam_asset_metadata_get($input['album'], $input['asset']);

    $metadata['title'] = $title;
    $metadata['description'] = $input['description'];

    $res = ezmam_asset_metadata_set($input['album'], $input['asset'], $metadata);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // And we display the (new) asset details
    //
    asset_view();
}
