<?php

// used by web worker from js/uploadfile.js to upload chunk of file
// The upload is serial which means that each slice of file is the sequel of
// the previous one. We can then append each slice to the previous one to
// create the movie file.
function index($param = array())
{
    global $accepted_media_types;
    global $valid_mimeType;
    global $upload_slice_size;
    global $input;

    $array = array();

    $index = $_SERVER['HTTP_X_INDEX'];
    $id = $_SERVER['HTTP_X_ID'];
    $type = $_SERVER['HTTP_X_TYPE'];

    if (!isset($id) || empty($id) || !isset($_SESSION[$id])) {
        log_append('warning', 'upload_chunk: ' . ' current upload id is not set or not valid');
        $array["error"] = template_get_message('Invalid_id', get_lang());
        echo json_encode($array);
        die;
    }

     $path= $_SESSION[$id]['path'];

    // path must be in proper format
    if (!isset($path)) {
        log_append('warning', 'upload_chunk: ' . ' cannot find file upload path');
        $array["error"] = template_get_message('Invalid_path', get_lang());
        echo json_encode($array);
        die;
    }
    
    
    // type must be in proper format
    if (!isset($type) || !in_array($type, $accepted_media_types)) {
        log_append('warning', 'upload_chunk: ' . $input['type'] . ' is not a valid media type');
        $array["error"] = template_get_message('Invalid_type', get_lang());
        echo json_encode($array);
        die;
    }

    // index must be set, and number
    if (!isset($index) || !preg_match('/^[0-9]+$/', $index)) {
        log_append('warning', 'upload_chunk: ' . $index . ' is not a valid index');
        $array["error"] = template_get_message('Invalid_index', get_lang());
        echo json_encode($array);
        die;
    }

    // index must be as expected (previous + 1)
    $current_index = $_SESSION[$id][$type]['index'];
    if ($index == $current_index) {
        $_SESSION[$id][$type]['index'] += 1;
    } else {
        log_append('warning', 'upload_chunk: expected index [' . $current_index . '] found index [' . $index . ']');
        $array["error"] = template_get_message('bad_sequence', get_lang());
        echo json_encode($array);
        die;
    }

    // we store chunks in directory named after filename
    /*    if (!file_exists("$path/" . $type . '/')) {
      mkdir("$path/" . $type . '/');
      }

      $target = "$path/" . $type . '/' . $type . '-' . $index;
     */

    /*
      // alternative way
      $putdata = fopen("php://input", "r");
      $fp = fopen($target, "w");
      while ($data = fread($putdata, 1024))
      fwrite($fp, $data);
      fclose($fp);
      fclose($putdata);
     */

    //  $input = fopen("php://input", "r");
    //  file_put_contents($target, $input);

    $target = "$path/" . $type . '.mov';

    // the incoming stream is put at the end of the file
    $input = fopen("php://input", "r");

    
    if ($input == false) {
        log_append('warning', "error opening php input stream");
        $array["error"] = template_get_message('write_error', get_lang());
        echo json_encode($array);
        die;
    }
//    
//    if(!in_array(mime_content_type($input),$valid_mimeType)){
//        log_append('error', "Mimetype is not valid");
//        $array["error"] = template_get_message('mimetype_error', get_lang()).' type:'.mime_content_type($input);
//        echo json_encode($array);
//        die;
//    }
//    
    
    if (!file_exists($target) || filesize($target) <= 2048000000) {
        /*  $fp = fopen($target, "a");
          while ($data = fread($input, $upload_slice_size)) {
          fwrite($fp, $data);
          }
          fclose($fp);
          fclose($index);
         */
        $res = file_put_contents($target, $input, FILE_APPEND | LOCK_EX);
    } else {
        // if the file is bigger than 2Go, PHP 32bit cannot handle it.
        // We then save each chunk in a separate file and concatenate
        // the final file in command line (see upload_finished())
        if (!file_exists("$path/" . $type . '/')) {
            // creates the directory for files to be concat
            mkdir("$path/" . $type . '/');
            // saves index of the first file to be concat
            $_SESSION[$id][$type]['concat'] = $index;
        }
        $target = "$path/" . $type . '/' . $type . '-' . $index;
        $input = fopen("php://input", "r");
        file_put_contents($target, $input);
        
    }
    
    if(!in_array(mime_content_type($target),$valid_mimeType)){
        log_append('error', "Mimetype is not valid");
        $array["error"] = template_get_message('mimetype_error', get_lang()).' type:'.mime_content_type($target);
        echo json_encode($array);
        die;
    }

    if ($res === false) {
        log_append('warning', 'upload_chunk: ' . "error while writting chunk $index");
        $array["error"] = template_get_message('write_error', get_lang());
        echo json_encode($array);
        die;
    }

    // required by js/fileupload.js
    $array['value'] = "OK";
    echo json_encode($array);
}
