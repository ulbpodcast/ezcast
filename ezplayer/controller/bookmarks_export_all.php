<?php

/**
 * Exports all bookmarks from the given album / asset in an xml file
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 * @param type $export_asset false if all album's bookmarks must be exported;
 * true if only specified asset's bookmarks must be exported
 */
function index($param = array())
{
    $export_asset = (count($param) == 1 && $param[0]);
    
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $input['album'];
    if ($export_asset) {
        $asset = $input['asset'];
    }

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // name for the file to be saved
    $filename = (get_lang() == 'fr') ? 'signets' : 'bookmarks';
    $filename .= '_' . suffix_remove($album);
    if (isset($asset) && $asset != '') {
        $filename .= '_' . $asset;
    }
    $filename .= '.xml';

    // download popup
    if ($export_asset) {
        $bookmarks = user_prefs_asset_bookmarks_list_get($_SESSION['user_login'], $album, $asset);
    } else {
        $bookmarks = user_prefs_album_bookmarks_list_get($_SESSION['user_login'], $album);
    }
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: text/xml");
    header("Content-Transfer-Encoding: binary");

    // XML to save in the file
    $xml_txt = assoc_array2xml_string($bookmarks, "bookmarks", "bookmark");
    // Formating XML for pretty display
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadXML($xml_txt);
    $dom->formatOutput = true;
    ob_clean();
    flush();
    echo $dom->saveXml();

    log_append('export_asset_bookmarks: bookmarks exported from the album ' . $album);
}
