<?php

/**
 * Publishes or unpublishes an asset. The parameter $action tells us if we want the asset to be moved to the public 
 * ('publish') album or private ('unpublish') album.
 * 
 * @global type $input
 * @global type $repository_path
 * @param string $action publish|unpublish
 */
function index($param = array()) {
    global $input;
    global $repository_path;
    ezmam_repository_path($repository_path);

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset'])) {
        echo "Usage: index.php?action=publish_asset&amp;album=ALBUM&amp;asset=ASSET";
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    
    if(count($param) != 1) {
        echo 'Specify if publish or unpublish';
        die;
    }
    $action = $param[0];
    

    private_asset_schedule_remove($input['album'], $input['asset']);
    //
    // (Un)publishing the asset, and displaying a confirmation message.
    //
    if ($action == 'publish') {
        $res = ezmam_asset_publish($input['album'], $input['asset']);
        if (!$res) {
            error_print_message(ezmam_last_error());
            die;
        }
        // moves asset bookmarks from private to public 
        toc_album_bookmarks_swap($input['album'], $input['asset']);

        require_once template_getpath('popup_asset_successfully_published.php');
        //include_once "tmpl/fr/popup_asset_successfully_published.php";
    } else if ($action == 'unpublish') {
        $res = ezmam_asset_unpublish($input['album'], $input['asset']);
        if (!$res) {
            error_print_message(ezmam_last_error());
            die;
        }
        // moves asset bookmarks from public to private
        toc_album_bookmarks_swap($input['album'], $input['asset']);

        require_once template_getpath('popup_asset_successfully_unpublished.php');
    } else {
        error_print_message('Publish_unpublish: no operation provided');
        die;
    }

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }
}