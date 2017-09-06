<?php

/**
 * @package ezcast.commons.lib.various
 */

/**
 * Parse a JSON file
 * @param String $json_file_path
 * @return an array with the json file's content
 */

function json_to_array($json_file_path)
{
    $result_array = array();
    
    if (!file_exists($json_file_path)) {
        return false;
    }
    
    $json_file_content = file_get_contents($json_file_path);
    $json_objects = json_decode($json_file_content, true);
    $json_iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($json_objects),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $i = -1;
    foreach ($json_iterator as $key => $value) {
        if (is_array($value)) {
            $i++;
            $result_array[$i] = array();
        } else {
            $result_array[$i][$key] = $value;
        }
    }
    
    return $result_array;
}

class DateTimeFrench extends DateTime
{
    public function format($format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, parent::format($format)));
    }
}

function get_pid_from_file($filePath)
{
    $handle = fopen($filePath, "r");
    if ($handle == false) {
        return 0;
    }
    
    $pid = fgets($handle);
    fclose($handle);
    return $pid;
}

// determines if a process is running or not
function is_process_running($pid)
{
    if (!isset($pid) || $pid == '' || $pid == 0) {
        return false;
    }
  
    if (PHP_OS == "SunOS") {
        $command = "pargs";
    } else {
        $command = "ps";
    }

    exec("$command $pid", $output, $result);
    return count($output) >= 2;
}
        
function debug_to_console($data)
{
    if (is_array($data) || is_object($data)) {
        echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
    } else {
        echo("<script>console.log('PHP: ".$data."');</script>");
    }
}

/**
 * Require Once a controller
 *
 * @param String $controller_name name of the controller
 */
function requireController($controller_name)
{
    require_once './controller/'.$controller_name;
}

/**
 * Redirect the user to an other controller
 *
 * @global Array $input which was send to the current page
 * @param String $controller_name name of the new controller page
 */
function redirectToController($controller_name)
{
    global $input;
    $input['action'] = $controller_name;
    header('Location: index.php?'.http_build_query($input));
}

function get_asset_name($course_name, $record_date)
{
    return $record_date . '_' . $course_name;
}

/**
 * Returns the album full title from an album mnemonic
 * @global type $repository_path
 * @param type $album the album mnemonique
 * @return boolean the album full title if the album exists; false otherwise
 */
function get_album_title($album)
{
    global $repository_path;
    
    ezmam_repository_path($repository_path);
    //
    // Usual sanity checks
    //
    if (!ezmam_album_exists($album)) {
        return false;
    }
    
    $album_metadata = ezmam_album_metadata_get($album);
    return choose_title_from_metadata($album_metadata);
}

function choose_title_from_metadata(&$album_metadata)
{
    if (isset($album_metadata['title'])) {
        return $album_metadata['title'];
    } elseif (isset($album_metadata['description'])) {
        return $album_metadata['description'];
    } else {
        return false;
    }
}
