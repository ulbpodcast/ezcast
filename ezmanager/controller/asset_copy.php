<?php

/**
 * Copy asset from $input['from'] to $input['to']
 * @global type $input
 * @global type $repository_path 
 */
function index($param = array()) {
    global $input;
    global $repository_path;
	global $update_title;
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
    
    $res = ezmam_asset_copy($input['asset'], $input['from'], $input['to']);
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

	// view_main();
    require_once template_getpath('popup_asset_successfully_copied.php');
	// echo"<script>
		// show_popup_from_inner_div('#popup_asset_successfully_copied');
	// </script>";

	if ($update_title=='auto')	update_title($input['to'],$input['asset']);

}
