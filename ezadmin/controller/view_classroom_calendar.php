<?php

require_once 'lib_sql_event.php';


function index($param = array())
{
    global $input;
    
    global $error_asset;
    global $max_count;
    global $MIN_TIME_RECORD;
    
    $error_asset = array('Error_date' => array(),
                        'Error_time' => array());
    $max_count = 0;
    $MIN_TIME_RECORD = 180; // min time of a record (in second)

    
    $START_HOUR = 11; // must be divided by 2
    $END_HOUR = 44; // must be divided by 2
    
    
    if (array_key_exists('post', $input)) {
        if (array_key_exists('classroom', $input) && array_key_exists('nweek', $input)) {
            $recordRepartition = db_event_get_record_after_date(
                calcul_date($input['nweek']),
                        empty_str_if_not_def('classroom', $input),
                        empty_str_if_not_def('courses', $input),
                        empty_str_if_not_def('teacher', $input),
                        empty_str_if_not_def('cam_slide', $input), //cam_slide not currently in form, add it if you need it
                        get_courses_excluded_from_stats()
            );
            
            $resultRecord = calcul_hour_by_hour($recordRepartition);
        }
    }
    
    $js_classroom = "";
    $listClassroom = array();
    foreach (db_classrooms_list() as $classroomInfos) {
        $listClassroom[] = "'".$classroomInfos['name']."'";
    }
    $js_classroom = '['.implode(', ', $listClassroom).']';
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_search_calendar.php');
    if (!empty($resultRecord)) {
        include template_getpath('div_monit_classroom_calendar.php');
    }
    include template_getpath('div_main_footer.php');
}

function calcul_date($nbr_day)
{
    // 86400 = one day
    // (86400*7)= 604800 = one week
    return date("Y-m-d H:i:s", strtotime("monday this week")-($nbr_day*604800));
}


/**
 * Calcul when the record must be show
 *
 *
 * @global Array $error_asset list of asset who have an error
 * @global Integer $MIN_TIME_RECORD minimum time of a record
 * @param Array $recordData sql data for all asset
 * @return ArrayList with number of day in key and array in value. In this value there
 * is hour in key and asset in value
 */
function calcul_hour_by_hour($recordData)
{
    global $error_asset;
    global $MIN_TIME_RECORD;
    
    // Init day week
    $resultRecord = array();
    for ($i = 1;$i <= 7; ++$i) {
        $resultRecord[$i] = array();
    }
    
    foreach ($recordData as $record) {
        $nbrDayStart = get_day_in_week_of_date($record['start_time']);
        $nbrDayEnd = get_day_in_week_of_date($record['end_time']);
        
        if ($nbrDayStart != $nbrDayEnd) {
            // TODO: show this !
            $error_asset['Error_date'][] = $record['asset'];
            continue;
        }
         
        if ((strtotime($record['end_time'])-strtotime($record['start_time'])) < $MIN_TIME_RECORD) {
            $error_asset['Error_time'][] = $record['asset'];
            continue;
        }
        
        $timeStart = calcul_int_date($record['start_time'], true);
        $timeEnd = calcul_int_date($record['end_time']);
        
        for ($hour = $timeStart; $hour <= $timeEnd; ++$hour) {
            if (!array_key_exists($hour, $resultRecord[$nbrDayStart])) {
                $resultRecord[$nbrDayStart][$hour] = array('nbr_cours' => 0);
            }
            
            $resultRecord[$nbrDayStart][$hour] = insert_record_in_an_hour(
                                    $resultRecord[$nbrDayStart][$hour],
                                    $record
            
            );
        }
    }
    
    return $resultRecord;
}

/**
 * Get the number of the day in this week (1 for monday, 2 for tuesday, ...)
 *
 * @param Date $date with format YYYY-MM-DD HH:mm:ss
 * @return integer the number of the day week
 */
function get_day_in_week_of_date($date)
{
    return date("N", strtotime($date));
}

function get_day_number($date)
{
    return date("zY", strtotime($date));
}

