<?php

function index($param = array())
{
    global $_POST;
    
    $datePicked = $_POST['datePicked'];
    $year_month = substr($datePicked, 3, 7) . "-" . substr($datePicked, 0, 2);

    $resultTrd = threads_count_by_month($year_month);
    $resultCmt = comments_count_by_month($year_month);

    $result = array_merge($resultTrd, $resultCmt);

    if ($result[0]['nbTrd'] == "0") {
        unset($_SESSION['monthStats']);
        include_once 'month_stats.php';
        return true;
    }

    $_SESSION['monthStats']['threadsCount'] = $result[0]['nbTrd'];
    $_SESSION['monthStats']['commentsCount'] = $result[1]['nbCmt'];
    $_SESSION['currentMonth'] = $datePicked;

    include_once template_getpath('div_stats_threads_month.php');
}
