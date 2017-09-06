<?php

/**
 * Copies a bookmark from the personal bookmarks to the table of contents and reverse
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 * @global type $tab
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;
    global $tab;

    $bookmark_album = $input['album'];
    $bookmark_asset = $input['asset'];
    $bookmark_timecode = $input['timecode'];
    $bookmark_title = $input['title'];
    $bookmark_description = html_entity_decode($input['description']);
    $bookmark_keywords = $input['keywords'];
    $bookmark_level = $input['level'];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);

    if ($input['tab'] == 'official') { // copies from table of contents to personal bookmarks
        user_prefs_asset_bookmark_add(
            $_SESSION['user_login'],
            $bookmark_album,
            $bookmark_asset,
            $bookmark_timecode,
                $bookmark_title,
            $bookmark_description,
            $bookmark_keywords,
            $bookmark_level
        );

        // lvl, action, album, asset, timecode, target (to official|personal), title, description keywords, bookmark_lvl
        trace_append(array('3', 'asset_bookmark_copy', $bookmark_album, $bookmark_asset, $bookmark_timecode, 'custom',
            $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));
        log_append('copy_bookmark', 'bookmark copied from official to personal : album -' . $bookmark_album .
                ' asset - ' . $bookmark_asset .
                ' timecode - ' . $bookmark_timecode);
        
        // copies from personal bookmarks to table of contents
    } elseif (acl_user_is_logged() && acl_has_album_moderation($bookmark_album)) {
        toc_asset_bookmark_add(
            $bookmark_album,
            $bookmark_asset,
            $bookmark_timecode,
            $bookmark_title,
                $bookmark_description,
            $bookmark_keywords,
            $bookmark_level
        );

        trace_append(array('3', 'asset_bookmark_copy', $bookmark_album, $bookmark_asset, $bookmark_timecode, 'official',
            $bookmark_title, $bookmark_description, $bookmark_keywords, $bookmark_level));
        log_append('copy_bookmark', 'bookmark copied from personal to official : album -' . $bookmark_album .
                ' asset - ' . $bookmark_asset .
                ' timecode - ' . $bookmark_timecode);
    }

    if ($input['source'] == 'assets') {
        // refreshes the right_div (lvl 2)
        $input['token'] = ezmam_album_token_get($bookmark_album);
    }
    // refreshes the right_div (lvl 2 (if source = assets else 3))
    bookmarks_list_update();
}
