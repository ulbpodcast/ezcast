<?php

/**
 * Exports all selected bookmarks in an xml file
 * @global type $input
 * @global type $user_files_path
 * @global type $repository_path
 */
function index($param = array())
{
    global $input;
    global $user_files_path;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];
    $selection = $input['export_selection']; // the selection of bookmarks to export
    $target = $input['target'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // name for the file to be saved
    $filename = (get_lang() == 'fr') ? 'signets' : 'bookmarks';
    if ($target == 'official') {
        $filename .= (get_lang() == 'fr') ? '_officiels' : '_official';
    }
    $filename .= '_' . suffix_remove($album);
    if (isset($asset) && $asset != '') {
        $filename .= '_' . $asset;
    }
    $filename .= '.xml';
    
    // download popup
    if ($target == 'official') { // bookmarks from the table of contents
        $bookmarks = toc_asset_bookmarks_selection_get($album, $asset, $selection);
    } else { // personal bookmarks
        $bookmarks = user_prefs_asset_bookmarks_selection_get($_SESSION['user_login'], $album, $asset, $selection);
    }
    
    if ($bookmarks == false) {
        echo "Failed to export bookmarks";
        return;
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

    log_append('export_bookmarks: bookmarks exported from the album ' . $album);
    // lvl, action, album, asset, target (from official|personal), number of exported bookmarks
    trace_append(array($_SESSION['asset'] == '' ? '2' : '3', 'bookmarks_export', $album,
        $_SESSION['asset'] != '' ? $_SESSION['asset'] : '-',
        $target == '' ? 'personal' : $target,
        count($selection)));
}
