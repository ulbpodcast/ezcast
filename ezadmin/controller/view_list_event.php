<?php

require_once 'lib_sql_event.php';
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';


function index($param = array())
{
    global $input;
    global $logger;
    
    
    if (array_key_exists('page', $input)) {
        $pagination = new Pagination($input['page'], 50);
    } else {
        $pagination = new Pagination(1, 50);
    }
    
    if (array_key_exists('col', $input) && array_key_exists('order', $input)) {
        $colOrder = new Sort_colonne($input['col'], $input['order']);
    } else {
        $colOrder = new Sort_colonne('event_time');
    }
    
    $js_classroom = "";
    $listClassroom = array();
    foreach (db_classrooms_list() as $classroomInfos) {
        $listClassroom[] = "'".$classroomInfos['name']."'";
    }
    $js_classroom = '['.implode(', ', $listClassroom).']';
    
    // Get Events
    if (isset($input['post'])) {
        $events = db_event_get(
 
            empty_str_if_not_def('asset', $input),
                empty_str_if_not_def('origin', $input),
 
            empty_str_if_not_def('classroom', $input),
                empty_str_if_not_def('courses', $input),
 
            empty_str_if_not_def('teacher', $input),
                empty_str_if_not_def('startDate', $input),
 
            empty_str_if_not_def('endDate', $input),
                empty_str_if_not_def('type_id', $input),
 
            empty_str_if_not_def('context', $input),
                empty_str_if_not_def('log_level', $input),
 
            empty_str_if_not_def('message', $input),
                $colOrder->getCurrentSortCol(),
 
            $colOrder->getOrderSort(),
                $pagination->getStartElem(),
 
            $pagination->getElemPerPage()
 
        );
        
        foreach ($events as &$event) {
            $event['event_time'] = date("d/m/y H:i:s", strtotime($event['event_time']));
            $event['loglevel_name'] = $logger->get_log_level_name($event['loglevel']);
            if (strlen($event['message']) > 50) {
                $event['min_message'] = substr($event['message'], 0, 50);
                $event['min_message'] .= "...";
            }
        }
        
        $pagination->setTotalItem(db_found_rows());
    } else {
        $logLevel_default_max_selected = 3;
    }
    
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_search_events.php');
    if (isset($events)) {
        include template_getpath('div_monit_list_events.php');
    }
    include template_getpath('div_main_footer.php');
}
