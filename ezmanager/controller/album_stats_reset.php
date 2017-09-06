<?php

/**
 * Reset album statistics change the column "visibility" to mask statistic but this operation doesn't remove stats
 */
function index($param = array())
{
    global $input;
    global $trace_on;
    global $display_trace_stats;
   
    if (!$trace_on || !$display_trace_stats) {
        die;
    }
   
    if (!isset($input['album'])) {
        echo "Usage: index.php?action=album_stats_reset&album=ALBUM";
        die;
    }
    require_once dirname(__FILE__) . '/../lib_sql_stats.php';
    db_stats_album_hide($input['album']);
    
    require_once template_getpath('popup_album_stats_successfully_reset.php');
}
