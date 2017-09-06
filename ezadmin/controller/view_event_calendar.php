<?php

require_once 'lib_sql_event.php';
require_once '../commons/event_status.php';

/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';

function index($param = array())
{
    global $input;
    
    $classRoom = false;
    $sqlDateEvent = array();
    if (array_key_exists('post', $input) && array_key_exists('classroom', $input) &&
            $input['classroom'] != "") {
        $sqlDateEvent = db_event_asset_infos_get($input['classroom']);
        $classRoom = true;
    } else {
        $sqlDateEvent = db_event_asset_infos_get();
    }
    
    $phpDateEvent = array();
    $excluded = get_courses_excluded_from_stats();
    foreach ($sqlDateEvent as $info) {
        $event = array();
        
        if (in_array($info['course'], $excluded)) {
            continue;
        }
        
        $event['asset'] = $info['asset'];
        $event['title'] = "";
        if (!$classRoom) {
            if ($info['classroom_id'] != "") {
                $event['title'] = '['.$info['classroom_id'].'] ';
            } else {
                $event['title'] = '[Submit] ';
            }
        }
        $event['title'] .= $info['asset'];
        $event['startsAt'] = $info['start_time'];
        $event['endsAt'] = $info['end_time'];
        $event['type'] = EventStatus::getColorStatus($info['status']);
        
        $phpDateEvent[] = $event;
    }
    
    $dateEvent = json_encode($phpDateEvent);
    
    $js_classroom = "";
    $listClassroom = array();
    foreach (db_classrooms_list() as $classroomInfos) {
        $listClassroom[] = "'".$classroomInfos['name']."'";
    }
    $js_classroom = '['.implode(', ', $listClassroom).']';
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_event_calendar.php');
    include template_getpath('div_main_footer.php');
}
