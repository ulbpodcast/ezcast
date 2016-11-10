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

/*
 * This script is called every night to count the number of albums in ezmanager and update the DB accordingly
 */

require_once 'config.inc';
require_once '../commons/lib_database.php';

$dircontent = scandir($repository_path);

foreach($dircontent as $album) {
    if($album == '.' || $album == '..')
        continue;

    if($course_code_str = strstr($album, '-pub', true)) {
        $course_code = $course_code_str;
        $updated_courses[] = $course_code;
    }
    else if(($course_code_str = strstr($album, '-priv', true)) && !in_array($course_code_str, $updated_courses)) {
        $course_code = $course_code_str;
        $updated_courses[] = $course_code;
    }
}

db_prepare();
db_courses_update_hasalbums($dircontent);
db_close();
?>
