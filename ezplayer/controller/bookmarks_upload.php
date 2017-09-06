<?php

/**
 * Uploads a temp file which contains bookmarks to import
 * @global type $imported_bookmarks
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $bookmarks_validation_file
 * @global type $album
 * @global type $asset
 */
function index($param = array())
{
    global $imported_bookmarks;
    global $repository_path;
    global $user_files_path;
    global $bookmarks_validation_file;
    global $album;
    global $asset;

    $album = $_POST['album']; // the album user wants to import in
    $asset = $_POST['asset']; // the asset user wants to import in
    $target = $_POST['target']; // personal bookmarks or table of contents
    
    if (!in_array($target, array('official', 'custom'))) {
        return false;
    }
    
    $_SESSION['album'] = $album;
    $_SESSION['asset'] = $asset;
    $_SESSION['target'] = $target;

    // 1) Sanity checks
    if ($_FILES['XMLbookmarks']['error'] > 0) {
        error_print_message(template_get_message('upload_error', get_lang()));
        log_append('error', 'upload_bookmarks: an error occurred during file upload (code ' . $_FILES['XMLbookmarks']['error']);
        die;
    }

    if ($_FILES['XMLbookmarks']['type'] != 'text/xml') {
        error_print_message(template_get_message('error_mimetype', get_lang()));
        log_append('warning', 'upload_bookmarks: invalid mimetype for file ' . $_FILES['XMLbookmarks']['tmp_name']);
        die;
    }

    if ($_FILES['XMLbookmarks']['size'] > 2147483) {
        error_print_message(template_get_message('error_size', get_lang()));
        log_append('warning', 'upload_bookmarks: file too big ' . $_FILES['XMLbookmarks']['tmp_name']);
        die;
    }

    // 2) Validates the XML file and converts it in associative array

    if (file_exists($_FILES['XMLbookmarks']['tmp_name'])) {

        // Validates XML structure
        $xml_dom = new DOMDocument();
        // trim heading and trailing white spaces
        // because blank lines in top and end of XML file lead to
        // validation error
        file_put_contents($_FILES['XMLbookmarks']['tmp_name'], trim(file_get_contents($_FILES['XMLbookmarks']['tmp_name'])));
        $xml_dom->load($_FILES['XMLbookmarks']['tmp_name']);

        if (!$xml_dom->schemaValidate($bookmarks_validation_file)) {
            include_once template_getpath('popup_bookmarks_import.php');
            error_print_message(template_get_message('error_structure', get_lang()));
        }

        // Converts XML file in SimpleXMLElement
        $xml = simplexml_load_file($_FILES['XMLbookmarks']['tmp_name']);
        $imported_bookmarks = xml_file2assoc_array($xml, 'bookmark');
    }

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    // Keeps only bookmarks from existing assets
    foreach ($imported_bookmarks as $index => $bookmark) {
        if (!ezmam_asset_exists($bookmark['album'], $bookmark['asset'])) {
            unset($imported_bookmarks[$index]);
        }
    }
    log_append('upload_bookmarks: file imported');
    // lvl, action, album, asset, target (in official|personal bookmarks), number of bookmarks uploaded
    trace_append(array($asset != '' ? '3' : '2', 'bookmarks_upload', $album, $asset != '' ? $asset : '-', $target, count($imported_bookmarks)));
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
    //   echo  "<script language='javascript' type='text/javascript'>".
    //              "window.top.window.document.getElementById('popup_import_bookmarks').innerHTML='$lapin';</script>";
    include_once template_getpath('popup_bookmarks_import.php');
}
