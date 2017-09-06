<?php

/**
 * It's the function for recept the file and move in the queues in submit_upload ( progress_bar )
 * @global type $submit_upload_dir
 * @global type $dir_date_format
 * @global type $head_code
 * @global type $accepted_media_types
 */
function index($param = array())
{
    global $submit_upload_dir;
    global $dir_date_format;
    global $head_code;
    global $accepted_media_types;
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;
    global $logger;

    $input = array_merge($_GET, $_POST);

    if ($_FILES['media']['error'] > 0) {
        error_print_message(template_get_message('upload_error', get_lang()));
        log_append('error', 'submit_media: an error occurred during file upload (code ' . $_FILES['media']['error']);
        die;
    }
    list($type, $subtype) = explode('/', $_FILES['media']['type']);


    if (!in_array($input['type'], $accepted_media_types)) {
        error_print_message(template_get_message('Invalid_type', get_lang()));
        log_append('warning', 'submit_media: ' . $input['type'] . ' is not a valid media type');
        die;
    }

    // 2) Creating the folder in the queue, and the metadata for the Filedata
    $tmp_name = date($dir_date_format) . '_' . $input['album'];

    //$moderation = ($input['moderation']) ? 'true' : 'false';
    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $input['title'],
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_filename' => $_FILES['media']['name'],
        'submitted_mimetype' => $_FILES['media']['type'],
        'moderation' => $input['moderation'],
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => date($dir_date_format),
        'super_highres' => $input['keepQuality']
    );

    $res = media_submit_create_metadata($tmp_name, $metadata);

    // 3) Uploading the media file inside the folder

    $path = $submit_upload_dir . '/' . $tmp_name . '/' . $input['type'] . '.mov';

    $res = move_uploaded_file($_FILES['media']['tmp_name'], $path);

    // 4) Calling cli_mam_insert.php so that it adds the file into ezmam
    $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . dirname($path) . '  >>' . dirname($path) . '/mam_insert.log 2>&1"|at now';

    $logger->log(
        EventType::ASSET_CREATED,
        LogLevel::NOTICE,
       "User ".$_SESSION['user_login']." submitted asset",
        array('submit_media_progress_bar'),
        $tmp_name,
        $_SESSION['user_full_name'],
        "todo",
        $input['album'],
        ""
    );
         
    exec($cmd, $output, $ret);
    if ($ret != 0) {
        error_print_message('Error while trying to use cli_mam_insert: error code ' . $ret);
        die;
    }

    redraw_page();
}
