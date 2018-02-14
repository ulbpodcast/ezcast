<?php

// used by web worker from js/uploadfile.js to submit metadata over the file
function index($param = array())
{
    global $input;
    global $submit_upload_dir;
    global $dir_date_format;
    global $accepted_media_types;
    global $upload_slice_size;


    $array = array();
    // 1) Sanity checks
    $title = trim($input['title']);
    if (!isset($title) || empty($title)) {
        log_append('warning', 'upload_init: no title');
        $array["error"] = template_get_message('Missing_title', get_lang());
        echo json_encode($array);
        die;
    }

    if ($input['type'] != 'camslide' && !in_array($input['type'], $accepted_media_types)) {
        log_append('warning', 'upload_init: ' . $input['type'] . ' is not a valid media type');
        $array["error"] = template_get_message('Invalid_type', get_lang());
        echo json_encode($array);
        die;
    }

    // 2) Creating the folder in the queue, and the metadata for the media
    $record_date = date($dir_date_format);
    $tmp_name = $record_date . '_' . $input['album'];

    $moderation = ($input['moderation'] == 'false') ? 'false' : 'true';

    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $input['title'],
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_cam' => isset($input['cam_filename']) ? $input['cam_filename'] : '',
        'submitted_slide' => isset($input['slide_filename']) ? $input['slide_filename'] : '',
        'moderation' => $moderation,
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => $record_date,
        'super_highres' => $input['keepQuality'],
        'intro' => $input['intro'],
        //currently no user option to input credits //'credits' => $input['credits'],
        'add_title' => $input['add_title'],
        'downloadable' => $input['downloadable'],
        'ratio' => $input['ratio']
    );

    $res = media_submit_create_metadata($tmp_name, $metadata);
    if (!$res) {
        log_append('warning', 'upload_init: ' . ezmam_last_error());
        $array["error"] = ezmam_last_error();
        echo json_encode($array);
        die;
    }

    // 3) saves informations for coming upload

    $path = $submit_upload_dir . '/' . $tmp_name . '/';

    $_SESSION[$tmp_name] = array(
        'path' => $path,
        'type' => $input['type'],
        'album' => $input['album'],
        'record_date' => $record_date,
        'cam' => array('index' => 0, 'finished' => false, 'concat' => -1),
        'slide' => array('index' => 0, 'finished' => false, 'concat' => -1));

    $array['values'] = array("id" => $tmp_name, "chunk_size" => $upload_slice_size);
    echo json_encode($array);
}
