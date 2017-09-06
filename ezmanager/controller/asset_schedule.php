<?php

/**
 * Schedules the publication / archiving of an asset
 * @global type $input
 * @global type $repository_path
 * @global type $php_cli_cmd
 * @global type $ezmanager_basedir
 * @global type $action
 */
function index($param = array())
{
    global $input;
    global $repository_path;
    global $php_cli_cmd;
    global $ezmanager_basedir;
    global $action;
    $DTZ = new DateTimeZone('Europe/Paris');
    $asset_publish_pgm= $ezmanager_basedir. "/cli_asset_publish_unpublish.php";

    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['date'])) {
        echo "Usage: index.php?action=schedule_asset&album=ALBUM&asset=ASSET&date=DATE";
        die;
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($input['album'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    if (!ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }


    $action = (album_is_public($input['album'])) ? "unpublish" : "publish";
    $date = date("H:i M d, Y", strtotime($input["date"]));

    $cmd = "echo '" . $php_cli_cmd . " " . $asset_publish_pgm . " " . $input["album"] . " " . $input["asset"] . " " .
            $action . "' | at " . $date . "  2>&1 | awk '/job/ {print $2}'";
    $at_id = shell_exec($cmd);
    //
    // Then we update the metadata
    //
    $asset_meta = ezmam_asset_metadata_get($input["album"], $input["asset"]);

    $asset_meta['scheduled'] = true;
    $asset_meta['schedule_id'] = $at_id;
    $asset_meta['schedule_date'] = $input['date'];

    $res = ezmam_asset_metadata_set($input["album"], $input["asset"], $asset_meta);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //  view_main();
    require_once template_getpath('popup_asset_successfully_scheduled.php');
}
