<?php

/**
 * Moves asset from $input['from'] to $input['to']
 * @global type $input
 * @global type $repository_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $regenerate_title_mode;

    
    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['asset']) || !isset($input['from']) || !isset($input['to'])) {
        echo 'Usage: index.php?action=move_asset&amp;from=SOURCE&amp;to=DESTINATION&amp;asset=ASSET';
        die;
    }

    if (!acl_has_album_permissions($input['from']) || !acl_has_album_permissions($input['to'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'move_asset: you can\'t manage album ' . $input['from'] . ' or ' . $input['to']);
        die;
    }

    if (!ezmam_asset_exists($input['from'], $input['asset'])) {
        error_print_message(template_get_message('Non-existant_album', get_lang()));
        log_append('warning', 'move_asset: asset ' . $input['asset'] . ' of album ' . $input['from'] . ' does not exist');
        die;
    }
    
    private_asset_schedule_remove($input['from'], $input['asset']);

    // saves the bookmarks to copy
    $bookmarks = toc_asset_bookmark_list_get($input['from'], $input['asset']);
    // deletes the bookmarks from the source album
    toc_asset_bookmarks_delete_all($input['from'], $input['asset']);
    //
    // Moving the asset
    // TODO: the moving won't work if there is a different asset with the same name in dest folder.
    // Should be corrected in the future (new asset renamed)
    //
    $res = ezmam_asset_move($input['asset'], $input['from'], $input['to']);
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    // adds the previously saved bookmarks to the new album
    $count = count($bookmarks);
    for ($index = 0; $index < $count; $index++) {
        $bookmarks[$index]['album'] = $input['to'];
    }
    toc_album_bookmarks_add($bookmarks);
    
    require_once dirname(__FILE__) . '/../lib_sql_stats.php';
    db_stats_update_album($input['from'], $input['to']);

    // include_once $basedir.'/ezmanager/'.template_getpath('popup_asset_successfully_moved.php');

    // regegerate intro
    if ($regenerate_title_mode=='auto') {
        update_title($input['to'], $input['asset']);
    }
    
    
    $album = $input['from'];
    include_once template_getpath('popup_asset_successfully_moved.php');
}
