<?php
/*
* EZCAST EZadmin
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
*
* This software is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * @package ezcast.ezadmin.lib.sqlRequest
 */
require_once '../commons/lib_database.php';


$stmts = stat_statements_get();
db_prepare($stmts);

function stat_statements_get()
{
    return array(
        'thread_get' =>
            'SELECT * FROM ' . db_gettable('threads') . ' ' .
            'WHERE id = :thread_id '.
            'LIMIT 1',
        
        'thread_oldest_get' =>
            'SELECT min(creationDate) as minDate FROM ' . db_gettable('threads') . ' ' .
            'LIMIT 1',
        
        'thread_newest_get' =>
            'SELECT max(creationDate) as maxDate FROM ' . db_gettable('threads') . ' ' .
            'LIMIT 1',
        
        'threads_all_get' =>
            'SELECT * FROM ' . db_gettable("threads"),
        
        'threads_by_asset_get' =>
            'SELECT * FROM ' . db_gettable("threads") . ' ' .
            'WHERE albumName like :album_name ' .
            'AND assetName like :asset_name ' .
            'ORDER BY timecode',
        
        'threads_count' =>
            'SELECT count(*) FROM ' . db_gettable('threads'),
        
        'threads_by_month_count' =>
            'SELECT count(*) as nbTrd FROM ' . db_gettable('threads') . ' ' .
            'WHERE creationDate like :creation_date',
        
        'threads_by_interval_count' =>
            'SELECT count(*) as nbTrd FROM ' . db_gettable('threads') . ' ' .
            'WHERE creationDate between :earlier and :later',
        
        'threads_by_album_count' =>
            'SELECT count(*) FROM ' . db_gettable('threads') . ' ' .
            'WHERE albumName like :album_name',
        
        'threads_by_album_by_month_count' =>
            'SELECT count(*) as nbTrd FROM ' . db_gettable('threads') . ' ' .
            'WHERE albumName like :album_name ' .
            'AND creationDate like :creation_date',
        
        'threads_by_album_by_interval_count' =>
            'SELECT count(*) as nbTrd FROM ' . db_gettable('threads') . ' ' .
            'WHERE albumName like :album_name ' .
            'AND creationDate between :earlier AND :later',
        
        'threads_by_asset_count' =>
            'SELECT creationDate, albumName, assetName, count(*) FROM ' . db_gettable('threads') . ' ' .
            'GROUP BY EXTRACT(DAY from creationDate), albumName',
        
        'comments_by_thread_get' =>
            'SELECT * FROM ' . db_gettable("comments") . ' ' .
            'WHERE thread = :thread_id',
        
        'comments_count' =>
            'SELECT count(*) FROM ' . db_gettable('comments'),
        
        'comments_by_album_count' =>
            'SELECT count(*) FROM ' . db_gettable('comments') . ' ' .
            'JOIN ' . db_gettable('threads') . ' as t ' .
                'ON t.id = thread ' .
                'AND albumName like :album_name',
        
        'comments_by_month_count' =>
            'SELECT count(*) as nbCmt FROM ' . db_gettable('comments') . ' ' .
            'WHERE creationDate like :creation_date',
        
        'comments_by_interval_count' =>
            'SELECT count(*) as nbCmt FROM ' . db_gettable('comments') . ' ' .
            'WHERE creationDate between :earlier and :later',
        
        'comments_by_album_by_month_count' =>
            'SELECT count(*) FROM ' . db_gettable('comments') . ' ' .
            'JOIN ' . db_gettable('threads') . ' as t ' .
                'ON t.id = thread ' .
                'AND albumName like :album_name ' .
            'WHERE t.creationDate like :creation_date',
        
        'comments_by_album_by_interval_count' =>
            'SELECT count(*) FROM ' . db_gettable('comments') . ' c ' .
            'JOIN '. db_gettable('threads') . ' as t ' .
                'ON t.id = thread ' .
                'AND albumName like :album_name ' .
            'WHERE c.creationDate between :earlier and :later',
        
        'albums_all_get' =>
            'SELECT DISTINCT albumName FROM ' . db_gettable('threads'),
        
        'albums_count' =>
            'SELECT count(*) FROM ' . db_gettable('threads') . ' ' .
            'WHERE albumName like :album_name',
        
         
    );
}

/**
 * Returns the thread with the given id
 * @global null $db
 * @param int $_id
 * @return array or false if wrong parameter
 */
