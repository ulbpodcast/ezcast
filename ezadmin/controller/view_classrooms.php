<?php

function index($param = array()) {
    global $input;

    if (isset($input['update'])) {
        db_classroom_update($input['room_ID'], $input['u_room_ID'], $input['u_name'], $input['u_ip'], $input['u_ip_remote']);
    }

    if (isset($input['post'])) {
        $room_ID = db_sanitize($input['room_ID']);
        $name = db_sanitize($input['name']);
        $ip = db_sanitize($input['IP']);
        $page = $input['page'];
        $col = $input['col'];
        $order = $input['order'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $classrooms = db_classrooms_search($room_ID, $name, $ip, -1, $col . ' ' . $order, '' . $limit . ', ' . $size);
        if($classrooms == false) {
            //todo: logger instead ?
            die("view_classrooms: db_classrooms_search failed");
        }
        
        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input["enabled"] = 1;
        $input["not_enabled"] = 1;
        $input['page'] = 1;
        $input['col'] = 'room_ID';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_classroom.php');
    if (!empty($classrooms)) {
        include template_getpath('div_list_classrooms.php');
    }
    include template_getpath('div_main_footer.php');
}

