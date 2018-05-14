<?php


function index($param = array())
{
    global $input;
    global $repository_path;
    global $basedir;
    require_once $basedir.'/commons/lib_sql_management.php';


    //
    // Usual sanity checks
    //
    if (!isset($input['album']) || !isset($input['moderation'])) {
        echo "Usage: index.php?action=edit_album&session=SESSION_ID&intro=INTRO&addTitle=ADD_TITLE&credits=CREDITS";
        die;
    }

    if ($input['moderation'] == true) {
        $album = $input['album'] . '-priv';
    } else {
        $album = $input['album'] . '-pub';
    }

    ezmam_repository_path($repository_path);

    if (!ezmam_album_exists($album)) {
        error_print_message(ezmam_last_error());
        die;
    }

    //
    // Then we update the metadata
    //
    $album_meta = ezmam_album_metadata_get($album);

    $album_meta['intro'] = $input['intro'];
    if (isset($input['credits'])) {
        $album_meta['credits'] = $input['credits'];
    }
    $album_meta['add_title'] = $input['add_title'];
    $album_meta['downloadable'] = $input['downloadable'];
    $album_meta['anon_access'] = $input['anon_access'];
    $album_meta['recorder_access'] = $input['recorder_access'];
    
    if(isset($input['recorder_access'])){
        //change value of 'in_recorders' in DB 
        if($input['recorder_access']=='true')
            $recorder_access=1;
        else 
            $recorder_access=0;
        $res=db_in_recorder_update($input['album'],$recorder_access);
        //TO DO : synchro in cron every x hours
//        exec('php '.$basedir.'/ezadmin/cli_push_changes.php > /dev/null 2>/dev/null &');
    }

    $res = ezmam_album_metadata_set($album, $album_meta);

    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    //  view_main();
    require_once template_getpath('popup_album_successfully_edited.php');
}
