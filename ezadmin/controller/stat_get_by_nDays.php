<?php

function index($param = array())
{
    global $input;

    $nDays = $input['nDays'];

    $todayTemp = date('Y-m-d H:i:s');
    $today = $todayTemp;

    $nDaysDate = date('Y-m-d H:i:s', strtotime('-' . $nDays . ' days', strtotime($todayTemp)));

    $resultTrd = threads_count_by_date_interval($nDaysDate, $today);
    $resultCmt = comments_count_by_date_interval($nDaysDate, $today);

    $result = array_merge($resultTrd, $resultCmt);

    if ($result[0]['nbTrd'] == "0") {
        unset($_SESSION['nDaysStats']);
        include_once template_getpath('div_stats_threads_nDays.php');
        return true;
    }

    $_SESSION['nDaysStats']['nDaysEarlier'] = $nDaysDate;
    $_SESSION['nDaysStats']['nDaysLater'] = $today;
    $_SESSION['nDaysStats']['threadsCount'] = $result[0]['nbTrd'];
    $_SESSION['nDaysStats']['commentsCount'] = $result[1]['nbCmt'];

    include_once template_getpath('div_stats_threads_nDays.php');

    return true;
}
