<?php

$in_install = true;

require_once __DIR__ .'/commons/common_static.inc';

$G="\033[32m\033[1m";
$R="\033[31m\033[1m";
$N="\033[0m";

$components = array("ezmanager","ezplayer","ezadmin");
$languages = array("fr","en");

foreach ($components as $component) {
    $source_folder = "./$component/tmpl_sources";
    $dest_folder = "./$component/$template_folder";
    
    if (!is_dir($dest_folder)) {
        $res = mkdir($dest_folder);
        if (!$res) {
            echo $R."ERROR : Could not create $dest_folder ! $N". PHP_EOL ;
            continue;
        }
    }
    
    echo "Compiling files ..." . PHP_EOL;
    foreach ($languages as $language) {
        exec(PHP_BINARY . " ./commons/cli_template_generate.php $source_folder $language $dest_folder ./$component/translations.xml");
    }
    echo $G."Compilation of $component complete. $N " . PHP_EOL;
}
