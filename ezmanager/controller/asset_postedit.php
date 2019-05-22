<?php
require_once dirname(__FILE__) . '/../config.inc';
require_once dirname(__FILE__).'/../lib_ezmam.php';
require_once dirname(__FILE__) . '/../../commons/common.inc';
require_once dirname(__FILE__) . '/../../commons/config.inc';



function index($param = array())
{

    global $input;
    global $basedir;
    global $php_cli_cmd;
    global $repository_basedir;
    global $copyright;
    global $organization_name;
    global $repository_path;


    //$renderer=require_once '/../../commons/renderer.inc';

    error_log($input['album']." ".$input['cutArray']);
    //
    // 0) Permissions checks
    //
    if (!isset($input['album']) || !isset($input['cutArray']) || !acl_has_album_permissions($input['album'])) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'asset_postedit: tried to access album ' . $input['album'] . ' without permission');
        die;
    }
    $cutArray=json_decode($input['cutArray'],true);
    $cutArray=$cutArray['cutArray'];
    $duration=0;


    //test validite cutArray

    $error=false;
    for ($i=0; $i < count($cutArray); $i++) {
        if (($cutArray[$i][0] >= $cutArray[$i][1] || $cutArray[$i][0] < 0 || $cutArray[$i][1] > $duration) || ($i>0 && $cutArray[$i][0]<$cutArray[$i-1][1])) {
            $error=true;
        }
    }
    if ($error) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        log_append('warning', 'asset_postedit: array send to postedit album ' . $input['album'] . ' corrupted');
        die;
    }


    $album=$input['album'];
    $asset=$input['asset'];
    $album_path = $repository_path . "/" . $album;

    $album_metadata = metadata2assoc_array($album_path . "/_metadata.xml");
    $asset_metadata = metadata2assoc_array($album_path .'/'.$asset. "/_metadata.xml");


  }
