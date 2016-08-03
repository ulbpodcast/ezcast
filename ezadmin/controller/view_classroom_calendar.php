<?php

require_once 'lib_sql_event.php'; 
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';


function index($param = array()) {
    global $input;
    
    if(array_key_exists('post', $input)) {
        
        if(array_key_exists('classroom', $input) && array_key_exists('nweek', $input)) {
            
            
            $recordRepartition = db_event_get_record_after_date(
                        $input['classroom'], 
                        calcul_date($input['nweek']));
            print_r($recordRepartition);
        }
        
        
    }
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_search_calendar.php');
    if (!empty($recordRepartition)) {
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
