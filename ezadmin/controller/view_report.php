<?php

require_once 'lib_report.php';

function index($param = array()) {
    global $input;
    
    repository_get_all();
    $generalInfos = repository_get_general_infos();
//    echo "real: ".(memory_get_peak_usage(true)/1024/1024)." MiB\n\n";
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_stats_report.php');
    include template_getpath('div_main_footer.php');
    
}

