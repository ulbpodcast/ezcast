<?php

/**
 * Defines user's preferences on how bookmarks should be ordered in the web interface
 * @global type $input
 * @global type $repository_path
 * @global type $user_files_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $user_files_path;

    $album = $_SESSION["album"];
    $panel = $input['panel'];
    $new_order = $input["order"];

    // init paths
    ezmam_repository_path($repository_path);
    user_prefs_repository_path($user_files_path);
    
    if (acl_value_get("${panel}_bm_order") != $new_order) {
        if (acl_user_is_logged()) {
            user_prefs_settings_edit($_SESSION['user_login'], "${panel}_bm_order", $new_order);
            acl_update_settings();
        } else {
            $_SESSION["acl_user_settings"]["${panel}_bm_order"] = $new_order;
        }
    }
    // lvl, action, album, panel (official|personal), new_order (chron|reverse_chron)
    trace_append(array($input['source'] == 'assets' ? '2' : '3', 'bookmarks_sort', $album, $panel, $new_order));
    
    global $show_panel;
    $show_panel = true;
    
    // determines the page to display
    if ($input['source'] == 'assets') {
        // the token is needed to display the album assets
        $input['token'] = ezmam_album_token_get($album);
    }
    bookmarks_list_update();
}
