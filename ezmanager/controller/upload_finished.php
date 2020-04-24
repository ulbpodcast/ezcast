<?php

// used by web worker from js/uploadfile.js to launch the mam_insert
function index($param = array())
{
    global $php_cli_cmd;
    global $recorder_mam_insert_pgm;
    global $input;
    global $accepted_media_types;
    global $logger;

    if (!isset($input['index'])) {
        die();
    }
    
    $array = array();

    $index = $input['index'];
    $id = $input['id'];
    $type = $input['type'];
    $path = $_SESSION[$id]['path'];

    if (!isset($_SESSION[$id])) {
        log_append('warning', 'upload_finished: ' . ' current upload id is not set or not valid');
        $array["error"] = template_get_message('Invalid_id', get_lang());
        echo json_encode($array);
        die;
    }

    if ($index != $_SESSION[$id][$type]['index']) {
        log_append('warning', 'upload_finished: ' . ' missing file chunks [' . $_SESSION[$id][$type]['index'] . '/' . $index . ']');
        $array["error"] = template_get_message('missing_chunks', get_lang());
        echo json_encode($array);
        die;
    }

    if ($_SESSION[$id][$type]['concat'] > -1) {
        // file is bigger than 2Go
        // Everything that exceeds 2Go has been saved
        // as separated files in path/type/. and
        // must be concatenated now
        $dest = $path . "/" . $type . ".mov";
        for ($i = $_SESSION[$id][$type]['concat']; $i <= $index; $i++) {
            $src = $path . "/" . $type . "/" . $type . "-" . $i;
            $cmd = "cat $src >> $dest";
            exec($cmd);
            unlink($src);
        }
        rmdir($path . '/' . $type);
    }

    $finished = true;
    if ($_SESSION[$id]['type'] == 'camslide') {
        $_SESSION[$id][$type]['finished'] = true;
        foreach ($accepted_media_types as $type) {
            $finished = $finished && $_SESSION[$id][$type]['finished'];
        }
    }

    //all files finished
    if ($finished) {
        $recording_metadata = metadata2assoc_array($path."/metadata.xml");
        $album = "";
        if ($recording_metadata) {
            $album = $recording_metadata['course_name'];
        }
        $asset_name = basename($path);

        // Calling cli_mam_insert.php so that it adds the file into ezmam
        $cmd = 'echo "' . $php_cli_cmd . ' ' . $recorder_mam_insert_pgm . ' ' . $path . ' >>' . $path . '/mam_insert.log 2>&1"|at now';

        exec($cmd, $output, $ret);

        $logger->log(
            EventType::ASSET_CREATED,
            LogLevel::NOTICE,
           "User ".$_SESSION['user_login']." submitted asset",
            array('upload_finished'),
            $asset_name,
            $_SESSION['user_full_name'],
            $_SESSION[$id]['type'],
            $album,
            ""
        );

        if ($ret != 0) {
            log_append('warning', 'upload_finished: ' . ' Error while trying to use cli_mam_insert: error code ' . $ret);
            $array["error"] = ' Error while trying to use cli_mam_insert: error code ' . $ret;
            echo json_encode($array);
            die;
            error_print_message(' Error while trying to use cli_mam_insert: error code ' . $ret);
            die;
        }
    } else {
        $array["wait"] = 'Wait until all files are submitted';
        echo json_encode($array);
        die;
    }

    $array['value'] = "OK";
    echo json_encode($array);
}
