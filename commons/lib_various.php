<?php

/*
* EZCAST Commons 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
 *                  Carlos Avimadjessi
*
* This library is free software; you can redistribute it and/or
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
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Parse a JSON file
 * @param String $json_file_path
 * @return an array with the json file's content
 */

function json_to_array($json_file_path){
    $result_array = array();
    
    if (!file_exists($json_file_path)) return false;
    
    $json_file_content = file_get_contents($json_file_path);
    $json_objects = json_decode($json_file_content, TRUE);
    $json_iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($json_objects), RecursiveIteratorIterator::SELF_FIRST);
    $i = -1;
    foreach ($json_iterator as $key => $value){
        if(is_array($value)){
            $i++;
            $result_array[$i] = array();
        }  else {
            $result_array[$i][$key] = $value;
        }
        
    }
    
    return $result_array;
}
?>

