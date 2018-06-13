<?php

function index($param = array())
{
    global $input;

    $users = array();

    if (isset($input['post'])) {
        $user_ID = db_sanitize($input['user_ID']);
        $forename = db_sanitize($input['forename']);
        $surname = db_sanitize($input['surname']);
        $intern = (isset($input['intern'])&& $input['intern']) ? 1 : 0;
        $extern = (isset($input['extern'])&& $input['extern']) ? 1 : 0;
        $is_admin = (isset($input['is_admin'])&& $input['is_admin']) ? 1 : 0;
        $is_not_admin = (isset($input['is_not_admin'])&& $input['is_not_admin']) ? 1 : 0;
        $page = intval($input['page']);
        $col = addslashes($input['col']);
        $order = $input['order'] == 'ASC' ? 'ASC' : 'DESC';
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $users = db_users_list($user_ID, $surname, $forename, ($intern == $extern) ? -1 : (($extern == 1) ? 'external' : 'internal'), ($is_admin == $is_not_admin) ? -1 : $is_admin, $col . ' ' . $order, '' . $limit . ', ' . $size);

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input['intern'] = 1;
        $input['extern'] = 1;
        $input['is_admin'] = 1;
        $input['is_not_admin'] = 1;
        $input['page'] = 1;
        $input['col'] = 'user_ID';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_user.php');
    if (!empty($users)) {
        include template_getpath('div_list_users.php');
    }
    include template_getpath('div_main_footer.php');
}
