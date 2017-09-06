<?php

function index($param = array())
{
    global $input;
    
    /// Load Helper ///
    include_once '../commons/view_helpers/helper_sort_col.php';
    
    if (array_key_exists('col', $input) && array_key_exists('order', $input)) {
        $colOrder = new Sort_colonne($input['col'], $input['order']);
    } else {
        $colOrder = new Sort_colonne('albumName', 'ASC');
    }
    
    ########## CHARTS DATA LOADING #################################################
    setlocale(LC_ALL, 'fr_BE');
    $DTZ = new DateTimeZone('Europe/Paris');
    $allAlbums = stat_album_get_all($colOrder->getCurrentSortCol(), $colOrder->getOrderSort());

    setlocale(LC_ALL, 'fr_BE');
    $threadsCount = stat_threads_count_all();
    $commentsCount = stat_get_nb_comments();

    $minCreationDate = get_oldest_date();
    $minDateFr = new DateTimeFrench($minCreationDate, $DTZ);
    $maxCreationDate = get_newest_date();
    $today = date('Y-m-d H:i:s');
    $todayMY = date('m/Y');
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_stats_threads.php');
    include template_getpath('div_main_footer.php');
}
