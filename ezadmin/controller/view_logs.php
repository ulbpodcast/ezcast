<?php

/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';


function index($param = array())
{
    global $input;
    
    $pagination = new Pagination();
    if (isset($input['post']) && !empty($input['post'])) {
        if (array_key_exists('page', $input)) {
            $pagination = new Pagination($input['page']);
        }
        
        $logs = db_logs_get(
        
            $input['date_start'],
        
            $input['date_end'],
        
            $input['table'],
        
            $input['author'],
                $pagination->getStartElem(),
        
            $pagination->getElemPerPage()
        
        );

        $pagination->setTotalItem(db_found_rows());
    }
    
    // Create variable to specific whitch option is selected
    $selectTable = '';
    if (isset($input) && array_key_exists('table', $input)) {
        $selectTable = $input['table'];
    }
    
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_logs.php');
    if (!empty($logs)) {
        include template_getpath('div_list_logs.php');
    }
    include template_getpath('div_main_footer.php');
}
