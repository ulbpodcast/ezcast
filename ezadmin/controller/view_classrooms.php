<?php

require_once 'lib_sql_event.php';
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';


function index($param = array())
{
    global $input;
    global $logger;
    
    global $onlyRecording;
    global $onlyOnline;
    
    $MAX_CLASSROOMS_PER_PAGE = 50;
    
    $onlyRecording = false;
    $onlyOnline = false;

    
    if (isset($input['update'])) {
        db_classroom_update(trim($input['a_room_ID']), $input['u_room_ID'], $input['u_name'], $input['u_ip'], $input['u_ip_remote']);
        notify_changes(); //we must write new allowed classrooms IP's
    }
    
    if (array_key_exists('page', $input)) {
        $pagination = new Pagination($input['page'], $MAX_CLASSROOMS_PER_PAGE);
    } else {
        $pagination = new Pagination(1, $MAX_CLASSROOMS_PER_PAGE);
    }
    if (array_key_exists('col', $input) && array_key_exists('order', $input)) {
        $colOrder = new Sort_colonne($input['col'], $input['order']);
    } else {
        $colOrder = new Sort_colonne('room_ID');
    }
    

    if (isset($input['post'])) {
        $sqlListClassrooms = db_classrooms_search(
            empty_str_if_not_def('room_ID', $input),
                empty_str_if_not_def('name', $input),
                empty_str_if_not_def('ip', $input),
                empty_str_if_not_def('only_classroom_active', $input),
                $colOrder->getCurrentSortCol(),
            $colOrder->getOrderSort(),
                $pagination->getStartElem(),
            $pagination->getElemPerPage()
        );
        
        // If only recording
        if (array_key_exists('being_record', $input)) {
            $onlyRecording = true;
            $onlyOnline = true;
            $input['only_online'] = true;
        } elseif (array_key_exists('only_online', $input)) {
            $onlyOnline = true;
        }
        
        $listClassrooms = array();
        foreach ($sqlListClassrooms as &$classroom) {
            if (!$onlyOnline || $classroom['enabled']) {
                $listClassrooms[] = $classroom;
            }
        }
        
        $pagination->setTotalItem(db_found_rows());
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_classroom.php');
    if (!empty($listClassrooms)) {
        include template_getpath('div_list_classrooms.php');
    }
    include template_getpath('div_main_footer.php');
}
