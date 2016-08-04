<?php

require_once 'lib_sql_event.php'; 
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';

const URL_ADR = '/ezrecorder/services/state.php';


function index($param = array()) {
    global $input;
    global $logger;
    
    global $onlyRecording;
    global $onlyOnline;
    
    $onlyRecording = false;
    $onlyOnline = false;

    
    if (isset($input['update'])) {
        db_classroom_update($input['room_ID'], $input['u_room_ID'], $input['u_name'], $input['u_ip'], $input['u_ip_remote']);
    }
    
    if(array_key_exists('page', $input)) {
        $pagination = new Pagination($input['page'], 10);
    } else {
        $pagination = new Pagination(1, 10);
    }
    if(array_key_exists('col', $input) && array_key_exists('order', $input)) {
        $colOrder = new Sort_colonne($input['col'], $input['order']);
    } else {
        $colOrder = new Sort_colonne('room_ID');
    }
    

    if (isset($input['post'])) {
        $sqlListClassrooms = db_classrooms_search(empty_str_if_not_def('room_ID', $input),
                empty_str_if_not_def('name', $input), 
                empty_str_if_not_def('ip', $input),
                empty_str_if_not_def('only_classroom_active', $input), 
                $colOrder->getCurrentSortCol(), $colOrder->getOrderSort(),
                $pagination->getStartElem(), $pagination->getElemPerPage());
        
        // If only recording
        if(array_key_exists('being_record', $input)) {
            $onlyRecording = true;
            $onlyOnline = true;
            $input['only_online'] = true;
            
        } else if(array_key_exists('only_online', $input)) {
            $onlyOnline = true;
        }
        
        $listClassrooms = array();
        foreach($sqlListClassrooms as &$classroom) {
            
            if(!$classroom['enabled'] && !$onlyOnline && !$onlyRecording) {
                $listClassrooms[] = $classroom;
                continue;
            }
            
            // Timeout after one second
            $ctx = stream_context_create(array( 
                'http' => array( 
                    'timeout' => 1 
                    ) 
                ) 
            ); 
            // Get JSON informations
            $json = @file_get_contents('http://'.$classroom['IP'].URL_ADR, 0, $ctx);
            if($json == null) {
                $classroom['online'] = false;
                if(!must_be_remove($classroom)) {
                    echo "add: ".$classroom;
                    $listClassrooms[] = $classroom;
                }
                continue;
            }
            $classroom['online'] = true;
            
            
            $data = json_decode($json);
            $classroom['recording'] = isset($data->recording) && $data->recording == 1;
            if(!$classroom['recording'] && !must_be_remove($classroom)) {
                $listClassrooms[] = $classroom;
            }
            
            
            if(!$classroom['recording']) {
                continue;
            }
            
            if(isset($data->status_general)) {
                $classroom['status_general'] = $data->status_general;
            }
            
            if(isset($data->status_cam)) {
                $classroom['status_cam'] = $data->status_cam;
            }
            
            if(isset($data->status_slides)) {
                $classroom['status_slides'] = $data->status_cam;
            }
            
            if(isset($data->author)) {
                $classroom['author'] = $data->author;
            }
            
            if(isset($data->asset)) {
                $classroom['asset'] = $data->asset;
                $loglevel = db_event_get_event_loglevel_most($data->asset);
                if($loglevel >= 0) {
                    $classroom['loglevel'] = $logger->get_log_level_name($loglevel);
                }
            }
            
            if(isset($data->course)) {
                $classroom['course'] = $data->course;
            }
            
            $listClassrooms[] = $classroom;
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

function must_be_remove($element) {
    global $onlyOnline;
    global $onlyRecording;
    
    return ($element['online'] == false && $onlyOnline) || 
            (array_key_exists('recording', $element) && 
            $onlyRecording &&
            $element['recording']);
    
}

