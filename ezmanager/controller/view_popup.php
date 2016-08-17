<?php

/**
 * General popup dispatcher. Depending on the value of $input['popup'], it will show the right popup.
 * @global type $input 
 */
function index($param = array()) {
    global $input;

    if (!isset($input['popup'])) {
        echo 'Usage: web_index.php?action=show_popup&amp;popup=POPUP';
        die;
    }

    switch ($input['popup']) {
        case 'media_url':
            popup_media_url();
            break;

        case 'embed_code':
            popup_embed_code();
            break;

        case 'ezplayer_link':
            popup_ezplayer_link();
            break;

        case 'ulb_code':
            popup_ulb_code();
            break;

        default:
            error_print_message('view_popup: content of popup ' . $input['popup'] . ' not found');
            die;
    }
}


/**
 * Displays the popup with a link to the media
 */
function popup_media_url() {
    global $input;
    global $repository_path;
    global $ezmanager_url;

    ezmam_repository_path($repository_path);
    $media_url = get_link_to_media($input['album'], $input['asset'], $input['media']) . "&origin=link";
    $media_url_web = get_link_to_media($input['album'], $input['asset'], $input['media'], false) . "&origin=link";


    require_once template_getpath('popup_media_url.php');

    $url = $ezmanager_url;
    // TODO: why include 2 times ?
    require_once template_getpath('popup_media_url.php');
}


/**
 * DIsplays the popup with the embed code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_embed_code() {
    global $input;
    global $repository_path;
    global $ezmanager_url;
    global $distribute_url;

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=embed_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    // Retrieving the info needed for the embed code and target link
    $metadata = ezmam_media_metadata_get($input['album'], $input['asset'], $input['media']);
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    if (!$token)
        $token = ezmam_album_token_get($input['album']);

    $media_infos = explode('_', $input['media']);
    $type = $media_infos[1];
    $quality = $media_infos[0];
    //compute iframe size according to media size
    $iframe_height = $metadata['height'] + 40;
    $iframe_width = $metadata['width'] + 30;
    // Embed code
    $link_target = $distribute_url . '?action=embed&amp;album=' . $input['album'] . '&amp;asset=' . $input['asset'] . '&amp;type=' . $type . '&amp;quality=' . $quality . '&amp;token=' . $token;
    $embed_code_web = '<iframe width="' . $iframe_width . '" height="' . $iframe_height . '" style="padding: 0;" frameborder="0" scrolling="no" src="' . $distribute_url . '?action=embed_link&album=' . $input['album'] . '&asset=' . $input['asset'] . '&type=' . $type . '&quality=' . $quality . '&token=' . $token . '&width=' . $metadata['width'] . '&height=' . $metadata['height'] . '&lang=' . get_lang() . '"><a href="' . $link_target . '">' . template_get_message('view_video', get_lang()) . '</a></iframe>';
    $embed_code = htmlentities($embed_code_web, ENT_COMPAT, 'UTF-8');

    // Displaying the popup
    require_once template_getpath('popup_embed_code.php');
}


/**
 * Displays the popup with the EZplayer link to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_ezplayer_link() {
    global $input;
    global $ezplayer_url;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];

    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset'])) {
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    $asset_meta = ezmam_asset_metadata_get($input['album'], $input['asset']);
    $action = (strtolower($asset_meta['origin']) === "streaming") ? 'view_asset_streaming' : 'view_asset_details';
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    $ezplayer_link = $ezplayer_url . '/index.php?'
            . 'action=' . $action
            . '&album=' . $album
            . '&asset=' . $asset
            . '&asset_token=' . $token;

    // Displaying the popup
    require_once template_getpath('popup_ezplayer_link.php');
}


/**
 * Displays the popup with the ulb code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url 
 */
function popup_ulb_code() {
    global $input;
    global $repository_path;

    $asset_name = $input['asset'];

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=ulb_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    $ulb_code = get_code_to_media($input['album'], $input['asset'], $input['media']);

    // Displaying the popup
    require_once template_getpath('popup_ulb_code.php');
}
