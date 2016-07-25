<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
*            Detobel Rémy <rdetobel@ulb.ac.be>
* 	     Arnaud Wijns <awijns@ulb.ac.be>
*            Antoine Dewilde
*            Thibaut Roskam
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



require_once '../commons/lib_database.php';

if(file_exists('config.inc')) {
    include_once 'config.inc';

    db_prepare(event_statements_get());
}
 

function event_statements_get() {
    return array(
            'get_all_event' =>
                    'SELECT * ' .
                    'FROM ' . db_gettable('events'). ' ' .
                    'ORDER BY event_time',
        
            'get_event' =>
                    'SELECT * ' .
                    'FROM ' . db_gettable('events'). ' ' .
                    'WHERE (:asset = "" OR asset = :asset) AND '
                        . '(:origin = "" OR origin = :origin) AND '
                        . '(:asset_classroom_id = "" OR asset_classroom_id = :asset_classroom_id) AND '
                        . '(:asset_course = "" OR asset_course = :asset_course) AND '
                        . '(:asset_author = "" OR asset_author = :asset_author) AND '
                        . '(:first_event_time = 0 OR event_time >= :first_event_time) AND ' 
                        . '(:last_event_time = 0 OR event_time <= :last_event_time) AND '
                        . '(:context = "" OR context = :context) AND '
                        . '(:loglevel = "" OR loglevel = :loglevel) AND '
                        . '(:message = "" OR message = :message) ' .
                    'ORDER BY event_time'
        );
}

/**
 * Return all the event list (without event status)
 * @global array $statements
 */
function db_event_get_all() {
    global $statements;

    $statements['get_all_event']->execute();
    return $statements['get_all_event']->fetchAll();
}

function db_event_get($asset, $origin, $asset_classroom_id, $asset_course, $asset_author,
        $first_event_time, $last_event_time, $context,
        $loglevel, $message) {
    global $statements;
    
    $statements['get_event']->bindParam(':asset', $asset);
    $statements['get_event']->bindParam(':origin', $origin);
    $statements['get_event']->bindParam(':asset_classroom_id', $asset_classroom_id);
    $statements['get_event']->bindParam(':asset_course', $asset_course);
    $statements['get_event']->bindParam(':asset_author', $asset_author);
    $statements['get_event']->bindParam(':first_event_time', $first_event_time);
    $statements['get_event']->bindParam(':last_event_time', $last_event_time);
    $statements['get_event']->bindParam(':context', $context);
    $statements['get_event']->bindParam(':loglevel', $loglevel);
    $statements['get_event']->bindParam(':message', $message);
    
    $statements['get_event']->execute();
    
    return $statements['get_event']->fetchAll();
}