/**
 * Convert date and hour in an integer
 * It's the hour*2 and 30 min = one
 *
 * @param Data $date format YYYY-MM-DD HH:mm:ss
 */
function calcul_int_date($date, $start = false)
{
    $nbrHour = date("G", strtotime($date));
    $nbrMin = date("i", strtotime($date));
    
    
    if ($nbrMin >= 50) {
        ++$nbrHour;
        $nbrMin = 0;
    } elseif ($nbrMin < 10 && !$start) {
        --$nbrHour;
        $nbrMin = 31;
    } elseif ($nbrMin > 20) {
        $nbrMin += 10;
    }
    
    return $nbrHour*2 + ((int)($nbrMin/30));
}

/**
 * Get color depending on the importance
 *
 * @param int $nbr importance
 * @return String the color (rgb)
 */
function number_to_color($nbr)
{
    global $max_count;
    
    if ($max_count <= 7) {
        $step = 30;
    } elseif ($max_count <= 15) {
        $step = 15;
    } elseif ($max_count <= 21) {
        $step = 10;
    } elseif ($max_count <= 42) {
        $step = 5;
    } else {
        $step = 1;
    }
    $t = $step*($nbr-1);
    return 'rgb('.(217-$t).', '.(237-$t).', 247)';
}

function insert_record_in_an_hour($listToInsert, $data)
{
    // If not exist
    if (!isset($data['course']) || !array_key_exists($data['course'], $listToInsert)) {
        $listToInsert[$data['course']] = array();
        $subList = &$listToInsert[$data['course']];
        foreach ($data as $key => $value) {
            $subList[$key] = array($value);
        }
        $subList['day_nbr'] = array(get_day_number($data['start_time']));
        increment_nbr_new_cours($listToInsert['nbr_cours']);
        $subList['str_infos'] = generate_str_info($data['course'], $subList);
        
        // If already exist
    } else {
        $subList = &$listToInsert[$data['course']];
        
        $dayNbr = get_day_number($data['start_time']);
        if (!in_array($dayNbr, $subList['day_nbr'])) {
            increment_nbr_new_cours($listToInsert['nbr_cours']);
            $subList['day_nbr'][] = $dayNbr;
        }
        
        foreach ($subList as $key => $value) {
            if (array_key_exists($key, $data) && !in_array($data[$key], $subList[$key])) {
                $subList[$key][] = $data[$key];
            }
        }
        
        
        $subList['str_infos'] = generate_str_info($data['course'], $subList);
    }
    
    return $listToInsert;
}

/**
 * Increment the number of total cours
 *
 * @global Integer $max_count number of maximum cours at the same time
 * @param Array $listToInsert with all informations
 */
function increment_nbr_new_cours(&$newNbrCours)
{
    global $max_count;
    
    ++$newNbrCours;
    if ($newNbrCours > $max_count) {
        $max_count = $newNbrCours;
    }
}


/**
 * Generate a string with all data about a cours
 *
 * @param Array $cours_data data of this cours
 * @return String with data
 */
function generate_str_info($cours_asset, $cours_data)
{
    $strRes = "";
    
    $strRes .= '<h5>'.$cours_asset.'</h5>';
    for ($nbrTime = 0; $nbrTime < count($cours_data['start_time']); ++$nbrTime) {
        $start = date("H:i", strtotime($cours_data['start_time'][$nbrTime]));
        $end = date("H:i j/m", strtotime($cours_data['end_time'][$nbrTime]));
                
        $strRes .= '<span class="glyphicon glyphicon-time" aria-hidden="true"></span> ';
        $strRes .= $start.' - '.$end;
        $strRes .= '<br />';
    }
    
    foreach ($cours_data['author'] as $author) {
        $strRes .= '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ';
        $strRes .= $author;
        $strRes .= '<br />';
    }
    
    foreach ($cours_data['asset'] as $asset) {
        $strRes .= $asset.'<br />';
    }
    
    return $strRes;
}
