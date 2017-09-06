<?php

function index($param = array())
{
    global $input;

    $courses = array();

    if (isset($input['post'])) {
        $course_code = db_sanitize($input['course_code']);
        $teacher = db_sanitize($input['teacher']);
        $intern = $input['intern'] ? 1 : 0;
        $extern = $input['extern'] ? 1 : 0;
        $has_albums = $input['has_albums'] ? 1 : 0;
        $no_albums = $input['no_albums'] ? 1 : 0;
        $in_recorders = $input['in_recorders'] ? 1 : 0;
        $out_recorders = $input['out_recorders'] ? 1 : 0;
        $with_teacher = $input['with_teacher'] ? 1 : 0;
        $without_teacher = $input['without_teacher'] ? 1 : 0;
        $page = $input['page'];
        $col = $input['col'];
        $order = $input['order'];
        $size = 20;
        $limit = (intval($page) - 1) * $size;

        $courses = db_courses_search(
            $course_code,
            $teacher,
            $extern,
            $intern,
            $has_albums == $no_albums ? -1 : $has_albums,
                $in_recorders == $out_recorders ? -1 : $in_recorders,
            $with_teacher == $without_teacher ? -1 : $with_teacher,
                $col . ' ' . $order,
            '' . $limit . ', ' . $size
        );

        $rows = db_found_rows();
        $max = intval($rows / 20) + 1;
    } else {
        // default options
        $input['intern'] = 1;
        $input['extern'] = 1;
        $input['has_albums'] = 1;
        $input['no_albums'] = 1;
        $input['in_recorders'] = 1;
        $input['out_recorders'] = 1;
        $input['with_teacher'] = 1;
        $input['without_teacher'] = 1;
        $input['page'] = 1;
        $input['col'] = 'course_code';
        $input['order'] = 'ASC';
    }

    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_search_course.php');
    if (!empty($courses)) {
        include template_getpath('div_list_courses.php');
    }
    include template_getpath('div_main_footer.php');
}
