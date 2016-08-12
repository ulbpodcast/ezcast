<?php

require_once 'lib_sql_management.php';
require_once 'lib_report.php';


function index($param = array()) {
    global $input;
    
    // DEBUG TIME
//    $time = explode(' ', microtime());
//    $start = $time[1] + $time[0];
    
    if(array_key_exists('post', $input)) {
        
        // Param for report
        if(array_key_exists('start_date', $input) && array_key_exists('end_date', $input)) {
            $start_date = str_replace('-', '', $input['start_date']);
            $end_date = str_replace('-', '', $input['end_date']);
        } else {
            $start_date = 0;
            $end_date = PHP_INT_MAX;
        }
        $general = array_key_exists('general', $input);
        $ezplayer = array_key_exists('ezplayer', $input);
        // Generate report
        $report = new Report($start_date, $end_date, $general, $ezplayer);
        
        // get all classroom, number and time record
        $allClassRoom = $report->get_date_classroom_record_time();
        $totalSubmit = $allClassRoom['SUBMIT'];
        $totalClassroom = $allClassRoom['CLASSROOM'];
        unset($allClassRoom['SUBMIT']);
        unset($allClassRoom['CLASSROOM']);
        $classroom_not_use = calcul_unused_classroom($allClassRoom);
        
        $nbrSubmit = $totalSubmit['nbr'];
        $totalNbrClassroom = $nbrSubmit + $totalClassroom['nbr'];
        $percentSubmit = calcul_percent($nbrSubmit, $totalNbrClassroom);
        $percentAuditoir = round((100-$percentSubmit), 2);

        // Browser
        $totalBrowser = array_sum($report->get_ezplayer_date_list_user_browser());
        
        $json_view_asset_data = json_encode($report->get_ezplayer_asset_view_date());
        $json_view_asset_data = str_replace(',"', '], [', $json_view_asset_data);
        $json_view_asset_data = str_replace('":', ',', $json_view_asset_data);
        $json_view_asset_data = substr($json_view_asset_data, 2, -1);
        $json_view_asset_data = '[['.$json_view_asset_data.']]';
        
        
        $MAX_DETAILS_LIST = 20;
        
    }
        
    // DEBUG TIME
//    echo "real: ".(memory_get_peak_usage(true)/1024/1024)." MiB\n\n";
//    $time = explode(' ', microtime());
//    $finish = $time[1] + $time[0];
//    echo '<br />Page generated in '.round(($finish - $start), 4).' seconds.';
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_stats_report.php');
    include template_getpath('div_main_footer.php');
}

function calcul_unused_classroom(&$allClassRoom) {
    $classroom_not_use = array();
    $sqlClassroom = db_classrooms_list();
    foreach($sqlClassroom as $classroomInfo) {
        $classroom = $classroomInfo['room_ID'];
        if(!in_array($classroom, $allClassRoom)) {
            $classroom_not_use[] = $classroom;
        }
    }
    return $classroom_not_use;
}

function convert_seconds($duration){
    $hours = floor($duration / 3600);
    $minutes = floor(($duration / 60) % 60);
    $seconds = $duration % 60;

    return sprintf("%1$3d:%2$02d:%3$02d", $hours, $minutes, $seconds);
}

function calcul_percent($value, $total) {
    return round(($value / max(1, $total)) *100, 2);
}
