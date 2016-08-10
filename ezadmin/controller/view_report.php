<?php

require_once 'lib_sql_management.php';
require_once 'lib_report.php';


function index($param = array()) {
    global $input;
    
    // DEBUG TIME
    $time = explode(' ', microtime());
    $start = $time[1] + $time[0];
    
    
    if(array_key_exists('start_date', $input) && array_key_exists('end_date', $input)) {
        $start_date = $input['start_date'];
        $end_date = $input['end_date'];
    } else {
        $start_date = 0;
        $end_date = PHP_INT_MAX;
    }
     
    $report = new Report($start_date, $end_date);
    
    $classroom_not_use = array();
    $allClassRoom = $report->get_date_classroom_record_time();
    $sqlClassroom = db_classrooms_list();
    foreach($sqlClassroom as $classroomInfo) {
        $classroom = $classroomInfo['room_ID'];
        if(!in_array($classroom, $allClassRoom)) {
            $classroom_not_use[] = $classroom;
        }
    }
    $nbrSubmit = $allClassRoom['SUBMIT']['nbr'];
    if(!array_key_exists('AUDITOIRES', $allClassRoom)) {
        $allClassRoom['AUDITOIRES'] = array('nbr' => 0, 'time' => 0);
    }
    $totalClassroom = $nbrSubmit + $allClassRoom['AUDITOIRES']['nbr'];
    $percentSubmit = round(($nbrSubmit / $totalClassroom) *100, 2);
    $percentAuditoir = round((100-$percentSubmit), 2);
    
    /// Mount asset consult
    $mountAsset = array();
    for($i = 1; $i <= 12; ++$i) {
        $mountAsset[$i] = 0;
    }
    foreach($report->get_ezplayer_date_unique_asset() as $asset => $nbr) {
        $res = array();
        preg_match("/[0-9]{4}_([0-9]{2})_[0-9]{2}_[0-9]{2}h[0-9]{2}_[\w-_]*/", $asset, $res);
        $mount = $res[1];
        $mountAsset[intval($mount)] += $nbr;
    }
    
    // DEBUG TIME
    echo "real: ".(memory_get_peak_usage(true)/1024/1024)." MiB\n\n";
    $time = explode(' ', microtime());
    $finish = $time[1] + $time[0];
    echo '<br />Page generated in '.round(($finish - $start), 4).' seconds.';
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_stats_report.php');
    include template_getpath('div_main_footer.php');
}

