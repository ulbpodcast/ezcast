<?php

require_once dirname(__FILE__) . '/config.inc';
require_once dirname(__FILE__) . '/lib_ezmam.php';
require_once dirname(__FILE__) . '/../commons/common.inc';
require_once dirname(__FILE__) . '/../commons/config.inc';

global $basedir;
global $php_cli_cmd;
global $repository_basedir;
global $copyright;
global $organization_name;
global $repository_path;

$album=$argv[1];
$asset=$argv[2];
$album_path = $repository_path . "/" . $album;

// Get albumname
$album_metadata = metadata2assoc_array($album_path . "/_metadata.xml");
$asset_metadata = metadata2assoc_array($album_path .'/'.$asset. "/_metadata.xml");
if (!isset($album_metadata['course_code_public'])) {
    $album_metadata['course_code_public']='';
}

// Get ip/name of renderer
$ezrenderer_list= require $basedir.'/ezmanager/renderers.inc';
$keys=array_keys($ezrenderer_list);
$ezrenderer=$ezrenderer_list[$keys[0]];

$log_path=$repository_basedir.'/repository/'.$album.'/'.$asset.'/renderer_update_title.log';

// with copy, ther is already a process in background so may generate conflict with the regeneration of the title.
$t=0;
while ($asset_metadata['status']!='processed' || $t>10) {
    sleep(2);
    $asset_metadata = metadata2assoc_array($album_path .'/'.$asset. "/_metadata.xml");
    $t++;
}


if (isset($asset_metadata['add_title']) && $asset_metadata['status']=='processed') {
    
    // put the status of the asset to "processing"
    $cmd1='sed -i "s/<status>processed<\/status>/<status>processing<\/status>/g" '.$repository_basedir.'/repository/'.$album.'/'.$asset.'/_metadata.xml';
    // Copy asset in ezrenderer
    $cmd2='rsync -avhp '.$repository_basedir.'/repository/'.$album.'/'.$asset.'/  '.$ezrenderer['client'].'@'.$ezrenderer['host'].':'.$ezrenderer['processed_dir'].'/../processing/'.$album.'_'.$asset;
    // launch the rendering
    $cmd3='ssh '.$ezrenderer['client'].'@'.$ezrenderer['host'].' "'.$php_cli_cmd.' '.$ezrenderer['processed_dir'].'/../../bin/gen_new_intro.php '.$ezrenderer['processed_dir'].'/../processing/'.$album.'_'.$asset.' \"'.base64_encode($album_metadata['name']).'\"  \''.base64_encode($copyright).'\'  \''.base64_encode($organization_name).'\'   \''.base64_encode($album_metadata['id']).'\'  \''.base64_encode(suffix_remove($album)).'\'  \''.base64_encode($album_metadata['course_code_public']).'\' "';
    // get new files with good title
    $cmd4='rsync -avhp '.$ezrenderer['client'].'@'.$ezrenderer['host'].':'.$ezrenderer['processed_dir'].'/../processing/'.$album.'_'.$asset.'/'.' '.$repository_basedir.'/repository/'.$album.'/'.$asset;
    // delete proccessifile
    $cmd5='ssh '.$ezrenderer['client'].'@'.$ezrenderer['host'].' "rm -rf '.$ezrenderer['processed_dir'].'/../processing/'.$album.'_'.$asset.'"';
    // put status of the asset to processed
    $cmd6='sed -i "s/<status>processing<\/status>/<status>processed<\/status>/g" '.$repository_basedir.'/repository/'.$album.'/'.$asset.'/_metadata.xml ';
    
    exec($cmd1, $cmdoutput1, $returncode1);
    exec($cmd2, $cmdoutput2, $returncode2);
    exec($cmd3, $cmdoutput3, $returncode3);
    exec($cmd4, $cmdoutput4, $returncode4);
    exec($cmd5, $cmdoutput5, $returncode5);
    exec($cmd6, $cmdoutput6, $returncode6);
        
    file_put_contents($log_path, implode("\n", $cmdoutput1)."\n".implode("\n", $cmdoutput2)."\n".implode("\n", $cmdoutput3)."\n".implode("\n", $cmdoutput4)."\n".implode("\n", $cmdoutput5)."\n".implode("\n", $cmdoutput6)."\n", FILE_APPEND);

    
    // Debug
                                                                                                                                                                           
    // file_put_contents($log_path,'cmd1 :  '.$cmd1. PHP_EOL .'cmd2  : '.$cmd2. PHP_EOL .'cmd3  : '.$cmd3. PHP_EOL .'cmd4  : '.$cmd4. PHP_EOL .'cmd5  : '.$cmd5. PHP_EOL .'cmd6  : '.$cmd6. PHP_EOL .'output : '.$cmdoutput. PHP_EOL .'returnval : '.$returncode. PHP_EOL .'return : '.$return,FILE_APPEND);
    // file_put_contents($log_path,'return : '.$assoc_metadata['name'],FILE_APPEND);
    // file_put_contents($log_path,'return : '.$ezrenderer['name'],FILE_APPEND);
}
