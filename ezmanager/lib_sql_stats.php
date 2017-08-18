<?php

require_once '../commons/lib_database.php';
    
if(file_exists('config.inc')) {
    include_once 'config.inc';

    $stmt_array = stats_statements_get();
    db_prepare($stmt_array);
}
 

function stats_statements_get() {
    $table_stats_infos = "stats_video_infos";
    $table_stats_view = "stats_video_view";
    $table_thread = "threads";
    
    return array(
            'album_info_empty' => 
                'SELECT 1 ' . 
                'FROM ' . db_gettable($table_stats_infos) . ' ' .
                'WHERE album = :album ' .
                'LIMIT 1;',
        
            'album_get_view_comment' =>
                'SELECT month, '.
                    'SUM(nbr_comment) AS total_comment, ' .
                    'SUM(nbr_view_total) AS total_view_total, ' .
                    'SUM(nbr_view_unique) AS total_view_unique ' . 
                'FROM ' . db_gettable($table_stats_infos) . ' ' .
                'WHERE album = :album ' .
                'GROUP BY month;',
        
            'video_get_view_comment' => 
                'SELECT asset, ' . 
                    'SUM(nbr_comment) AS total_comment, ' .
                    'SUM(nbr_view_total) AS total_view_total, ' .
                    'SUM(nbr_view_unique) AS total_view_unique ' . 
                'FROM ' . db_gettable($table_stats_infos) . ' ' .
                'WHERE album = :album ' .
                'GROUP BY asset;',
        
            'video_get_view_time' => 
                'SELECT view_time, SUM(nbr_view) AS total_view ' .
                'FROM ' . db_gettable($table_stats_view) . ' ' .
                'WHERE album = :album ' . 
                    'AND ' .
                    'asset = :asset ' .
                'GROUP BY view_time ' .
                'ORDER BY video_time;',
        
            'threads_by_album_count' =>
                'SELECT count(*) '.
                'FROM ' . db_gettable($table_thread) . ' ' .
                'WHERE albumName like :album',
        );
}

/**
 * Check if album stats are empty or not
 * 
 * @global array $statements
 * @param String $album name
 * @return boolean True if album stats are empty, False other wise
 */
function db_stats_album_empty($album) {
    global $statements;
    
    $statements['album_info_empty']->bindParam(':album', $album);
    
    $statements['album_info_empty']->execute();
    return !($statements['album_info_empty']->rowCount() == 1);
}

/**
 * Return all datas (view and comment) for an album and grouped by month
 * @param album name
 * @global array $statements
 */
function db_stats_album_get_view_comment($album) {
    global $statements;
    
    $statements['album_get_view_comment']->bindParam(':album', $album);
    
    $statements['album_get_view_comment']->execute();
    return $statements['album_get_view_comment']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Return all datas (view and comment) for an album and grouped by video
 * @param album name
 * @global array $statements
 */
function db_stats_video_get_view_comment($album) {
    global $statements;
    
    $statements['video_get_view_comment']->bindParam(':album', $album);
    
    $statements['video_get_view_comment']->execute();
    return $statements['video_get_view_comment']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all view of a video min per min
 * @param type $album name
 * @param type $asset name
 */
function db_stats_video_get_view_time($album, $asset) {
    global $statements;
    
    $statements['video_get_view_time']->bindParam(':album', $album);
    $statements['video_get_view_time']->bindParam(':asset', $asset);
    
    $statements['video_get_view_time']->execute();
    return $statements['video_get_view_time']->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Get number of threads for a specific album
 * 
 * @global array $statements
 * @param string $album name
 * @return int number of thread
 */
function db_stats_album_threads_get($album) {
    global $statements;
    
    $statements['threads_by_album_count']->bindParam(':album', $album);
    
    $statements['threads_by_album_count']->execute();
    $result = $statements['threads_by_album_count']->fetchAll(PDO::FETCH_COLUMN);
    return count($result) > 0 ? $result[0] : 0;
}