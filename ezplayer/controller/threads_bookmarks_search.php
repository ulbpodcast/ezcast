<?php

/**
 * Searches a specific pattern in the bookmarks and/or threads
 * @global type $input
 * @global type $bookmarks
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $words
 */
function index($param = array())
{
    global $input;
    global $search_result_threads;
    global $bookmarks;
    global $bookmarks_toc;
    global $repository_path;
    global $user_files_path;
    global $words; // used to highlight the searched words in 'div_search_result.php'

    $search = trim($input['search']); // the pattern to be searched
    $target = $input['target']; // where to search (all albums / selected albums / current album)
    $albums = $input['albums']; // the selection of albums
    $fields = $input['fields']; // where to search in the bookmark fields (title / descr. / keywords)
    $fields_thread = array_key_exists('fields_thread', $input) ? $input['fields_thread'] : array('');
    $level = $input['level'];
    $tab = $input['tab'];

    if (!isset($level) || is_nan($level) || $level < 0 || $level > 3) {
        $level = 0;
    }

    log_append('search_bookmarks : ' . PHP_EOL .
            'search - ' . $search . PHP_EOL .
            'target - ' . $target . PHP_EOL .
            'fields - ' . implode(", ", $fields) . PHP_EOL .
            'fields thread - ' . implode(", ", $fields_thread) . PHP_EOL .
            'tab - ' . implode(", ", $tab));

    // defines target
    if (!isset($target) || $target == '') {
        $target = 'global';
    }

    $album = $_SESSION['album'];
    $asset = $_SESSION['asset'];

    if ($target == 'current' // we search in the current album / asset
            && (!isset($album) || $album == '')) {
        $target = 'global';
    }

    // split the string, saves the value to search in a array
    $words = str_getcsv($search, ' ', '"');
    $search = array();
    foreach ($words as $index => $word) {
        if ($word == '' || $word == '+') {
            unset($words[$index]);
        } else {
            $search[] = $word;
        }
    }
    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    $bookmarks_toc = array();

    switch ($target) {
        case 'current': // searches in current location (either global or album or asset)
            $albums = array($album);
            break;
        case 'album': // searches in albums selection
            if (!acl_has_album_permissions($album)) {
                $bookmarks_toc = toc_bookmarks_search($search, $fields, $level, array($album), $asset);
            }
            $asset = ""; // asset must be empty for searching in albums selection
            break;
        default: // searches in all albums
            if (!acl_has_album_permissions($album)) {
                $bookmarks_toc = toc_bookmarks_search($search, $fields, $level, array($album), $asset);
            }
            $asset = ""; // asset must be empty for searching in all albums
            $albums = acl_authorized_albums_list();
            break;
    }

    if (in_array('official', $tab)) { // searches in official bookmarks
        $bookmarks_toc = array_merge($bookmarks_toc, toc_bookmarks_search($search, $fields, $level, $albums, $asset));
    }
    if (in_array('custom', $tab)) { // searches in personal bookmarks
        $bookmarks = user_prefs_bookmarks_search($_SESSION['user_login'], $search, $fields, $level, $albums, $asset);
    }
    if (acl_user_is_logged() && acl_display_threads() && in_array('threads', $tab)) { // searches in threads
        $search_result_threads = thread_search($search, $fields_thread, $albums, $asset);
    }

    $lvl = ($_SESSION['album'] != '' && $_SESSION['asset'] != '') ? 3 : (($_SESSION['album'] != '') ? 2 : 1);
    trace_append(array($lvl,
        $input['origin'] == 'keyword' ? 'keyword_search' : 'bookmarks_search',
        $_SESSION['album'] == '' ? '-' : $_SESSION['album'],
        $_SESSION['asset'] == '' ? '-' : $_SESSION['asset'],
        implode(', ', $search),
        $target,
        implode(", ", $fields),
        implode(", ", $fields_thread),
        implode(", ", $tab),
        count($bookmarks_toc),
        count($bookmarks),
        count($search_result_threads)));

    include_once template_getpath('div_search_result.php');
}
