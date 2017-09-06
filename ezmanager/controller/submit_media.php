<?php

/**
 * Processes media submission
 * Used only by old web browsers (when xhr2 is not supported)
 * @global type $input
 */
function index($param = array())
{
    global $input;
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;
    global $submit_upload_dir;
    global $head_code;
    global $dir_date_format;
    global $accepted_media_types;

    // 1) Sanity checks
    $title = trim($input['title']);
    if (!isset($title) || empty($title)) {
        error_print_message('no Title');
        die;
    } elseif (count($title) > 70) {
        error_print_message('Title too long');
        die;
    }


    if ($_FILES['media']['error'] > 0) {
        log_append('error', 'submit_media: an error occurred during file upload (code ' . $_FILES['media']['error'] . ')');
        error_print_message(template_get_message('upload_error', get_lang()));
        die;
    }

    if (!in_array($input['type'], $accepted_media_types)) {
        log_append('warning', 'submit_media: ' . $input['type'] . ' is not a valid media type');
        error_print_message(template_get_message('Invalid_type', get_lang()));
        die;
    }

    // 2) Creating the folder in the queue, and the metadata for the media
    $tmp_name = date($dir_date_format) . '_' . $input['album'];
    if ($input['moderation'] == 'false') {
        $moderation = "false";
    } else {
        $moderation = "true";
    }
    
    $metadata = array(
        'course_name' => $input['album'],
        'origin' => 'SUBMIT',
        'title' => $title,
        'description' => $input['description'],
        'record_type' => $input['type'],
        'submitted_cam' => $_FILES['media']['name'],
        'submitted_mimetype' => $_FILES['media']['type'],
        'moderation' => $moderation,
        'author' => $_SESSION['user_full_name'],
        'netid' => $_SESSION['user_login'],
        'record_date' => date($dir_date_format),
        'super_highres' => $input['keepQuality'],
        'intro' => $input['intro'],
        'credits' => $input['credits'],
        'add_title' => $input['add_title'],
        'downloadable' => $input['downloadable'],
        'ratio' => $input['ratio']
    );
    //    assoc_array2metadata_file($metadata, './metadata_tmp.xml');
    $res = media_submit_create_metadata($tmp_name, $metadata);
    echo $res;
    if (!$res) {
        error_print_message(ezmam_last_error());
        die;
    }

    // 3) Uploading the media file inside the folder

    $path = $submit_upload_dir . '/' . $tmp_name . '/' . $input['type'] . '.mov';

    $res = move_uploaded_file($_FILES['media']['tmp_name'], $path);
    if (!$res) {
        error_print_message('submit_media: unable to move the uploaded file');
        die;
    }

    // 4) Calling cli_mam_insert.php so that it adds the file into ezmam
    $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . dirname($path) . ' >>' . dirname($path) . '/mam_insert.log 2>&1"|at now';

    $logger->log(
        EventType::ASSET_CREATED,
        LogLevel::NOTICE,
       "User ".$_SESSION['user_login']." submitted asset",
        array('submit_media'),
        $tmp_name,
        $_SESSION['user_full_name'],
        "todo",
        $input['album'],
        ""
    );
         
    exec($cmd, $output, $ret);

    if ($ret != 0) {
        error_print_message(' Error while trying to use cli_mam_insert: error code ' . $ret);
        die;
    }
    echo "success";
    return true;
    // 5) Displaying a confirmation alert.
    //TODO translate
    $head_code = '<script type="text/javascript">$(document).ready(function() {window.alert(\'Fichier envoyé et en cours de traitement.\');});</script>';
    redraw_page();
}
