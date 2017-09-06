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
 * @package ezcast.ezadmin.lib.statistiques
 */
require_once 'lib_sql_stats.php';

##### STATS FUNCTIONS #######################################################

/**
 * Returns an array with all albums
 * @return type
 */
function stat_album_get_all($colOrder = "", $orderSort = "")
{
    return album_get_all($colOrder, $orderSort);
}

/**
 * Returns the thread count of an album
 * @param string $albumName
 * @return numeric
 */
function stat_threads_count_by_album($albumName)
{
    return threads_count_by_album($albumName);
}

### M O N T H

/**
 * Returns the thread count of an album during a specified month
 * @param string $albumName
 * @param string $currentMonth
 * @return numeric
 */
function stat_threads_count_by_album_and_month($albumName, $currentMonth)
{
    return threads_count_by_album_and_month($albumName, $currentMonth);
}
/**
 * Returns the comment count of an album during a specified month
 * @param string $albumName
 * @param string $currentMonth
 * @return numeric
 */
function stat_comments_count_by_album_and_month($albumName, $currentMonth)
{
    return comments_count_by_album_and_month($albumName, $currentMonth);
}

### D A T E   I N T E R V A L

/**
 * Returns the thread count of an album between 2 dates
 * @param string $albumName
 * @param date $earlier
 * @param date $later
 * @return numeric
 */
function stat_threads_count_by_album_and_date_interval($albumName, $earlier, $later)
{
    return threads_count_by_album_and_date_interval($albumName, $earlier, $later);
}

/**
 * Returns the comments count of an album between 2 date
 * @param string $albumName
 * @param date $earlier
 * @param date $later
 * @return numeric
 */
function stat_comments_count_by_album_and_date_interval($albumName, $earlier, $later)
{
    return comments_count_by_album_and_date_interval($albumName, $earlier, $later);
}

/**
 * Return the number of comments by album
 * @param type $albumName
 * @return numeric
 */
function stat_comments_count_by_album($albumName)
{
    return comments_count_by_album($albumName);
}

/**
 * Returns the number of threads
 * @return numeric number of theads
 */
function stat_threads_count_all()
{
    return threads_count_all();
}

/**
 * Returns the number of comments
 * @return numeric
 */
function stat_get_nb_comments()
{
    return comments_count_all();
}

/**
 * Sets a month's stats
 * @return boolean
 */
//function stat_get_by_month() {
//    VIEW /controller/stat_get_by_month
//}

/**
 * Sets the stats from the last n days
 * @global array $input
 * @return boolean
 */
//function stat_get_by_nDays() {
//    VIEW /controller/stat_get_by_nDays
//}

/**
 * Makes download the csv file with the stats grouped by asset
 */
//function stat_csv_by_asset() {
//    VIEW /controller/stat_csv_by_asset
//}

/**
 * Returns the oldest date saved
 * @return datetime
 */
function get_oldest_date()
{
    return date_select_oldest();
}

/**
 * Returns the rewest date saved
 * @return datetime
 */
function get_newest_date()
{
    return date_select_newest();
}

/**
 * Returns an array with threads and comments count grouped by asset
 * @return array threads count
 */
function stat_threads_count_all_by_asset()
{
    return threads_count_all_by_asset();
}
