<?php

function index($param = array()) {
    global $input;

    if (isset($input['post']) && !empty($input['post'])) {
        $page = $input['page'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $logs = db_logs_get($input['date_start'], $input['date_end'], $input['table'], $input['author'], '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_logs.php');
    if (!empty($logs)) {
        include template_getpath('div_list_logs.php');
    }
    include template_getpath('div_main_footer.php');
}
