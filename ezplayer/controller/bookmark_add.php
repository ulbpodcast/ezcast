<?php

/**
 * Adds or edits a bookmark to the user's bookmarks list
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;


    $bookmark_album = $input['album'];
    $bookmark_asset = $input['asset'];
    $bookmark_timecode = $input['timecode'];
    $bookmark_title = $input['title'];
    $bookmark_description = $input['description'];
    $bookmark_keywords = $input['keywords'];
    $bookmark_level = $input['level'];
    $bookmark_source = $input['source'];
    $bookmark_type = $input['type'];

    if (!acl_user_is_logged()) {
        return false;
    }
    
    if (is_nan($bookmark_timecode) || is_nan($bookmark_level)) {
        bookmarks_list_update();
    }

    if (!isset($bookmark_type) || ($bookmark_type != 'cam' && $bookmark_type != 'slide')) {
        $bookmark_type = '';
    }
    
    if (!isset($bookmark_source) || ($bookmark_source != 'personal' && $bookmark_source != 'official')) {
        $bookmark_source = '';
    }
    
    $str_bookmark_keywords = "";
    foreach (explode(",", $bookmark_keywords) as $keyword) {
        if ($str_bookmark_keywords != "") {
            $str_bookmark_keywords .= ",";
        }
        $str_bookmark_keywords .= trim($keyword);
    }
    $bookmark_keywords = $str_bookmark_keywords;
    
    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($bookmark_source == 'personal') { // personal bookmarks
        user_prefs_asset_bookmark_add(
            $_SESSION['user_login'],
            $bookmark_album,
            $bookmark_asset,
                $bookmark_timecode,
            $bookmark_title,
            $bookmark_description,
            $bookmark_keywords,
                $bookmark_level,
            $bookmark_type
        );
        
        // table of contents
    } elseif (acl_user_is_logged() && acl_has_album_moderation($bookmark_album)) {
        toc_asset_bookmark_add(
            $bookmark_album,
            $bookmark_asset,
            $bookmark_timecode,
            $bookmark_title,
                $bookmark_description,
            $bookmark_keywords,
            $bookmark_level,
            $bookmark_type
        );
    }
    
    log_append('add_asset_bookmark', 'bookmark added : album -' . $bookmark_album . PHP_EOL .
            'asset - ' . $bookmark_asset . PHP_EOL .
            'timecode - ' . $bookmark_timecode);
    
    // lvl, action, album, asset, timecode, target (personal|official), type (cam|slide), title, descr, keywords, bookmark_lvl
    if ($bookmark_type != '' && $bookmark_source != '') { // TODO error if not define
        trace_append(array('3', (array_key_exists('edit', $input) && $input['edit']) ? 'asset_bookmark_edit' : 'asset_bookmark_add',
            $bookmark_album, $bookmark_asset, $bookmark_timecode, $bookmark_source, $bookmark_type,
            $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));
    }

    bookmarks_list_update();
}
