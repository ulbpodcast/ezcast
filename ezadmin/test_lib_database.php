<?php
/**
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
 *
 * @package ezcast.ezadmin.test
 */

require_once '../commons/lib_database.php';

$course_name = "COURSE_MNEMO";
$user_name = "netid";

echo 'Preparing DB ...';
var_dump(db_prepare());

echo 'Testing courses_search ...'.PHP_EOL;
db_courses_search_compact('%', '%', true, true, true, true);
echo "All users of $course_name ...".PHP_EOL;
var_dump(db_courses_search_compact($course_name, '%', true, true, false, true));
echo "All courses by $user_name ...".PHP_EOL;
var_dump(db_courses_search_compact('%', $user_name, true, true, false, true));
echo "All users of $course_name, step 2 ...".PHP_EOL;
var_dump(db_course_get_users($course_name));
