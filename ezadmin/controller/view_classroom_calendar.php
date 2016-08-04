<?php

require_once 'lib_sql_event.php'; 
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';

$error_asset = array();


function index($param = array()) {
    global $input;
    
    if(array_key_exists('post', $input)) {
        
        if(array_key_exists('classroom', $input) && array_key_exists('nweek', $input)) {
            $recordRepartition = db_event_get_record_after_date(
                        $input['classroom'], 
                        calcul_date($input['nweek']));
            
            $resultRecord = calcul_hour_by_hour($recordRepartition);
            
        }
    }
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_search_calendar.php');
    if (!empty($resultRecord)) {
        include template_getpath('div_monit_view_calendar.php');
    }
    include template_getpath('div_main_footer.php');
    
}

function calcul_date($nbr_day) {
    // 86400 = one day
    // (86400*7)= 604800 = one week
    $res = date("Y-m-d H:i:s", strtotime("monday this week")-($nbr_day*604800));
    return $res;
}


/**
 * Calcul when the record must be show
 * 
 * 
 * @param Array $recordData sql data for all asset
 * @return ArrayList with number of day in key and array in value. In this value there 
 * is hour in key and asset in value
 */
function calcul_hour_by_hour($recordData) {
    
    $resultRecord = array();
    for($i = 1;$i <= 7; ++$i) {
        $resultRecord[$i] = array();
    }
    
    foreach ($recordData as $record) {
        $nbrDayStart = date("N", strtotime($record['start_time']));
        $nbrDayEnd = date("N", strtotime($record['end_time']));
        
        if($nbrDayStart != $nbrDayEnd) {
            // TODO: show this !
            $error_asset[] = $record['asset'];
            continue;
        }
        
        $timeStart = calcul_int_date($record['start_time']);
        $timeEnd = calcul_int_date($record['end_time']);
        
        for($hour = $timeStart; $hour <= $timeEnd; ++$hour) {
            if(!array_key_exists($hour, $resultRecord[$nbrDayStart])) {
                $resultRecord[$nbrDayStart][$hour] = array();
            }
            
            $resultRecord[$nbrDayStart][$hour][] = $record;
        }
        
    }
    
    return $resultRecord;
}

/**
 * Convert date and hour in an integer
 * It's the hour*2 and 30 min = one
 * 
 * @param Data $date format YYYY-MM-DD HH:mm:ss
 */
function calcul_int_date($date) {
    return date("G", strtotime($date))*2 + ((int)(date("i", strtotime($date))/30));
}
