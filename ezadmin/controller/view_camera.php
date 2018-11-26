<?php

require_once 'lib_sql_event.php';

function index($param = array())
{
    global $input;

    if (empty($input['room']) && $input['room_name']) {
        die;
    }

    $room = $input['room'];
    $room_name = htmlentities($input['room_name']);

    /*$hostsLLN=array("130.104.10.60","130.104.10.243","130.104.250.157", "130.104.10.125","130.104.11.125","130.104.10.60","130.104.11.253");
    $hostsWOLU=array("130.104.70.93", "130.104.70.56","130.104.70.133","130.104.70.77","130.104.70.149","130.104.70.109","130.104.70.141","130.104.69.250");

	$recorders=array('A10' => array("130.104.10.61","LLN: Science A10") ,
                 'S21' => array("130.104.11.124","LLN: Socrate 21") ,
                 'S20' => array("130.104.11.123","LLN: Socrate 20") ,
                 'M11' => array("130.104.11.252","LLN: Montesquieu 11") ,
                 'C10' => array("130.104.11.122","LLN: Coubertin 10") ,
                //WOLUWE
                 'CONFA' => array("130.104.70.125","WOLU: Pavillon des conférences Salle A") ,
                 'AF' => array("130.104.70.61","WOLU: Auditoire F") ,
                 'AE' => array("130.104.70.60","WOLU: Auditoire E") ,
                 'AD' => array("130.104.70.59","WOLU: Auditoire D") ,
                 'AC' => array("130.104.70.58","WOLU: Auditoire C") ,
                 'AB' => array("130.104.70.57","WOLU: Auditoire B") ,
                 'AA' => array("130.104.69.251","WOLU: Auditoire A") 
    
    );*/

    $classroom = db_classroom_from_name_get_ip($room);

    if(empty($classroom))
        $error = template_get_message("missing_audience", get_lang());


    // Display page
    include template_getpath('div_main_header.php');
    if($enable_control_panel_options)
        include template_getpath('div_display_camera.php');
    include template_getpath('div_main_footer.php');
}
