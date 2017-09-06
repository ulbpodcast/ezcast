<?php

require_once 'lib_sql_event.php';

const URL_ADR = '/ezrecorder/services/state.php';
const TIMEOUT = 10;

function index($param = array())
{
    global $input;
    global $logger;
    session_write_close();
    
    if (!isset($input['classroomId'])) {
        print_error_status(0);
        return;
    }
    
    // Get IP from name (database request)
    $room_id = htmlspecialchars($input['classroomId']);
    $result = db_classroom_from_name_get_ip($room_id);
    
    if ($result != null && count($result) == 1) {
        $ip = $result[0];
        $json = @url_get_contents('http://'.$ip.URL_ADR, TIMEOUT);
        $data = json_decode($json);
        if ($data != null) {
            $data->status = 1;
            
            if (isset($data->asset)) {
                $loglevel = db_event_get_event_loglevel_most($data->asset);
                if ($loglevel >= 0) {
                    $data->loglevel = $logger->get_log_level_name($loglevel);
                } else {
                    $data->loglevel = '';
                }
            }
            
            print_data_status($data);
            return;
        }
    }
    
    print_error_status(0);
}

function print_data_status($data)
{
    echo json_encode($data);
}

function print_error_status()
{
    echo json_encode(array(
            'status' => 0
        ));
}

function url_get_contents($url, $timeout)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //timeout in seconds
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
