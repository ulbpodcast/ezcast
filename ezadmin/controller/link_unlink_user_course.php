<?php

function index($param = array())
{
    global $input;

    if (empty($input['course_code'])) {
        die;
    }

    $netid = trim($input['id']);
    switch ($input['query']) {
        case 'link':
            $info = db_users_courses_create($input['course_code'], $netid);
            if (!$info) {
                echo json_encode(array('error' => '1'));
                return;
            }
            echo json_encode(array(
                'id' => $netid,
                'netid' => $info['user']['user_ID'],
                'name' => $info['user']['surname'] . ' ' . $info['user']['forename'],
                'origin' => 'internal'
            ));
            db_log(db_gettable('users_courses'), 'Added link between user ' . $info['user']['user_ID'] . ' and course ' . $input['course_code'], $_SESSION['user_login']);
            break;
        case 'unlink':
            if (!db_users_courses_delete($netid)) {
                echo json_encode(array('error' => '1'));
            } else {
                echo json_encode(array('success' => '1'));
                db_log(db_gettable('classrooms'), 'Removed link between user ' . $input['id'] . ' and course ' . $input['course_code'], $_SESSION['user_login']);
            }

            break;
        default:
            echo json_encode(array('error' => 'Unknown query'));
            break;
    }

    notify_changes();
}