function threads_select_by_id($thread_id)
{
    global $statements;
    
    if (!$thread_id) {
        return false;
    }
    $statements['thread_get']->bindParam(':thread_id', $thread_id);
    $statements['thread_get']->execute();
    
    return $statements['thread_get']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns an array with all saved threads
 * @global null $db
 * @return type
 */
function threads_select_all()
{
    global $statements;
    
    $statements['threads_all_get']->execute();
    return $statements['threads_all_get']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns an array with all threads published on an asset
 * @global null $db
 * @param type $albumName
 * @param type $assetName
 * @return type
 */
function threads_select_all_by_asset($albumName, $assetName)
{
    global $statements;
    
    $statements['threads_by_asset_get']->bindParam(':album_name', $albumName);
    $statements['threads_by_asset_get']->bindParam(':asset_name', $assetName);
    $statements['threads_by_asset_get']->execute();
    
    return $statements['threads_by_asset_get']->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Returns an array with all comments posted on a thread
 * @global null $db
 * @param type $_id
 * @return array
 */
function comments_select_by_threadId($thread_id)
{
    global $statements;

    if (!$thread_id) {
        return false;
    }
    
    
    $statements['comments_by_thread_get']->bindParam(':thread_id', $thread_id);
    $statements['comments_by_thread_get']->execute();
    
    return $statements['comments_by_thread_get']->fetchAll(PDO::FETCH_ASSOC);
}


##### STATS FUNCTIONS #######################################################

/**
 * Returns all album name
 * @global null $db
 * @return array
 */
function album_get_all($colOrder = "", $orderSort = "")
{
    global $db_object;
    
    $strSQL = 'SELECT DISTINCT albumName FROM ' . db_gettable('threads');
    if ($colOrder != "") {
        $strSQL .= " ORDER BY ".$colOrder;
        if ($orderSort != "") {
            $strSQL .= ' '.$orderSort;
        }
    }
    
    $reqSQL = $db_object->prepare($strSQL);
    $reqSQL->execute();
    
    return $reqSQL->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns the number of threads published by album
 * @global null $db
 * @param type $albumName
 * @return int
 */
function threads_count_by_album($albumName)
{
    global $statements;
        
    $statements['threads_by_album_count']->bindParam(':album_name', $albumName);
    $statements['threads_by_album_count']->execute();
    
    $result = $statements['threads_by_album_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the number of threads by album during a month
 * @global null $db
 * @param type $albumName
 * @param type $month
 * @return int
 */
function threads_count_by_album_and_month($albumName, $month)
{
    global $statements;
    
    $creation_date = substr($month, 3, 7)."-".substr($month, 0, 2)."-%";
    $statements['threads_by_album_by_month_count']->bindParam(':album_name', $albumName);
    $statements['threads_by_album_by_month_count']->bindParam(':creation_date', $creation_date);
    $statements['threads_by_album_by_month_count']->execute();
    
    $result = $statements['threads_by_album_by_month_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the number of threads by album during a date interval
 * @global null $db
 * @param type $albumName
 * @param type $earlier
 * @param type $later
 * @return int
 */
function threads_count_by_album_and_date_interval($albumName, $earlier, $later)
{
    global $statements;
    
    $statements['threads_by_album_by_interval_count']->bindParam(':album_name', $albumName);
    $statements['threads_by_album_by_interval_count']->bindParam(':earlier', $earlier);
    $statements['threads_by_album_by_interval_count']->bindParam(':later', $later);
    $statements['threads_by_album_by_interval_count']->execute();
    
    $result = $statements['threads_by_album_by_interval_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Return the number of comments by album
 * @global null $db
 * @param type $albumName
 * @return int
 */
function comments_count_by_album($albumName)
{
    global $statements;
    
    $statements['comments_by_album_count']->bindParam(':album_name', $albumName);
    $statements['comments_by_album_count']->execute();
    
    $result = $statements['comments_by_album_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Return the number of comments by album during a specified month
 * @global null $db
 * @param type $albumName
 * @param type $currentMonth
 * @return int
 */
function comments_count_by_album_and_month($albumName, $currentMonth)
{
    global $statements;
    
    $creation_date = substr($currentMonth, 3, 7)."-".substr($currentMonth, 0, 2)."-%";
    $statements['comments_by_album_by_month_count']->bindParam(':album_name', $albumName);
    $statements['comments_by_album_by_month_count']->bindParam(':creation_date', $creation_date);
    $statements['comments_by_album_by_month_count']->execute();
    
    $result = $statements['comments_by_album_by_month_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the number of threads saved
 * @global null $db
 * @return int
 */
function threads_count_all()
{
    global $statements;
    
    $statements['threads_count']->execute();
    
    $result = $statements['threads_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the number of comments saved
 * @global null $db
 * @return int
 */
function comments_count_all()
{
    global $statements;
    
    $statements['comments_count']->execute();
    
    $result = $statements['comments_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the number of threads saved during month
 * @global null $db
 * @param type $year_month
 * @return int
 */
function threads_count_by_month($year_month)
{
    global $statements;
    
    $creation_date = $year_month."-%";
    
    $statements['threads_by_month_count']->bindParam(':creation_date', $creation_date);
    $statements['threads_by_month_count']->execute();
    
    $result = $statements['threads_by_month_count']->fetchAll(PDO::FETCH_ASSOC);
    // return $result[0]["nbTrd"];
    return $result;
}

/**
 * Returns the number of comments by month
 * @global null $db
 * @param type $year_month
 * @return int
 */
function comments_count_by_month($year_month)
{
    global $statements;
    
    $creation_date = $year_month."-%";
    
    $statements['comments_by_month_count']->bindParam(':creation_date', $creation_date);
    $statements['comments_by_month_count']->execute();
    
    $result = $statements['comments_by_month_count']->fetchAll(PDO::FETCH_ASSOC);
    // return $result[0]["nbCmt"];
    return $result;
}

/**
 * Returns the number of threads during a date interval
 * @global null $db
 * @param type $earlier
 * @param type $later
 * @return int
 */
function threads_count_by_date_interval($earlier, $later)
{
    global $statements;
    
    $statements['threads_by_interval_count']->bindParam(':earlier', $earlier);
    $statements['threads_by_interval_count']->bindParam(':later', $later);
    $statements['threads_by_interval_count']->execute();
    
    $result = $statements['threads_by_interval_count']->fetchAll(PDO::FETCH_ASSOC);
    // return $result[0]["nbTrd"];
    return $result;
}

/**
 * Returns the number of comments by date interval
 * @global null $db
 * @param type $earlier
 * @param type $later
 * @return int
 */
function comments_count_by_date_interval($earlier, $later)
{
    global $statements;
    
    $statements['comments_by_interval_count']->bindParam(':earlier', $earlier);
    $statements['comments_by_interval_count']->bindParam(':later', $later);
    $statements['comments_by_interval_count']->execute();
    
    $result = $statements['comments_by_interval_count']->fetchAll(PDO::FETCH_ASSOC);
    // return $result[0]["nbCmt"];
    return $result;
}

/**
 * Returns the number of comments by album and date interval
 * @global null $db
 * @param type $albumName
 * @param type $earlier
 * @param type $later
 * @return int
 */
function comments_count_by_album_and_date_interval($albumName, $earlier, $later)
{
    global $statements;
    
    $statements['comments_by_album_by_interval_count']->bindParam(':album_name', $albumName);
    $statements['comments_by_album_by_interval_count']->bindParam(':earlier', $earlier);
    $statements['comments_by_album_by_interval_count']->bindParam(':later', $later);
    $statements['comments_by_album_by_interval_count']->execute();
    
    $result = $statements['comments_by_album_by_interval_count']->fetchAll(PDO::FETCH_COLUMN);
    return $result[0];
}

/**
 * Returns the oldest date in the database
 * @global null $db
 * @return date
 */
function date_select_oldest()
{
    global $statements;
    
    $statements['thread_oldest_get']->execute();
    
    $result = $statements['thread_oldest_get']->fetchAll();
    return $result[0]['minDate'];
}

/**
 * Returns the newest date
 * @global null $db
 * @return type
 */
function date_select_newest()
{
    global $statements;
    
    $statements['thread_newest_get']->execute();
    
    $result = $statements['thread_newest_get']->fetchAll();
    return $result[0]['maxDate'];
}

/**
 * Returns an array with all assets and the corresponding thread count
 * @global null $db
 * @return array
 */
function threads_count_all_by_asset()
{
    global $statements;
    
    $statements['threads_by_asset_count']->execute();
    
    $result = $statements['threads_by_asset_count']->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
