<?php

require_once(__DIR__ . '/../../commons/lib_error.php');

/**
 * Copy asset from $input['from'] to $input['to']
 * @global type $input
 * @global type $repository_path
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $regenerate_title_mode;
    global $logger;
    
    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['asset']) || !isset($input['from']) || !isset($input['to'])) {
        echo 'Usage: index.php?action=copy_asset&amp;from=SOURCE&amp;to=DESTINATION&amp;asset=ASSET';
        $logger->log(EventType::MANAGER_ASSET_COPY, LogLevel::WARNING, 'Asset copy called with missing args. Input: '
                . var_export($input, true), array(basename(__FILE__)), $input['asset']);
        die;
    }

    if (!acl_has_album_permissions($input['from']) || !acl_has_album_permissions($input['to'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'copy_asset: you can\'t manage album ' . $input['from'] . ' or ' . $input['to']);
        $logger->log(
             EventType::MANAGER_ASSET_COPY,
             LogLevel::WARNING,
             'User tried to copy asset '. $input['asset']
                 . ' from ' . $input['from'] . 'to' . $input['from'] . ' but is not authorized',
                 array(basename(__FILE__)),
             $input['asset']
         );
        die;
    }

    if (!ezmam_asset_exists($input['from'], $input['asset'])) {
        $logger->log(
             EventType::MANAGER_ASSET_COPY,
             LogLevel::WARNING,
             'User tried to copy asset '. $input['asset']
                 . ' from ' . $input['from'] . 'to' . $input['from'] . ' but asset does not exists',
                 array(basename(__FILE__)),
             $input['asset']
         );
        error_print_message(template_get_message('Non-existant_album', get_lang()));
        log_append('warning', 'copy_asset: asset ' . $input['asset'] . ' of album ' . $input['from'] . ' does not exist');
        die;
    }
    
    $logger->log(EventType::MANAGER_ASSET_COPY, LogLevel::NOTICE, 'Started copying asset '. $input['asset'] . ' from '
             . $input['from'] . ' to ' . $input['to'], array(basename(__FILE__)), $input['asset']);

    $res = ezmam_asset_copy($input['asset'], $input['from'], $input['to']);
    if (!$res) {
        error_print_message(ezmam_last_error());
        $logger->log(
            EventType::MANAGER_ASSET_COPY,
            LogLevel::ERROR,
            'Asset copy '. $input['asset'] . ' from ' . $input['from']
                . 'to' . $input['from'] . ' failed. Last ezmam error: ' . ezmam_last_error(),
                array(basename(__FILE__)),
            $input['asset']
        );
        die;
    }

    // adds the previously saved bookmarks to the new album
    $bookmarks = toc_asset_bookmark_list_get($input['from'], $input['asset']);
    $count = count($bookmarks);
    for ($index = 0; $index < $count; $index++) {
        $bookmarks[$index]['album'] = $input['to'];
    }
    toc_album_bookmarks_add($bookmarks);

    require_once template_getpath('popup_asset_successfully_copied.php');

    if ($regenerate_title_mode == 'auto') {
        update_title($input['to'], $input['asset']);
    }

    $logger->log(
        EventType::MANAGER_ASSET_COPY,
        LogLevel::NOTICE,
        'Started copying asset '. $input['asset'] . ' from '
            . $input['from'] . ' to ' . $input['to'] . ' was successfully started',
            array(basename(__FILE__)),
        $input['asset']
    );
}
