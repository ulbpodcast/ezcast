<?php

require_once __DIR__ . '/../../commons/lib_sql_management.php';
require_once 'lib_sql_event.php';
require_once 'lib_report.php';

 
function index($param = array())
{
    global $input;
    
    // DEBUG TIME
    //    $time = explode(' ', microtime());
    //    $start = $time[1] + $time[0];
    
    if (array_key_exists('post', $input)) {
        
        // Param for report
        if (array_key_exists('start_date', $input) && array_key_exists('end_date', $input)) {
            $start_date = str_replace('-', '', $input['start_date']);
            $end_date = str_replace('-', '', $input['end_date']);
            $str_start_date = $input['start_date'].' 00:00:00';
            $str_end_date = $input['end_date'].' 00:00:00';
        } else {
            $start_date = 0;
            $end_date = PHP_INT_MAX;
            $str_start_date = $start_date;
            $str_end_date = $end_date;
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
        $percentClassrooms = round((100-$percentSubmit), 2);

        // Browser
        $totalBrowser = array_sum($report->get_ezplayer_date_list_user_browser());
        
        $json_view_asset_data = date_to_json_highcharts($report->get_ezplayer_asset_view_date());
        $data_status_date = db_event_get_success_error_status_for_dates($str_start_date, $str_end_date);
        //print_r($data_status_date);
        $json_status_date_success = date_to_json_highcharts($data_status_date['success']);
        $json_status_date_error = date_to_json_highcharts($data_status_date['error']);
        
        list($success, $error) = db_event_status_get_nbr($str_start_date, $str_end_date);
        $totalStatus = $success+$error;
        $percentSuccess = calcul_percent($success, $totalStatus);
        $percentError = calcul_percent($error, $totalStatus);
        
        $nbr_camslide = db_event_info_camslide_nbr($str_start_date, $str_end_date);
        $total_nbr_camslide = 0;
        foreach ($nbr_camslide as $infos) {
            $total_nbr_camslide += $infos['total_type'];
        }
        /*
        $all_last_status_for_period = db_event_get_last_status_for_period($str_start_date, $str_end_date);
        $count_for_status = array();
        foreach($all_last_status_for_period as $last_status) {
            if(isset( $count_for_status[$last_status['status']]))
                $count_for_status[$last_status['status']]++;
            else
                $count_for_status[$last_status['status']] = 1;
        }
        var_dump($count_for_status);
         */
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

function calcul_unused_classroom(&$allClassRoom)
{
    $classroom_not_use = array();
    $sqlClassroom = db_classrooms_list();
    foreach ($sqlClassroom as $classroomInfo) {
        $classroom = $classroomInfo['room_ID'];
        if (!in_array($classroom, $allClassRoom)) {
            $classroom_not_use[] = $classroom;
        }
    }
    return $classroom_not_use;
}

function convert_seconds($duration)
{
    $hours = floor($duration / 3600);
    $minutes = floor(($duration / 60) % 60);
    $seconds = $duration % 60;

    return sprintf("%1$3d:%2$02d:%3$02d", $hours, $minutes, $seconds);
}

function calcul_percent($value, $total)
{
    return round(($value / max(1, $total)) *100, 2);
}

function date_to_json_highcharts($data)
{
    $json_data = json_encode($data);
    $json_data = str_replace(',"', '], [', $json_data);
    $json_data = str_replace('":', ',', $json_data);
    $json_data = substr($json_data, 2, -1);
    return '[['.$json_data.']]';
}
