<?php

require_once __DIR__ . '/../commons/lib_database.php';
    
if (file_exists(__DIR__ . '/config.inc')) {
    include_once __DIR__ . '/config.inc';

    $stmt_array = stats_statements_get();
    db_prepare($stmt_array);
}


function stats_statements_get()
{
    $table_stats_month_infos = "stats_video_month_infos";
    $table_stats_view = "stats_video_view";
    $table_stats_infos = "stats_video_infos";
    $table_thread = "threads";
    
    return array(
            'album_info_empty' =>
                'SELECT 1 ' .
                'FROM ' . db_gettable($table_stats_month_infos) . ' ' .
                'WHERE album = :album AND visibility = 1 ' .
                'LIMIT 1;',
        
            'album_get_month_data' =>
                'SELECT month, '.
                    'SUM(nbr_view_total) AS total_view_total, ' .
                    'SUM(nbr_view_unique) AS total_view_unique ' .
                'FROM ' . db_gettable($table_stats_month_infos) . ' ' .
                'WHERE album = :album AND visibility = 1 ' .
                'GROUP BY month;',
            
            'video_get_month_data' =>
                'SELECT asset, asset_name, ' .
                    'SUM(nbr_view_total) AS total_view_total, ' .
                    'SUM(nbr_view_unique) AS total_view_unique ' .
                'FROM ' . db_gettable($table_stats_month_infos) . ' ' .
                'WHERE album = :album AND visibility = 1 ' .
                'GROUP BY asset, asset_name;',
        
            'video_get_view_time' =>
                'SELECT video_time, nbr_view, type ' .
                'FROM ' . db_gettable($table_stats_view) . ' ' .
                'WHERE album = :album ' .
                    'AND ' .
                    'asset = :asset ' .
                    'AND ' .
                    'visibility = 1 ' .
                'ORDER BY video_time;',
        
            'album_get_info' =>
                'SELECT '.
                    'SUM(nbr_bookmark_personal) AS bookmark_personal, ' .
                    'SUM(nbr_bookmark_official) AS bookmark_official, ' .
                    'SUM(nbr_thread) AS threads, ' .
                    'SUM(nbr_access) AS access ' .
                'FROM ' . db_gettable($table_stats_infos) . ' ' .
                'WHERE album = :album AND visibility = 1 '.
                'GROUP BY album;',
        
            'update_album_month_infos' =>
                'UPDATE '. db_gettable($table_stats_month_infos) .' '.
                    'SET album = :new_album ' .
                    'WHERE album = :old_album;',
        
            'update_album_view' =>
                'UPDATE '. db_gettable($table_stats_view) .' '.
                    'SET album = :new_album ' .
                    'WHERE album = :old_album;',
        
            'update_album_infos' =>
                'UPDATE '. db_gettable($table_stats_infos) .' '.
                    'SET album = :new_album ' .
                    'WHERE album = :old_album;',
        
            'hide_album_infos' =>
                'DELETE FROM ' .db_gettable($table_stats_infos) . ' ' .
                    'WHERE album = :album AND visibility = 1;',
        
            'hide_album_month_infos' =>
                'DELETE FROM ' .db_gettable($table_stats_month_infos) . ' ' .
                    'WHERE album = :album AND visibility = 1;',
            
            'hide_album_view' =>
                'DELETE FROM ' .db_gettable($table_stats_view) . ' ' .
                    'WHERE album = :album AND visibility = 1;'
        );
}

/**
 * Check if album stats are empty or not
 *
 * @global array $statements
 * @param String $album name
 * @return boolean True if album stats are empty, False other wise
 */
function db_stats_album_empty($album)
{
    global $statements;
    
    $statements['album_info_empty']->bindParam(':album', $album);
    
    $statements['album_info_empty']->execute();
    return !($statements['album_info_empty']->rowCount() == 1);
}

/**
 * Return all datas (view) for an album and grouped by month
 * @param album name
 * @global array $statements
 */
function db_stats_album_get_month_data($album)
{
    global $statements;
    
    $statements['album_get_month_data']->bindParam(':album', $album);
    
    $statements['album_get_month_data']->execute();
    return $statements['album_get_month_data']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Return all datas (view) for an album and grouped by video
 * @param album name
 * @global array $statements
 */
function db_stats_video_get_month_data($album)
{
    global $statements;
    
    $statements['video_get_month_data']->bindParam(':album', $album);
    
    $statements['video_get_month_data']->execute();
    return $statements['video_get_month_data']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all view of a video min per min
 * @param type $album name
 * @param type $asset name
 */
function db_stats_video_get_view_time($album, $asset)
{
    global $statements;
    
    $statements['video_get_view_time']->bindParam(':album', $album);
    $statements['video_get_view_time']->bindParam(':asset', $asset);
    
    $statements['video_get_view_time']->execute();
    return $statements['video_get_view_time']->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Get informations about an album (access and bookmarks)
 *
 * @global array $statements
 * @param string $album name
 * @return array with access, bookmarks_public, bookmarks_private
 */
function db_stats_album_infos_get($album)
{
    global $statements;
    
    $statements['album_get_info']->bindParam(':album', $album);
    
    $statements['album_get_info']->execute();
    return $statements['album_get_info']->fetchAll(PDO::FETCH_ASSOC);
}

function db_stats_update_album($album, $new_album)
{
    global $statements;
    
    $statements['update_album_month_infos']->bindParam(':old_album', $album);
    $statements['update_album_month_infos']->bindParam(':new_album', $new_album);
    $statements['update_album_month_infos']->execute();
    
    $statements['update_album_view']->bindParam(':old_album', $album);
    $statements['update_album_view']->bindParam(':new_album', $new_album);
    $statements['update_album_view']->execute();
    
    $statements['update_album_infos']->bindParam(':old_album', $album);
    $statements['update_album_infos']->bindParam(':new_album', $new_album);
    $statements['update_album_infos']->execute();
}

function db_stats_album_hide($album)
{
    global $statements;
    
    $statements['hide_album_month_infos']->bindParam(':album', $album);
    $statements['hide_album_month_infos']->execute();
    
    $statements['hide_album_view']->bindParam(':album', $album);
    $statements['hide_album_view']->execute();
    
    $statements['hide_album_infos']->bindParam(':album', $album);
    $statements['hide_album_infos']->execute();
}
